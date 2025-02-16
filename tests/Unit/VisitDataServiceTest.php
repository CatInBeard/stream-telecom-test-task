<?php

namespace Tests\Unit;

use App\Models\AdditionalVisitInfo;
use App\Models\ShortLink;
use App\Models\VisitUserData;
use App\Services\VisitDataService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class VisitDataServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testCreatePrimaryVisitData()
    {

        $shortLink = ShortLink::factory()->create();
        $userAgent = 'Test User Agent';
        $ipAddress = '127.0.0.1';
        $sessionUUID = Str::uuid();

        $service = new VisitDataService();

        $visitData = $service->createPrimaryData(
            $shortLink,
            $userAgent,
            $ipAddress,
            $sessionUUID
        );

        $this->assertInstanceOf(VisitUserData::class, $visitData);
        $this->assertDatabaseHas('visit_user_data', [
            'id' => $visitData->id,
            'short_link_id' => $shortLink->id,
            'user_agent' => $userAgent,
            'ip_address' => $ipAddress,
            'session_uuid' => $sessionUUID,
        ]);
    }

    public function testCreatePrimaryVisitDataWithNewSessionUuid()
    {
        $shortLink = ShortLink::factory()->create();
        $userAgent = 'Test User Agent';
        $ipAddress = '127.0.0.1';

        $service = new VisitDataService();

        $visitData = $service->createPrimaryData(
            $shortLink,
            $userAgent,
            $ipAddress
        );

        $this->assertInstanceOf(VisitUserData::class, $visitData);
        $this->assertDatabaseHas('visit_user_data', [
            'id' => $visitData->id,
            'short_link_id' => $shortLink->id,
            'user_agent' => $userAgent,
            'ip_address' => $ipAddress,
            'session_uuid' => $visitData->session_uuid,
        ]);
        $this->assertNotNull($visitData->session_uuid);
    }

    public function testSaveAdditionalVisitData()
    {

        $link = ShortLink::factory()->create();
        $visitData = VisitUserData::factory()->create(['short_link_id' => $link->id]);


        $additionalData = [
            'language' => 'en-US',
            'screenResolution' => '1920x1080',
        ];

        $service = new VisitDataService();

        $additionalVisitInfo = $service->saveAdditionalData($additionalData, $visitData->id);

        $this->assertInstanceOf(AdditionalVisitInfo::class, $additionalVisitInfo);
        $this->assertDatabaseHas('additional_visit_infos', [
            'language' => 'en-US',
            'screenResolution' => '1920x1080',
        ]);
    }

    public function testGetFullDataByShortLink()
    {

        $shortLink = ShortLink::factory()->create();
        VisitUserData::factory()->count(3)->create(['short_link_id' => $shortLink->id]);
        $visitUserData = VisitUserData::where('short_link_id', $shortLink->id)->first();

        AdditionalVisitInfo::factory()->create(['visit_user_data_id' => $visitUserData->id]);

        $service = new VisitDataService();

        $result = $service->getFullDataByShortLink($shortLink->id);

        $this->assertCount(3, $result->items());
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $result);

        $item = $result->items()[0];
        $this->assertArrayHasKey('additional_visit_infos', $item->toArray());
    }

}
