<?php
namespace App\Helpers\Aliyun;

use App\Enums\Common\UPLOAD_TYPE;
use DateTime;
use OSS\Core\OssException;
use OSS\OssClient;
use Tinify\Exception;

class AliyunOss
{
    /**
     * 获取OSS实例
     */
    public static function getClient(bool $internal = false)
    {
        try {
            return new OssClient(
                config('custom.secret.aliyun.oss.access_key_id'),
                config('custom.secret.aliyun.oss.access_key_secret'),
                'https://' . data_get(config('custom.secret.aliyun.oss'), $internal ? 'endpoint_internal' : 'endpoint')
            );
        } catch (OssException $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 获取OSS文件上传签名
     *
     * @param int $size     文件大小限制，默认2MB
     * @param int $expire   设置该policy超时时间多少秒 即这个policy过了这个有效时间，将不能访问
     * @param string $dir   用户上传的数据，必须是以$dir开始，不然上传会失败，这一步不是必须项，只是为了安全起见，防止用户通过policy上传到别人的目录
     * @return void
     */
    public static function getSign(int $size = 1024*1024*2, int $expire = 60, string $dir = UPLOAD_TYPE::DEFAULT):string
    {
        $expire = time() + $expire;
        $expiration = self::gmt_iso8601($expire);

        $conditions = [
            //最大文件大小
            [
                0 => 'content-length-range',
                1 => 0,
                2 => $size
            ],
            // policy目录
            [
                0 => 'starts-with',
                1 => '$key',
                2 => $dir
            ]
        ];

        $policy = json_encode([
            'expiration' => $expiration,
            'conditions' => $conditions,
        ]);
        $base64_policy = base64_encode($policy);
        $signature = base64_encode(hash_hmac('sha1', $base64_policy, config('custom.secret.aliyun.oss.access_key_secret'), true));

        return json_encode([
            'accessid' => config('custom.secret.aliyun.oss.access_key_id'),
            'host' => self::getBucketUrl(),
            'policy' => $base64_policy,
            'signature' => $signature,
            'expire' => $expire,
            'dir' => $dir,
            'size' => $size
        ]);
    }

    /**
     * 获取bucket域名
     */
    public static function getBucketUrl()
    {
        return sprintf('https://%s.%s', config('custom.secret.aliyun.oss.bucket'), config('custom.secret.aliyun.oss.endpoint'));
    }

    // 复制阿里云代码
    private static function gmt_iso8601($time) {
        $dtStr = date("c", $time);
        $mydatetime = new DateTime($dtStr);
        $expiration = $mydatetime->format(DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration."Z";
    }

}
