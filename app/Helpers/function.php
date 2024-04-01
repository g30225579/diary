<?php

use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;

// 是否移动浏览器（包含平板/手机）
function checkMobile()
{
    return (new Agent())->isMobile();
}
// 是否手机设备（仅手机，不含平板）
function checkPhone()
{
    return (new Agent())->isPhone();
}
// 是否微信浏览器
function checkWeChat()
{
    return Str::contains( strtolower(request()->server('HTTP_USER_AGENT')), 'micromessenger' );
}
// 是否IOS设备
function checkIOS()
{
    return (new Agent())->is('iOS');
}
// 是否Android设备
function checkAndroid()
{
    return (new Agent())->isAndroidOS();
}