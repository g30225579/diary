<?php
namespace App\Helpers;

use Intervention\Image\ImageManager;

/**
 * Intervention Image代理类
 */
class Image
{
    public static function getInstance()
    {
        return new ImageManager(config('image'));
    }

}