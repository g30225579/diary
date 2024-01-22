<?php
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