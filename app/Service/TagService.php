<?php
namespace App\Service;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * 日志标签
 */

class TagService
{
    const SEPARATOR = ',';
    const CACHE_KEY = 'diary:tag_map:v20240911';

    /**
     * 标签汇总统计（默认总是获取最新数据）
     */
    public function getTagMap(bool $refresh = false): array
    {
        if($refresh){
            $this->tagMapClear();
        }
        return Cache::remember(self::CACHE_KEY, 3600, function():array{
            $resTags = DB::table('diary')->whereRaw("ifnull(tags,'') <> ''")->groupBy(['tags'])->pluck('tags');

            $countMap = [];
            foreach($resTags as $tagStr){
                $tags = array_merge($this->getTagsByJsonStr($tagStr));
                foreach($tags as $tag){
                    if(!array_key_exists($tag,$countMap)){
                        $countMap[$tag] = 0;
                    }
                    $countMap[$tag]++;
                }
            }
            arsort($countMap);

            $tagMap = [];
            foreach($countMap as $tag=>$count){
                $tagMap[$tag] = [
                    'badge' => $this->getTagBadge($tag),
                    'count' => $count
                ];
            }
            return $tagMap;
        });
    }

    /**
     * 清楚标签汇总数据缓存
     */
    public function tagMapClear()
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * 传入标签字段值，返回标签数组
     */
    public function getTagsByJsonStr($tagJsonStr):array
    {
        $tags = [];
        if($tagJsonStr){
            $tags = json_decode($tagJsonStr);
        }
        return $tags;
    }

    /**
     * 标签徽章class
     */
    public function getTagBadge($tag):string
    {
        $badgeList = ['','layui-bg-orange','layui-bg-green','layui-bg-cyan','layui-bg-blue','layui-bg-black'];
        return $badgeList[hexdec(substr(md5($tag),0,8))%count($badgeList)];
    }

}