<?php
namespace App\Http\Controllers\Space;

use App\Http\Controllers\Controller;

/**
 * 用户家园首页，使用框架自带页面，登录后展示页
 */

class IndexController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('space.home');
    }

}
