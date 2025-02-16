<?php

namespace App\Models;

use Database\Factories\ShortLinkFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShortLink extends Model
{
    /** @use HasFactory<ShortLinkFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'link',
        'use_js_redirect',
        'user_id',
    ];

    protected $appends = ['short_link'];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getShortLinkAttribute()
    {
        return route('redirect-link.show', ['link' => base_convert((string) $this->id, 10, 36)]);
    }

    public function getShortLinkPart()
    {
        return base_convert((string) $this->id, 10, 36);
    }

    public static function getByShortLink(string $shortLink)
    {
        return self::where('id', base_convert($shortLink, 36, 10))->first();
    }

    public function visitUserData()
    {
        return $this->hasMany(VisitUserData::class);
    }
}
