<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisitUserData extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'short_link_id',
        'user_agent',
        'session_uuid',
        'ip_address'
    ];

    public function shortLink(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ShortLink::class);
    }

    public function additionalVisitInfos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AdditionalVisitInfo::class);
    }

    public function firstAdditionalVisitInfo(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AdditionalVisitInfo::class)->latest();
    }
}
