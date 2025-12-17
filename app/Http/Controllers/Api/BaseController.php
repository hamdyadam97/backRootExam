<?php

namespace App\Http\Controllers\Api;

use App\Constants\StatusCodes;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function send_response($message, $result, $code = 200)
    {
        $response = [
            'status' =>true,
            'statusCode' => $code,
            'message' => $message,
            'data' => $result,
        ];

        return response()->json($response, $code);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function send_error($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'status' =>false,
            'statusCode' => $code,
            'message' => $error,
        ];

        if(!empty($errorMessages)){
            $response['errors'] = $errorMessages;
        }

        return response()->json($response, $code);
    }


    function api_response($status, $message, $data = [], $statusCode = 200 , $extra_data = [])
    {
        $response = ['code' => $statusCode, 'status' => $status, 'message' => $message, 'data' => $data];

        $response = array_merge($response , $extra_data);
        return response()->json($response, $statusCode);
    }

    public function getPaginatorData($paginator)
    {
        return [
            'pagination' => [
                'total' => $paginator->total(),
                'count' => $paginator->count(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'total_pages' => $paginator->lastPage(),
                'next_page_url' => $paginator->nextPageUrl(),
                'pages' => $paginator->toArray()['links'],
            ]
        ];
    }

}
