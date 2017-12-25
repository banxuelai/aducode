<?php
/**
 * @author banxuelai@vread.cn
 * @date 2017-11-14
 * 数据库配置文件
 */
return array(
    'default' => array(
        'master' => array(
            'host' => '10.172.218.242',
            'user' => 'happycode_rw',
            'password' => 'us3om$0!cU@m$C*n',
            'dbname' => 'happycode',
            'charset' => 'utf8mb4', //支持emoji表情
            'port' => 3306,
        ),
        'slave' => array(
            'host' => '10.170.184.134',
            'user' => 'happycode_r',
            'password' => '!#x%Dq0E%6Or#!Ok^Xb3HTfm4MS61mFb',
            'dbname' => 'happycode',
            'charset' => 'utf8mb4', //支持emoji表情
            'port' => 15000,
        ),
    ),
);
