<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LandedCost extends Model
{
    protected $fillable = [
        'name', 'date', 'amount_total', 'state', 'journal_id',
        'company_id', 'description',
    ];

    protected $casts = [
        'date' => 'date',
        'amount_total' => 'decimal:2',
    ];

    public function lines(): HasMany { return $this->hasMany(LandedCostLine::class, 'landed_cost_id'); }
    public function journal(): BelongsTo { return $this->belongsTo(Journal::class); }
}
