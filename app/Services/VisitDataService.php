<?php

namespace App\Services;

use App\Models\AdditionalVisitInfo;
use App\Models\ShortLink;
use App\Models\VisitUserData;
use Illuminate\Support\Str;

class VisitDataService
{
    public function createPrimaryData(
        ShortLink $shortLink,
        string $userAgent,
        string $ipAddress,
        ?string $sessionUUID = null
    )
    {
        return VisitUserData::create([
            'id' => (string) Str::uuid(),
            'short_link_id' => $shortLink->id,
            'user_agent' => $userAgent,
            'ip_address' => $ipAddress,
            'session_uuid' => $sessionUUID ?? (string) Str::uuid(),
        ]);
    }

    public function saveAdditionalData(array $additionalData, $visitId)
    {
        $additionalData['visit_user_data_id'] = $visitId;
        return AdditionalVisitInfo::create($additionalData);
    }

    public function getFullDataByShortLink($shortLinkId, $limit = 100, $page = 1)
    {
        $visitUserData = VisitUserData::where('short_link_id', $shortLinkId)
            ->with(['additionalVisitInfos' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(1);
            }])
            ->paginate($limit, ['*'], 'page', $page);

        return $visitUserData;
    }

}
