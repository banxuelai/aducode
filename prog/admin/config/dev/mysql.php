<?php
/**
 * @author banxuelai@vread.cn
 * @date 2017-11-14
 * 数据库配置文件
 */
return array(
    'default' => array(
        'master' => array(
            'host' => 'localhost',
            'user' => 'root',
            'password' => '20170711.HS.test',
            'dbname' => 'happycode_old',
            'charset' => 'utf8mb4', //支持emoji表情
            'port' => 3306,
        ),
        'slave' => array(
            'host' => 'localhost',
            'user' => 'root',
            'password' => '20170711.HS.test',
            'dbname' => 'happycode_old',
            'charset' => 'utf8mb4', //支持emoji表情
            'port' => 3306,
        ),
    ),
    //新数据
    'new' => array(
            'master' => array(
                    'host' => 'localhost',
                    'user' => 'root',
                    'password' => '20170711.HS.test',
                    'dbname' => 'happycode',
                    'charset' => 'utf8mb4', //支持emoji表情
                    'port' => 3306,
            ),
            'slave' => array(
                    'host' => 'localhost',
                    'user' => 'root',
                    'password' => '20170711.HS.test',
                    'dbname' => 'happycode',
                    'charset' => 'utf8mb4', //支持emoji表情
                    'port' => 3306,
            ),
    ),
);
