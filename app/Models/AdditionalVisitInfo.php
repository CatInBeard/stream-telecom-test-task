<?php

namespace App\Models;

use Database\Factories\AdditionalVisitInfoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdditionalVisitInfo extends Model
{
    /** @use HasFactory<AdditionalVisitInfoFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'userAgent',
        'language',
        'platform',
        'screenResolution',
        'colorDepth',
        'timezone',
        'plugins',
        'cookiesEnabled',
        'hardwareConcurrency',
        'onlineStatus',
        'viewportSize',
        'canvasFingerprint',
        'installedFonts',
        'browserName',
        'browserVersion',
        'windowSize',
        'localStorageAvailable',
        'sessionStorageAvailable',
        'cssProperties',
        'visit_user_data_id',
    ];

    protected $casts = [
        'plugins' => 'array',
        'installedFonts' => 'array',
        'windowSize' => 'array',
        'cssProperties' => 'array',
    ];

    public function visitUserData()
    {
        return $this->belongsTo(VisitUserData::class);
    }
}
