<?php
if(!defined('SITE_PATH')) exit;
/**
 * 访问网页
 * @param string $url 请求网址
 * @param string $data 请求数据，非空时使用POST方法
 * @param string $cookies 可空
 * @param array $headers
 * @param string $proxy 代理地址，可空
 * @param int $time 超时时间，单位：秒。默认10秒
 * @return string 执行结果
 */
function getHttpData($url, $data = '', $cookies = null, $headers = array('Content-Type: application/json;charset=utf8'), $proxy = null, $time = 8)
{
	$ch = curl_init($url); //初始化 CURL 并设置请求地址
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //设置获取的信息以文件流的形式返回
	if($data) curl_setopt($ch, CURLOPT_POST, 1); //设置 post 方式提交
	if($data) curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //设置 post 数据
	if(is_array($cookies) && $cookies) {
		foreach ($cookies as $array) $data .= $array;
		$cookies = $data;
	}
	if($cookies) curl_setopt($ch, CURLOPT_COOKIE, $cookies);   //设置Cookies
	if($headers) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	if($proxy) curl_setopt ($ch, CURLOPT_USERAGENT, $proxy);
	curl_setopt($ch, CURLOPT_TIMEOUT, $time);   //只需要设置一个秒的数量就可以
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在

	$data = curl_exec($ch); //执行命令
	curl_close($ch); //关闭 CURL

	return $data;
}

/**
 * 转义消息链到字符串
 * @param $messageChain
 * @return array|null
 */
function escapeToString($messageChain){
	if(!is_array($messageChain))return null;
	$msg = [0, 0, ''];
	foreach($messageChain as $message){
		switch($message['type']){
			case 'Source'://消息信息
				$msg[0] = $message['id'];//消息ID  用于引用和撤回
				$msg[1] = $message['time'];//消息发送时间
			break;
			case 'Plain'://文本
				$msg[2] .= $message['text'];
			break;
			case 'Quote'://引用
				$msg[2] .= '[mirai:quote:'.$message['id'].':'.$message['groupId'].':'.$message['senderId'].':'.$message['targetId'].']';
			break;
			case 'At'://艾特
				$msg[2] .= '[mirai:at:'.$message['target'].']';
			break;
			case 'AtAll'://艾特
				$msg[2] .= '[mirai:atAll]';
			break;
			case 'FlashImage'://闪照
				$msg[2] .= '[mirai:flashImage:'.$message['imageId'].':'.$message['url'].']';
			break;
			case 'Image'://图片
				$msg[2] .= '[mirai:image:'.$message['imageId'].':'.$message['url'].']';
			break;
			case 'Face'://表情
				$msg[2] .= '[mirai:face:'.$message['faceId'].']';
			break;
		}
	}
	return $msg;
}

/**
 * 转义消息到消息链
 * @param $allMessage
 * @return false|string|null
 */
function escapeToMessageChain($allMessage){
	$messageChain = '';
	$arr = [];
	$preg = '/(?<=\[mirai:)[^\]]+/';
	preg_match_all($preg,$allMessage,$arr);
	if(count($arr[0])<=0){
		if(trim($allMessage)==''){
			return null;
		}
		$messageChain .= '{
			"type": "Plain",
			"text": "'.$allMessage.'"
		}';
		return $messageChain;
	}
	foreach($arr[0] as $re){
		$len = strpos($allMessage, '[mirai:'.$re.']');
		$info = explode(':', $re);
		$allMessage = str_replace('[mirai:'.$re.']', '', $allMessage);
		if($len>0){
			$lastMessage = substr($allMessage, 0, $len);
			if(strlen(trim($lastMessage))>0){
				$messageChain .= '{
					"type": "Plain",
					"text": "'.$lastMessage.'""
				},';
			}
		}
		switch($info[0]){
			case 'at'://艾特
				$messageChain .= '{
					"type": "At",
					"target": '.$info[1].'
				},';
			break;
			case 'image'://图片
				if(!isset($info[2]) or $info[2]==''){
					$messageChain .= '{
						"type": "Image",
						"imageId": "'.$info[1].'"
					},';
				}else{
					$url = substr(implode(':', $info), strlen($info[0])+strlen($info[1])+2, strlen(implode(':', $info)));
					$messageChain .= '{
						"type": "Image",
						"url": "'.$url.'"
					},';
				}
			break;
			case 'flashImage'://闪照
				if(!isset($info[2]) or $info[2]==''){
					$messageChain .= '{
						"type": "FlashImage",
						"imageId": "'.$info[1].'"
					},';
				}else{
					$url = substr(implode(':', $info), strlen($info[0])+strlen($info[1])+2, strlen(implode(':', $info)));
					$messageChain .= '{
						"type": "FlashImage",
						"url": "'.$url.'"
					},';
				}
			break;
			case 'face'://表情
				$messageChain .= '{
					"type": "Face",
					"faceId": "'.$info[1].'"
				},';
			break;
			case 'atAll'://艾特全部
				$messageChain .= '{
					"type": "AtAll"
				},';
			break;
		}
	}
	if(strlen(trim($allMessage))>0){
		$messageChain .= '{
			"type": "Plain",
			"text": "'.$allMessage.'""
		},';
	}
	$messageChain = substr($messageChain, 0, strlen($messageChain)-1);
	return $messageChain;
}

/**
 * 数组转移为json数组s
 * @param $array
 * @return string
 */
function arrayToJsonArray($array){
	$json = '['.PHP_EOL;
	foreach($array as $i => $v){
		if(is_numeric($i)){
			$json.='"'.$v.'",';
		}else{
			$json.='"'.$i.'": "'.$v.'",';
		}
	}
	$json = substr($json, 0, strlen($json)-1);
	return $json.PHP_EOL .']';
}









