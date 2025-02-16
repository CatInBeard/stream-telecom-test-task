<?php

namespace App\Services;

use App\Exceptions\ErrorJsonException;
use App\Models\ShortLink;
use Illuminate\Support\Facades\Auth;

class ShortLinkService
{

    public function __construct(private UTMService $utmService)
    {
    }

    public function createShortLink(string $url, bool $useJsRedirect = false): ShortLink
    {
        if(!$this->utmService->checkUtm($url)){
            $url = $this->utmService->addUtm($url, [
                'utm_source' => config('app.name')
            ]);
        }

        $shortLink = new ShortLink();
        $shortLink->link = $url;
        $shortLink->use_js_redirect = $useJsRedirect;
        $shortLink->user_id = Auth::id();
        $shortLink->save();

        return $shortLink;
    }

    /**
     * @param int $perPage
     * @param int $page
     * @return mixed
     */
    public function getShortLinks(int $perPage = 10, int $page = 1)
    {
        return auth()->user()->shortLinks()->paginate($perPage, ['*'], 'page', $page);
    }


    /**
     * @param $id
     * @return mixed
     */
    public function getShortLinkById($id)
    {
        return ShortLink::findOrFail($id);
    }

    /**
     * @param $link
     * @return mixed
     * @throws ErrorJsonException
     */
    public function getShortLinkByLink($link)
    {
        $link = ShortLink::getByShortLink($link);
        if(!$link){
            throw new ErrorJsonException('Link not found', 404);
        }
        return $link;
    }

    public function updateShortLink(int $id, string $url, bool $useJsRedirect = false): ShortLink
    {
        $user = auth()->user();
        $link = ShortLink::findOrFail($id);
        if(!$this->checkDangerAccessToLink($user, $link)){
            throw new ErrorJsonException('Access denied', 403);
        }

        if(!$this->utmService->checkUtm($url)){
            $url = $this->utmService->addUtm($url, [
                'utm_source' => config('app.name')
            ]);
        }

        $link->link = $url;
        $link->use_js_redirect = $useJsRedirect;
        $link->save();

        return $link;
    }

    public function deleteShortLink($id)
    {
        $user = auth()->user();
        $link = ShortLink::findOrFail($id);
        if(!$this->checkDangerAccessToLink($user, $link)){
            throw new ErrorJsonException('Access denied', 403);
        }
        return ShortLink::destroy($id);
    }

    private function checkDangerAccessToLink($user, $link)
    {
        return $user->isAdmin() || $user->id === $link->user_id;
    }


}
