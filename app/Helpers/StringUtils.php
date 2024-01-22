<?php
namespace App\Helpers;

/**
 * 字符串处理工具
 */
class StringUtils
{
    /**
     * 换行符替换
     */
    static function replaceLineChar(string $str, string $replace = ''): string
    {
        return str_replace(array("\r\n", "\r", "\n"), $replace, $str);
    }

    /**
     * 富文本输出纯文本摘要
     * @param int $limit 0为不限制长度
     */
    static function getSummary(string $content, int $limit = 0): string
    {
        $content = self::replaceLineChar($content);
        $content = strip_tags($content);
        $content = html_entity_decode($content);
        return self::strLimit(trim($content), $limit);
    }

    /**
     * 字符串截取
     * @param string $content   要截取的字符串
     * @param int $limit        长度限制
     * @param string $symbol    超出长度的字符替代
     * @param bool $isComputeSymbol symbol是否计算长度（默认计算）
     */
    static function strLimit(string $content, int $limit, string $symbol = '...', bool $isComputeSymbol = true): string
    {
        if(mb_strlen($content) <= $limit){
            return $content;
        } else{
            if($isComputeSymbol){
                return mb_substr($content,0,$limit-strlen($symbol)) . $symbol;
            } else{
                return mb_substr($content,0,$limit) . $symbol;
            }
        }
    }

}
