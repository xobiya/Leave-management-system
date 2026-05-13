<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseAgreement extends Model
{
    protected $fillable = [
        'name', 'vendor_id', 'start_date', 'end_date',
        'total_amount', 'status', 'terms', 'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function vendor(): BelongsTo { return $this->belongsTo(Vendor::class); }
    public function lines(): HasMany { return $this->hasMany(PurchaseAgreementLine::class, 'purchase_agreement_id'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
