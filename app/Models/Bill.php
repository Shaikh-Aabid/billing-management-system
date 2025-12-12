<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'party_id',
        'bill_number',
        'cancelled_bill_number',
        'bill_date',
        'bill_type',
        'subtotal',
        'cgst',
        'sgst',
        'igst',
        'gst_amount',
        'total_amount',
        'is_inter_state',
        'notes',
        'status',
    ];

    protected $casts = [
        'bill_date' => 'date',
        'subtotal' => 'decimal:2',
        'cgst' => 'decimal:2',
        'sgst' => 'decimal:2',
        'igst' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'is_inter_state' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bill) {
            if (empty($bill->bill_number)) {
                $bill->bill_number = self::generateBillNumber($bill->user_id);
            }
        });
    }

    public static function generateBillNumber(int $userId): string
    {
        $user = User::find($userId);
        $prefix = $user->getSetting('bill_prefix', 'INV');
        $year = date('Y');
        $month = date('m');

        // First, check if there's a cancelled bill whose number can be reused
        $cancelledBill = self::where('user_id', $userId)
            ->where('status', 'cancelled')
            ->whereNotNull('bill_number')
            ->whereYear('created_at', $year)
            ->orderByRaw('CAST(substr(bill_number, -4) AS INTEGER) ASC')
            ->first();

        if ($cancelledBill) {
            // Reuse the cancelled bill's number
            $reusedNumber = $cancelledBill->bill_number;
            // Save original number to cancelled_bill_number and clear bill_number
            $cancelledBill->update([
                'cancelled_bill_number' => $reusedNumber,
                'bill_number' => null,
            ]);
            return $reusedNumber;
        }

        // No cancelled bill to reuse, generate next sequential number
        $lastBill = self::where('user_id', $userId)
            ->whereYear('created_at', $year)
            ->whereNotNull('bill_number')
            ->orderByRaw('CAST(substr(bill_number, -4) AS INTEGER) DESC')
            ->first();

        $sequence = $lastBill ? intval(substr($lastBill->bill_number, -4)) + 1 : 1;

        return sprintf('%s/%s%s/%04d', $prefix, $year, $month, $sequence);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BillItem::class);
    }

    public function ewayBill(): HasOne
    {
        return $this->hasOne(EwayBill::class);
    }

    public function calculateTotals(): void
    {
        $subtotal = 0;
        $cgst = 0;
        $sgst = 0;
        $igst = 0;

        foreach ($this->items as $item) {
            $itemSubtotal = $item->quantity * $item->price;
            $itemGst = $itemSubtotal * ($item->gst_rate / 100);

            $subtotal += $itemSubtotal;

            if ($this->is_inter_state) {
                $igst += $itemGst;
            } else {
                $cgst += $itemGst / 2;
                $sgst += $itemGst / 2;
            }
        }

        $this->subtotal = $subtotal;
        $this->cgst = $cgst;
        $this->sgst = $sgst;
        $this->igst = $igst;
        $this->gst_amount = $cgst + $sgst + $igst;
        $this->total_amount = $subtotal + $this->gst_amount;
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('bill_number', 'like', "%{$search}%")
              ->orWhereHas('party', function ($q2) use ($search) {
                  $q2->where('name', 'like', "%{$search}%");
              });
        });
    }

    public function scopeDateRange($query, $from, $to)
    {
        if ($from) {
            $query->whereDate('bill_date', '>=', $from);
        }
        if ($to) {
            $query->whereDate('bill_date', '<=', $to);
        }
        return $query;
    }
}
