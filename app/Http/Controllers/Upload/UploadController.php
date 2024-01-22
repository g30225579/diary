<?php
namespace App\Http\Controllers\Upload;

use Exception;
use App\Enums\Common\UPLOAD_TYPE;
use App\Helpers\Aliyun\AliyunOss;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UploadController extends Controller
{
    const OSS_UPLOADED_KEY = 'oss_uploaded';
    const OSS_UPLOADED_EXPIRE = 1440*7; //单位是分钟

    /**
     * 获取OSS上传签名
     */
    public function getSign()
    {
        $uploadType = request()->get('upload_type', UPLOAD_TYPE::DEFAULT);
        if(!UPLOAD_TYPE::keyExists($uploadType)){
            throw new Exception('上传类型不正确');
        }

        $size = 1024 * 1024 * 2;
        $expire = 60;
        $dir = 'upload/' . $uploadType;

        return $this->output(1, [
            'sign' => AliyunOss::getSign($size, $expire, $dir)
        ]);
    }

    /**
     * oss文件上传成功标记
     */
    public function ossUploaded()
    {
        $source = request()->input('source');

        $ossUploadedSources = Cache::get(self::OSS_UPLOADED_KEY,[]);
        if(!in_array($source,$ossUploadedSources)){
            $ossUploadedSources[] = $source;
        }

        Cache::put(self::OSS_UPLOADED_KEY, $ossUploadedSources, self::OSS_UPLOADED_EXPIRE);

        Log::info('oss uploaded: ', [
            'source' => $source
        ]);

        return $this->output(1);
    }

}
