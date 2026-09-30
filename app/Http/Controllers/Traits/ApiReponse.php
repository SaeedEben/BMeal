<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\AbstractPaginator;

trait ApiResponse
{
    public static function success(
        mixed  $data = null,
        string $message = 'Success',
        int    $status = 200
    ) :JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }


    public static function error(
        string $message = 'Something went wrong',
        mixed  $errors = null,
        int    $status = 400
    ) :JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }


    public static function resource(
        JsonResource $resource,
        string       $message = 'Success',
        int          $status = 200
    ) :JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $resource,
        ], $status);
    }


    public static function collection(
        mixed $resource,
        string $message = 'Success',
        int $status = 200
    ): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if (
            $resource instanceof AnonymousResourceCollection &&
            $resource->resource instanceof AbstractPaginator
        ) {
            $paginator = $resource->resource;

            $response['data'] = $resource->collection;

            $response['meta'] = [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'from'         => $paginator->firstItem(),
                'to'           => $paginator->lastItem(),
            ];

            $response['links'] = [
                'first' => $paginator->url(1),
                'last'  => $paginator->url($paginator->lastPage()),
                'prev'  => $paginator->previousPageUrl(),
                'next'  => $paginator->nextPageUrl(),
            ];
        } else {
            $response['data'] = $resource;
        }

        return response()->json($response, $status);
    }
}
