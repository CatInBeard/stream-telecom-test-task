<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreAdditionalVisitInfoRequest;
use App\Services\VisitDataService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ShortlLinkDataCollectController
{
    public function __construct(private VisitDataService $visitDataService)
    {
    }

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
}
