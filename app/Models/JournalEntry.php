<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalEntry extends Model
{
    use HasFactory;

    protected $fillable = ['journal_id', 'code', 'date', 'reference', 'state'];

    protected $casts = ['date' => 'date', 'state' => 'string'];

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(JournalItem::class);
    }
}
