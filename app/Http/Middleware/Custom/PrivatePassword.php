<?php
namespace App\Http\Middleware\Custom;

use Closure;

/**
 * 二次输入的独立密码
 */
class PrivatePassword
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //未经二次验证则跳转密码输入页
        if(!$request->session()->get('private_password')){
            return redirect('/common/private_password?back_url='.urlencode($request->fullUrl()));
        }

        $response = $next($request);

        return $response;
    }

}
