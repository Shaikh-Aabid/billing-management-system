<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class EwayBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'eway_bill_number',
        'transport_mode',
        'vehicle_number',
        'vehicle_type',
        'transporter_id',
        'transporter_name',
        'distance',
        'from_address',
        'from_pincode',
        'to_address',
        'to_pincode',
        'generated_at',
        'valid_until',
        'status',
    ];

    protected $casts = [
        'distance' => 'integer',
        'generated_at' => 'datetime',
        'valid_until' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ewayBill) {
            if (empty($ewayBill->eway_bill_number)) {
                $ewayBill->eway_bill_number = self::generateEwayBillNumber();
            }
            if (empty($ewayBill->generated_at)) {
                $ewayBill->generated_at = now();
            }
            if (empty($ewayBill->valid_until)) {
                $ewayBill->valid_until = self::calculateValidUntil($ewayBill->distance);
            }
        });
    }

    public static function generateEwayBillNumber(): string
    {
        $year = date('Y');
        $random = strtoupper(bin2hex(random_bytes(4)));
        $sequence = self::whereYear('created_at', $year)->count() + 1;

        return sprintf('EWB%s%08d%s', $year, $sequence, $random);
    }

    public static function calculateValidUntil(int $distance): Carbon
    {
        // E-Way bill validity based on distance (as per GST rules)
        // Up to 100 km: 1 day
        // Every 100 km or part thereof: additional 1 day
        // For ODC: 1 day per 20 km or part thereof
        $days = max(1, ceil($distance / 100));

        return now()->addDays($days);
    }

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function isExpired(): bool
    {
        return $this->valid_until < now();
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && !$this->isExpired();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('valid_until', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('valid_until', '<', now());
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('eway_bill_number', 'like', "%{$search}%")
              ->orWhere('vehicle_number', 'like', "%{$search}%");
        });
    }
}
