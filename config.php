<?php
if(!defined('SITE_PATH')) exit;
/** authKey在插件setting.yml找到**/
$authKey = '2665337794';
/** QQ **/
$qq = 3084411499;
/** Http api URL **/
$url = 'http://127.0.0.1:8080';
/**
 * 授权信息
 */
$Authorization = 'basic xxx';


//获取key
getSessionKey();
/**
 * 获取会话key
 */
function getSessionKey(){
	if(file_exists('key.php')){
		global $key;
		include SITE_PATH.'key.php';
	}
}