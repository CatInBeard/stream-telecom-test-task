<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\CustomJsonException;
use App\Exceptions\ErrorJsonException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateShortLinkRequest;
use App\Http\Requests\GetShortLinksRequest;
use App\Http\Requests\UpdateShortLinkRequest;
use App\Models\ShortLink;
use App\Services\LinkValidationService;
use App\Services\ShortLinkService;
use Illuminate\Http\Request;

class ShortLinkController extends Controller
{

    /**
     * @group Short links Management
     *
     * APIs for managing short links
     */

    public function __construct(private ShortLinkService $shortLinkService, private LinkValidationService $linkValidationService)
    {
    }

    /**
     *
     * Get list of short links
     *
     * @header Authorization Bearer token
     *
     * @response 200 {
     * "current_page" : 1
     * "data" => [
     *    {
     *      "id": 1,
     *      "link": "https://example.com",
     *      "use_js_redirect": "false",
     *      "user_id": 1,
     *      "short_link": https://example.com/a3ew
     *    },
     *    {
     *      "id": 2,
     *      "link": "https://example.com/2",
     *      "use_js_redirect": "false"
     *      "user_id": 22,
     *      "short_link": https://example.com/b3ew
     *    }
     *  ]
     * "per_page" : 10,
     * "to" : 1,
     * "total" : 1
     * }
     *
     * @response 401 {
     *   "error": "Unauthenticated"
     * }
     * @response 403 {
     *   "error": "Unauthorized"
     * }
     */
    public function index(GetShortLinksRequest $request): \Illuminate\Http\JsonResponse
    {

        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $links = $this->shortLinkService->getShortLinks($perPage, $page);

        return response()->json($links);
    }

    /**
     *
     * Create new short link
     *
     * @header Authorization Bearer token
     *
     * @response 201 {
     * "id": 1,
     * "link": "https://example.com",
     * "use_js_redirect": "false",
     * "user_id": 1,
     * "short_link": https://example.com/a3ew
     * }
     *
     *
     * @response 401 {
     * "error": "Unauthenticated"
     * }
     * @response 403 {
     * "error": "Unauthorized"
     * }
     *
     *
     * @throws ErrorJsonException
     */
    public function store(CreateShortLinkRequest $request): \Illuminate\Http\JsonResponse
    {
        $url = $request->input('url');
        $use_js_redirect = (bool) $request->input('use_js_redirect');
        $this->linkValidationService->validateLink($url);
        $link = $this->shortLinkService->createShortLink($url, $use_js_redirect);
        return response()->json($link, 201);
    }

    /**
     *
     * Get short link by id
     *
     * @header Authorization Bearer token
     *
     * @response 200 {
     * "id": 1,
     * "link": "https://example.com",
     * "use_js_redirect": "false",
     * "user_id": 1,
     * "short_link": https://example.com/a3ew
     * }
     *
     *
     * @response 401 {
     * "error": "Unauthenticated"
     * }
     * @response 403 {
     * "error": "Unauthorized"
     * }
     *
     * @response 404 {
     * "error": "Link not found"
     * }
     *
     */
    public function show($id): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->shortLinkService->getShortLinkById($id));
    }

    /**
     * /**
     *
     * Update short link by id
     *
     * @header Authorization Bearer token
     *
     * @response 200 {
     * "id": 1,
     * "link": "https://example.com",
     * "use_js_redirect": "false",
     * "user_id": 1,
     * "short_link": https://example.com/a3ew
     * }
     *
     *
     * @response 401 {
     * "error": "Unauthenticated"
     * }
     * @response 403 {
     * "error": "Unauthorized"
     * }
     *
     * @response 404 {
     * "error": "Link not found"
     * }
     *
     * @throws ErrorJsonException
     */
    public function update(UpdateShortLinkRequest $request, $id): \Illuminate\Http\JsonResponse
    {
        $url = $request->input('url');
        $useJsRedirect = $request->input('use_js_redirect', false);
        $this->linkValidationService->validateLink($url);
        $link = $this->shortLinkService->updateShortLink($id, $url, $useJsRedirect);

        return response()->json($link);
    }

    /**
     *
     * Create new short link
     *
     * @header Authorization Bearer token
     *
     * @response 204 {
     * "message": "Link deleted successfully",
     * }
     *
     *
     * @response 401 {
     * "error": "Unauthenticated"
     * }
     * @response 403 {
     * "error": "Unauthorized"
     * }
     *
     * @response 404 {
     *  "error": "Link not found"
     * }
     *
     * @throws ErrorJsonException
     */
    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $this->shortLinkService->deleteShortLink($id);
        return response()->json(["message" => "Link deleted successfully"], 204);
    }
}
