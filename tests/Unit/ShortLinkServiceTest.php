<?php

namespace Tests\Unit;

use App\Exceptions\ErrorJsonException;
use App\Models\ShortLink;
use App\Models\User;
use App\Services\ShortLinkService;
use App\Services\UTMService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
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

    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function testCreateShortLinkAddsUtmIfNotPresent()
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

    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function testGetShortLinksReturnsPaginatedLinks()
    {
        ShortLink::factory()->count(15)->create(['user_id' => $this->user->id]);

        Auth::shouldReceive('user')->andReturn($this->user);

        $links = $this->shortLinkService->getShortLinks(10, 1);

        $this->assertCount(10, $links);
        $this->assertEquals(2, $links->lastPage());
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function testGetShortLinkByIdReturnsLink()
    {
        $shortLink = ShortLink::factory()->create();

        $result = $this->shortLinkService->getShortLinkById($shortLink->id);

        $this->assertEquals($shortLink->id, $result->id);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function testGetLinkByShortLinkReturnsLink()
    {
        $shortLink = ShortLink::factory()->create();

        $result = $this->shortLinkService->getShortLinkByLink(base_convert($shortLink->id, 10, 36));

        $this->assertEquals($shortLink->id, $result->id);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function testGetShortLinkByLinkThrowsExceptionIfNotFound()
    {
        $this->expectException(ErrorJsonException::class);

        $this->shortLinkService->getShortLinkByLink('nel');
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function testUpdateShortLink()
    {
        $shortLink = ShortLink::factory()->create(['user_id' => $this->user->id]);
        Auth::shouldReceive('user')->andReturn($this->user);

        $this->utmService->method('checkUtm')->willReturn(true);

        $updatedLink = $this->shortLinkService->updateShortLink($shortLink->id, 'http://example.com/test', true);

        $this->assertEquals('http://example.com/test', $updatedLink->link);
        $this->assertTrue($updatedLink->use_js_redirect);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function testDeleteShortLinkDeletesLink()
    {
        $shortLink = ShortLink::factory()->create(['user_id' => $this->user->id]);
        Auth::shouldReceive('user')->andReturn($this->user);

        $result = $this->shortLinkService->deleteShortLink($shortLink->id);

        $this->assertNull(ShortLink::find($shortLink->id));
    }

}
