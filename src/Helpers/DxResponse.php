<?php

namespace Pkt\StarterKit\Helpers;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class DxResponse
{
    /**
     * Format response for DevExtreme data grid
     * 
     * @param LengthAwarePaginator|Builder|QueryBuilder $data
     * @param Request $request
     * 
     * @throws \Exception
     * @return \Illuminate\Http\JsonResponse
     */
    public static function json(LengthAwarePaginator|Builder|QueryBuilder $data, Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            if ($data instanceof Builder || $data instanceof QueryBuilder) {
                $data = $data->paginate($request->take ?? $data->count());
                $responseData = $data->items();
                $responseCount = $data->total();
            } else {
                $responseData = $data->items();
                $responseCount = $data->total();
            }

            return response()->json([
                'status' => true,
                'data' => $responseData,
                'totalCount' => $responseCount,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
