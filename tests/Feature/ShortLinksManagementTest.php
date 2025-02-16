<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ShortLink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class ShortLinksManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;
    protected $admin;
    protected $tokenAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = JWTAuth::fromUser($this->user);

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->tokenAdmin = JWTAuth::fromUser($this->admin);
    }

    public function test_create_short_link()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson(route('short-links.store'), [
            'url' => 'http://example.com',
            'use_js_redirect' => false,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('short_links', [
            'link' => 'http://example.com?utm_source=' . config('app.name'),
        ]);
    }

    public function test_create_short_link_not_authenticated()
    {
        $response = $this->postJson(route('short-links.store'), [
            'url' => 'http://example.com',
            'use_js_redirect' => false,
        ]);

        $response->assertStatus(401);
    }

    public function test_get_list_of_short_links()
    {
        ShortLink::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson(route('short-links.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'current_page',
            'data' => [
                [
                    'id',
                    'link',
                    'use_js_redirect',
                    'user_id',
                    'short_link',
                    'deleted_at',
                    'created_at',
                    'updated_at',
                ]
            ],
            'per_page',
            'to',
            'total',
        ]);
    }

    public function test_get_list_of_short_links_not_authenticated()
    {
        $response = $this->getJson(route('short-links.index'));

        $response->assertStatus(401);
    }

    public function test_get_short_link_by_id()
    {
        $shortLink = ShortLink::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson(route('short-links.show', ['short_link' =>$shortLink->id]));

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $shortLink->id,
            'link' => $shortLink->link,
            'use_js_redirect' => $shortLink->use_js_redirect,
            'user_id' => $shortLink->user_id,
            'short_link' => $shortLink->short_link,
        ]);
    }

    public function test_get_short_link_by_id_not_authenticated()
    {
        $shortLink = ShortLink::factory()->create();

        $response = $this->getJson(route('short-links.show', ['short_link' =>$shortLink->id]));

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $shortLink->id,
            'link' => $shortLink->link,
            'use_js_redirect' => $shortLink->use_js_redirect,
            'user_id' => $shortLink->user_id,
            'short_link' => $shortLink->short_link,
        ]);
    }

    public function test_update_short_link()
    {
        $shortLink = ShortLink::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson(route('short-links.update', ['short_link' => $shortLink->id]), [
            'url' => 'https://example.com',
            'use_js_redirect' => true,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('short_links', [
            'id' => $shortLink->id,
            'link' => 'https://example.com?utm_source=' . config('app.name'),
            'use_js_redirect' => true,
        ]);
    }

    public function test_update_short_link_not_authenticated()
    {
        $shortLink = ShortLink::factory()->create();

        $response = $this->putJson(route('short-links.update', ['short_link' => $shortLink->id]), [
            'url' => 'http://updated-example.com',
            'use_js_redirect' => true,
        ]);

        $response->assertStatus(401);
    }

    public function test_delete_short_link()
    {
        $shortLink = ShortLink::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson(route('short-links.destroy', ['short_link' =>$shortLink->id]));

        $response->assertStatus(204);
        $this->assertDatabaseMissing('short_links', [
            'id' => $shortLink->id,
            'deleted_at' => null
        ]);
    }

    public function test_delete_short_link_not_authenticated()
    {
        $shortLink = ShortLink::factory()->create();

        $response = $this->deleteJson(route('short-links.destroy', ['short_link' =>$shortLink->id]));

        $response->assertStatus(401);
    }

    public function test_delete_short_link_not_found()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson(route('short-links.destroy', ['short_link' =>999]));

        $response->assertStatus(404);
    }
}
