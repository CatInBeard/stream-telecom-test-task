<?php

namespace Tests\Unit;

use App\Exceptions\ErrorJsonException;
use App\Models\ShortLink;
use App\Models\User;
use App\Services\ShortLinkService;
use App\Services\UTMService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ShortLinkServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ShortLinkService $shortLinkService;
    protected UTMService $utmService;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->utmService = $this->createMock(UTMService::class);
        $this->shortLinkService = new ShortLinkService($this->utmService);
        $this->user = User::factory()->create();
    }

    public function testCreateShortLinkAddsUtmIfNotPresent()
    {
        $url = 'http://example.com';
        $this->utmService->method('checkUtm')->willReturn(false);
        $this->utmService->method('addUtm')->willReturn($url . '?utm_source=app_name');

        Auth::shouldReceive('id')->andReturn($this->user->id);

        $shortLink = $this->shortLinkService->createShortLink($url);

        $this->assertInstanceOf(ShortLink::class, $shortLink);
        $this->assertEquals($url . '?utm_source=app_name', $shortLink->link);
        $this->assertEquals(1, $shortLink->user_id);
    }

    public function testGetShortLinksReturnsPaginatedLinks()
    {
        ShortLink::factory()->count(15)->create(['user_id' => $this->user->id]);

        Auth::shouldReceive('user')->andReturn($this->user);

        $links = $this->shortLinkService->getShortLinks(10, 1);

        $this->assertCount(10, $links);
        $this->assertEquals(2, $links->lastPage());
    }

    public function testGetShortLinkByIdReturnsLink()
    {
        $shortLink = ShortLink::factory()->create();

        $result = $this->shortLinkService->getShortLinkById($shortLink->id);

        $this->assertEquals($shortLink->id, $result->id);
    }

    public function testGetLinkByShortLinkReturnsLink()
    {
        $shortLink = ShortLink::factory()->create();

        $result = $this->shortLinkService->getShortLinkByLink(base_convert($shortLink->id, 10, 36));

        $this->assertEquals($shortLink->id, $result->id);
    }

    public function testGetShortLinkByLinkThrowsExceptionIfNotFound()
    {
        $this->expectException(ErrorJsonException::class);

        $this->shortLinkService->getShortLinkByLink('nel');
    }

    public function testUpdateShortLink()
    {
        $shortLink = ShortLink::factory()->create(['user_id' => $this->user->id]);
        Auth::shouldReceive('user')->andReturn($this->user);

        $updatedLink = $this->shortLinkService->updateShortLink($shortLink->id, 'http://new-url.com', true);

        $this->assertEquals('http://new-url.com', $updatedLink->link);
        $this->assertTrue($updatedLink->use_js_redirect);
    }

    public function testDeleteShortLinkDeletesLink()
    {
        $shortLink = ShortLink::factory()->create(['user_id' => $this->user->id]);
        Auth::shouldReceive('user')->andReturn($this->user);

        $result = $this->shortLinkService->deleteShortLink($shortLink->id);

        $this->assertNull(ShortLink::find($shortLink->id));
    }

}
