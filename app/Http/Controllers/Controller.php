<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * ajax统一返回值格式
     */
    protected function output($state, $msg = null)
    {
        return Response::json([
            'state' => $state,
            'data' => ($state ? $msg : null),
            'msg' => (!$state ? $msg : null)
        ]);
    }

}
