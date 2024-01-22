<?php
namespace App\Helpers;

use App\Helpers\Aliyun\AliyunOss;

/**
 * 文件上传、获取相关
 */
class UploadUtils
{
    // 获取文件URL
    public static function getFileUrl($path, $name)
    {
        //完整URL的文件名直接返回
        if( stripos($name,'http://') !== false || stripos($name,'https://') !== false ){
            return $name;
        }

        return AliyunOss::getBucketUrl() . '/upload/' . self::getFile($path,$name);
    }

    // 获取文件
    private static function getFile($path, $name)
    {
        //如果有分隔符，需要根据文件名获取分组
        $groups = explode('-',$name);
        if(count($groups) > 1){
            $path = $path . '/' . $groups[0];
        }

        return $path . (substr($name,0,1)=='/'?'':'/') . $name;
    }

}
