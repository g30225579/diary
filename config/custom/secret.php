<?php
/**
 * 密钥相关配置
 */
return [
    /*
     * 阿里云相关配置
     */
    'aliyun' => [
        /*
         * OSS配置
         */
        'oss' => [
            'access_key_id' => env('ALIYUN_OSS_ACCESS_KEY_ID'),
            'access_key_secret' => env('ALIYUN_OSS_ACCESS_KEY_SECRET'),
            'bucket' => env('ALIYUN_OSS_BUCKET'),
            'endpoint' => 'oss-cn-hongkong.aliyuncs.com',
            'endpoint_internal' => env('ALIYUN_OSS_ENDPOINT_INTERNAL')
        ]
    ],

    /**
     * tinypng
     */
    'tiny_png' => [
        'api_key' => env('TINYPNG_SECRET_KEY')
    ]
];
