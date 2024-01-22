<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

/**
 * 二次验证
 */

class PrivatePasswordController extends Controller
{
    public function index()
    {
        return view('auth.private_password');
    }

    /**
     * 编辑记事页
     */
    public function check()
    {
        $encrypt = md5(auth()->user()->created_at->timestamp . request()->input('private_password'));
        if($encrypt === auth()->user()->private_password){
            request()->session()->put('private_password',1);
        }

        return redirect(request()->input('back_url') ?: '/');
    }

}
