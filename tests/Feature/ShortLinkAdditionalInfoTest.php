<?php

namespace Tests\Feature;

use App\Models\ShortLink;
use App\Models\User;
use App\Models\VisitUserData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ShortLinkAdditionalInfoTest extends TestCase
{

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = JWTAuth::fromUser($this->user);

    }


    public function test_get_list_of_links_visitors()
    {

        $shortLink = ShortLink::factory()->create(['user_id' => $this->user->id]);

        VisitUserData::factory()->create(['short_link_id' => $shortLink->id, 'user_agent' => 'Mozilla/5.0', 'ip_address' => '192.168.1.1']);
        VisitUserData::factory()->create(['short_link_id' => $shortLink->id, 'user_agent' => 'Mozilla/5.0', 'ip_address' => '192.168.1.2']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson(route('short-links-additional-data.index', ['id' => $shortLink->id]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'current_page',
            'data' => [
                [
                    'id',
                    'short_link_id',
                    'user_agent',
                    'ip_address',
                    'session_uuid',
                ]
            ],
            'per_page',
            'to',
            'total',
        ]);
    }

    public function test_get_list_of_links_visitors_not_authenticated()
    {
        $shortLink = ShortLink::factory()->create();

        $response = $this->getJson(route('short-links-additional-data.index', ['id' => $shortLink->id]));

        $response->assertStatus(401);
    }

    public function test_save_data_from_js()
    {
        $shortLink = ShortLink::factory()->create(['user_id' => $this->user->id]);

        $response = $this->postJson(route('short-links-additional-data.index', ['id' => $shortLink->id]), [
            'userAgent' => 'Mozilla/5.0',
            'language' => 'en-US',
            'platform' => 'Win32',
            'screenResolution' => '1920x1080',
            'colorDepth' => 24,
            'timezone' => 'America/New_York',
            'plugins' => [],
            'cookiesEnabled' => true,
            'hardwareConcurrency' => 4,
            'onlineStatus' => true,
            'viewportSize' => '1920x1080',
            'canvasFingerprint' => 'fingerprint-string',
            'installedFonts' => [],
            'browserName' => 'Chrome',
            'browserVersion' => '93.0.4577.63',
            'windowSize' => [],
            'localStorageAvailable' => true,
            'sessionStorageAvailable' => true,
            'cssProperties' => [],
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Data successfully saved',
        ]);
    }

    public function test_save_data_from_js_invalid_data()
    {
        $shortLink = ShortLink::factory()->create(['user_id' => $this->user->id]);

        $response = $this->postJson(route('short-links-additional-data.index', ['id' => $shortLink->id]), []);

        $response->assertStatus(201);
    }

    public function test_save_data_from_js_with_throttle()
    {
        $shortLink = ShortLink::factory()->create(['user_id' => $this->user->id]);

        $limit = 5;

        for ($i = 0; $i < $limit; $i++) {
            $response = $this->postJson(route('short-links-additional-data.index', ['id' => $shortLink->id]), [
                'userAgent' => 'Mozilla/5.0',
                'language' => 'en-US',
                'platform' => 'Win32',
                'screenResolution' => '1920x1080',
                'colorDepth' => 24,
                'timezone' => 'America/New_York',
                'plugins' => [],
                'cookiesEnabled' => true,
                'hardwareConcurrency' => 4,
                'onlineStatus' => true,
                'viewportSize' => '1920x1080',
                'canvasFingerprint' => 'fingerprint-string',
                'installedFonts' => [],
                'browserName' => 'Chrome',
                'browserVersion' => '93.0.4577.63',
                'windowSize' => [],
                'localStorageAvailable' => true,
                'sessionStorageAvailable' => true,
                'cssProperties' => [],
            ]);

            $response->assertStatus(201);
            $response->assertJson([
                'message' => 'Data successfully saved',
            ]);
        }

        $response = $this->postJson(route('short-links-additional-data.index', ['id' => $shortLink->id]), [
            'userAgent' => 'Mozilla/5.0',
            'language' => 'en-US',
            'platform' => 'Win32',
            'screenResolution' => '1920x1080',
            'colorDepth' => 24,
            'timezone' => 'America/New_York',
            'plugins' => [],
            'cookiesEnabled' => true,
            'hardwareConcurrency' => 4,
            'onlineStatus' => true,
            'viewportSize' => '1920x1080',
            'canvasFingerprint' => 'fingerprint-string',
            'installedFonts' => [],
            'browserName' => 'Chrome',
            'browserVersion' => '93.0.4577.63',
            'windowSize' => [],
            'localStorageAvailable' => true,
            'sessionStorageAvailable' => true,
            'cssProperties' => [],
        ]);

        $response->assertStatus(429);
    }




}
