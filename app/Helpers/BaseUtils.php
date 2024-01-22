<?php
namespace App\Helpers;

class BaseUtils
{
    /**
     * 判断url文件是否存在
     */
    static function urlExists($url){
        $headers = @get_headers($url);
        return stripos($headers[0],"200 OK")?true:false;
    }

}
