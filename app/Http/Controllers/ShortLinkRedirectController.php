<?php

namespace App\Http\Controllers;

use App\Exceptions\ErrorJsonException;
use App\Services\ShortLinkService;
use App\Services\VisitDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class ShortLinkRedirectController extends Controller
{

    public function __construct(private ShortLinkService $shortLinkService, private VisitDataService $visitDataService)
    {
    }

    public function redirect(Request $request, $link)
    {

        try {
            $link = $this->shortLinkService->getShortLinkByLink($link);
        } catch (ErrorJsonException $e) {
            abort(404);
        }

        $data = $this->visitDataService->createPrimaryData(
            $link,
            $request->header('User-Agent'),
            $request->ip(),
            $request->cookie('user_uuid', null)
        );

        Cookie::queue('user_uuid', $data->session_uuid, 360000);

        if($link->use_js_redirect){
            return view('js_redirect', ['redirectUrl' => $link->link, 'visitId' => $data->id]);
        }

        return redirect($link->link);

    }
}
