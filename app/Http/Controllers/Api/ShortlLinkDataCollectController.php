<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\GetShortLinksStatisticsRequest;
use App\Http\Requests\StoreAdditionalVisitInfoRequest;
use App\Services\VisitDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ShortlLinkDataCollectController
{
    public function __construct(private VisitDataService $visitDataService)
    {
    }

    /**
     * Save data from js. Always return OK
     *
     *
     * @response 201 {
     *  "message": "Data successfully saved",
     * }
     *
     *
     * @param StoreAdditionalVisitInfoRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function store(StoreAdditionalVisitInfoRequest $request, $id){
        try{
            $this->visitDataService->saveAdditionalData($request->validated(), $id);
        }
        catch (ValidationException $exception){
        }
        finally {
            return response()->json(["message" => "Data successfully saved"], 201);
        }

    }

    /**
     *
     *
     * Get list of links visitors
     *
     * @header Authorization Bearer token
     *
     * @response 200 {
     * "current_page" : 1
     * "data": [
     *      {
     *        "id": "uuid-1",
     *        "short_link_id": 1,
     *        "user_agent": "Mozilla/5.0 ...",
     *        "ip_address": "192.168.1.1",
     *        "session_uuid": "uuid-session-1",
     *        "latest_additional_info": {
     *        "id": 1,
     *        "created_at": "2023-10-01T12:00:00Z",
     *        "updated_at": "2023-10-01T12:00:00Z"
     *        }
     *      },
     *      {
     *        "id": "uuid-2",
     *        "short_link_id": 1,
     *        "user_agent": "Mozilla/5.0 ...",
     *        "ip_address": "192.168.1.2",
     *        "session_uuid": "uuid-session-2",
     *        "additional_info": null
     *      }
     *    ]
     * "per_page" : 10,
     * "to" : 1,
     * "total" : 1
     * }
     *
     * @response 401 {
     * "error": "Unauthenticated"
     * }
     * @response 403 {
     * "error": "Unauthorized"
     * }
     * /
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function index(GetShortLinksStatisticsRequest $request, $id): JsonResponse
    {

        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $data = $this->visitDataService->getFullDataByShortLink($id, $perPage, $page);

        $response = [
            'current_page' => $data->currentPage(),
            'total' => $data->total(),
            'data' => $data->items(),
            'per_page' => $data->perPage(),
            'from' => $data->firstItem(),
            'to' => $data->lastItem(),
        ];

        return response()->json($response);
    }
}
