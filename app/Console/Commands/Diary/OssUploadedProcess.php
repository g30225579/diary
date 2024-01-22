<?php
namespace App\Console\Commands\Diary;

use App\Helpers\Aliyun\AliyunOss;
use App\Helpers\BaseUtils;
use App\Http\Controllers\Upload\UploadController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Tinify\Tinify;
use function Tinify\fromUrl;

class OssUploadedProcess extends Command
{
    protected $signature = 'diary:oss_uploaded_process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'oss上传成功后续处理';

    public function handle()
    {
        $sources = Cache::get(UploadController::OSS_UPLOADED_KEY);

        if($sources){
            Tinify::setKey(config('custom.secret.tiny_png.api_key'));
            $processed = [];

            foreach($sources as $source){
                $urlObj = parse_url($source);
                $bucket = data_get(explode('.',$urlObj['host']), 0);
                $object = ltrim($urlObj['path'],'/');
                $file = AliyunOss::getClient(false)->signUrl($bucket,$object,86400);

                if(!BaseUtils::urlExists($file)){
                    $this->warn('oss file do not exists: '.$file);
                    continue;
                }

                //调用图片压缩服务对图片进行优化处理
                $tinySource = fromUrl($file);

                //处理完成后重新上传oss（注意此处不再设置oss上传回调）
                AliyunOss::getClient(true)->putObject($bucket, $object, $tinySource->toBuffer());

                //记录已处理的文件
                $processed[] = $source;

                $this->info('oss file process: ' . $file);
            }

            //删除缓存中已处理文件
            $sources = array_diff($sources,$processed);
            Cache::put(UploadController::OSS_UPLOADED_KEY, $sources, UploadController::OSS_UPLOADED_EXPIRE);

            $this->info('remain processing sources: ' . json_encode($sources));
        }
    }

}
