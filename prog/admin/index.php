<?php
/**
 * @auth  bxl@gmail.com
 * @date 2017-12-12
 * 入口文件
 */
ini_set('session.use_strict_mode', true);
ini_set('session.cookie_lifetime', 86400 / 2);
ini_set('session.name', 'aducode_sid');
isset($_GET['__path__']) && $_SERVER['PATH_INFO'] = $_GET['__path__'];
require "../../tuner/init.php";

if (isset($_SERVER['TUNER_MODE'])) {
    Config::$mode = $_SERVER['TUNER_MODE'];
}
App::run(isset($_GET['debug']) && $_GET['debug'] == 'dodebug');
