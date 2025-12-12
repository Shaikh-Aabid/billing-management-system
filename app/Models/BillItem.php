<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'product_id',
        'hsn_code',
        'quantity',
        'unit',
        'price',
        'gst_rate',
        'cgst',
        'sgst',
        'igst',
        'amount',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'price' => 'decimal:2',
        'gst_rate' => 'decimal:2',
        'cgst' => 'decimal:2',
        'sgst' => 'decimal:2',
        'igst' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function calculateAmount(bool $isInterState = false): void
    {
        $subtotal = $this->quantity * $this->price;
        $gstAmount = $subtotal * ($this->gst_rate / 100);

        if ($isInterState) {
            $this->igst = $gstAmount;
            $this->cgst = 0;
            $this->sgst = 0;
        } else {
            $this->cgst = $gstAmount / 2;
            $this->sgst = $gstAmount / 2;
            $this->igst = 0;
        }

        $this->amount = $subtotal + $gstAmount;
    }
}
