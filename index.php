<?php

//入口文件
//ob_start();//调试
//设置时区
date_default_timezone_set("PRC");
//设置编码
ini_set("default_charset", "utf-8");
//设置目录常量
define('SITE_PATH', dirname(__FILE__).'/');

$heads = apache_request_headers();
if(!isset($heads['bot']))die('非上报访问。');
//引入工具类
include SITE_PATH.'utils.php';
//引用配置文件
include SITE_PATH.'config.php';
if (!isset($heads['Authorization']) or $heads['Authorization']!==$Authorization)die('授权失败。');
//引用Bot主类
include SITE_PATH.'bot.php';
//检查回话 key
if(!bot::validationKey())die('检查key错误');
//初始化请求
$data = json_decode(file_get_contents('php://input'), true, 512, JSON_BIGINT_AS_STRING);

$bot = new Bot($key, $qq, $url, $heads['bot']);
include SITE_PATH.'events.php';
unset($bot);//释放资源




//var_dump($data);
//$result = ob_get_clean();
//file_put_contents('debug.html', $result);//写入调试结果


