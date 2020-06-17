<?php
if(!defined('SITE_PATH')) exit;
class Bot{
    private $key;
    private $qq;
    private $url;
    private $requestQQ;
    public function __construct($key, $qq, $url, $requestQQ)
    {
        $this->key = $key;
        $this->qq = $qq;
        $this->url = $url;
        $this->requestQQ = $requestQQ;
        //TODO：多个Bot
    }

    /**
     * 返回上报请求的QQ
     * @return mixed
     */
    public function getRequestQQ()
    {
        return $this->requestQQ;
    }
    /**
     * 获取QQ号
     * @return mixed
     */
    public function getQQ()
    {
        return $this->qq;
    }
    /**
     * 艾特一个好友
     * @param $qq int 艾特目标的qq号
     * @return string mirai码
     */
	public function at($qq){
        return "[mirai:at:$qq]";
	}

    /**
     * 艾特全部
     * @return string mirai码
     */
	public function atAll(){
        return "[mirai:atAll]";
	}

    /**
     * 表情
     * @param $faceId int 表情id
     * @return string mirai码
     */
	public function face($faceId){
        return "[mirai:face:$faceId]";
	}

    /**
     * 图片URL转码
     * @param $imgUrl string 图片url
     * @return string mirai码
     */
	public function imageForUrl($imgUrl){
        return "[mirai:image::$imgUrl]";
	}

    /**
     * 从图片ID转码
     * @param $imgId string 图片id {xxx-xxx}.mirai
     * @return string mirai码
     */
	public function imageForId($imgId){
        return "[mirai:image:$imgId]";
	}

    /**
     * 从图片URL获取闪照码
     * @param $imgUrl string 图片url
     * @return string mirai码
     */
	public function flashImageForUrl($imgUrl){
        return "[mirai:flashImage::$imgUrl]";
	}

    /**
     * 从图片ID获取闪照码
     * @param $imgId string 图片id {xxx-xxx}.mirai
     * @return string mirai码
     */
	public function flashImageForId($imgId){
        return "[mirai:flashImage:$imgId]";
	}

    /**
     * 上传图片
     * @param $file string json File格式
     * @param $type string 类型 "friend" 或 "group" 或 "temp"
     * @return bool|mixed|null 返回图片id 失败返回null
     */
	public function uploadImage($file, $type){
		return $this->sendData('uploadImage', '
		"img": "'.$file.'",
		"type": "'.$type.'"
		', 'imageId', null, array('Content-Type：multipart/form-data'));
	}

    /**
     * 发送图片到群聊
     * @param $group int 群号
     * @param $urls array URLs
     * @return bool|array|null 返回图片ids
     */
	public function sendImageMessageToGroup($group, $urls){
		return $this->sendData('uploadImage', '
		"group": '.$group.',
		"urls": '.arrayToJsonArray($urls).'
		');
	}

    /**
     * 发送图片到好友
     * @param $qq int 好友qq
     * @param $urls array URLs
     * @return bool|array|null 返回图片ids
     */
	public function sendImageMessageToFriend($qq, $urls){
		return $this->sendData('uploadImage', '
		"qq": '.$qq.',
		"urls": '.arrayToJsonArray($urls).'
		');
	}

    /**
     * 发送图片到临时会话
     * @param $group int 群号
     * @param $qq int 好友qq
     * @param $urls array URLs
     * @return bool|array|null 返回图片ids
     */
	public function sendImageMessageToTemp($group, $qq, $urls){
		return $this->sendData('uploadImage', '
		"qq": '.$qq.',
		"group": '.$group.',
		"urls": '.arrayToJsonArray($urls).'
		');
	}

    /**
     * 全员禁言
     * @param $group int 群号
     * @return bool|mixed|null 返回状态码
     */
	public function muteAll($group){
		return $this->sendData('muteAll', '"target": '.$group);
	}

    /**
     * 解除全员禁言
     * @param $group int 群号
     * @return bool|mixed|null 返回状态码
     */
	public function unmuteAll($group){
		return $this->sendData('unmuteAll', '"target": '.$group);
	}

    /**
     * 禁言一个群成员
     * @param $group int 群号
     * @param $qq int 群成员qq号
     * @param $time int 禁言时间 单位：秒
     * @return bool|mixed|null 返回状态码
     */
	public function mute($group, $qq, $time){
		return $this->sendData('mute', '
		"target": '.$group.',
		"memberId": '.$qq.',
		"time": '.$time);
	}

    /**
     * 解除禁言
     * @param $group int 群号
     * @param $qq int 群成员qq号
     * @return bool|mixed|null 返回状态码
     */
	public function unmute($group, $qq){
		return $this->sendData('unmute', '
		"target": '.$group.',
		"memberId": '.$qq);
	}

    /**
     * 踢一个群成员
     * @param $group int 群号
     * @param $qq int 群成员qq号
     * @return bool|mixed|null 返回状态码
     */
	public function kick($group, $qq){
		return $this->sendData('kick', '
		"target": '.$group.',
		"memberId": '.$qq);
	}

    /**
     * 让机器人退出一个群
     * @param $group int 群号
     * @return bool|mixed|null 返回状态码
     */
	public function quit($group){
		return $this->sendData('quit', '
		"target": '.$group);
	}

    /**
     * 同意被邀请请求
     * @param $eventId string 事件id
     * @param $fromId int 邀请者qq
     * @param $groupId int 群号
     * @param $operate int true同意 false拒绝
     * @return bool|mixed|null 返回状态码
     * 好像有问题 不知道是不是http api的问题
     */
	public function respMemberJoinRequestEvent($eventId, $fromId, $groupId, $operate){
		return $this->sendData('resp/memberJoinRequestEvent', '
			"eventId": '.$eventId.',
			"fromId": '.$fromId.',
			"groupId": '.$groupId.',
			"operate": '.((int)$operate).'
			"message": ""
		');
	}

    /**
     * 同意加群请求
     * @param $eventId string 事件id
     * @param $fromId int 申请人qq号
     * @param $groupId int 群号
     * @param $operate int 0同意入群 1拒绝入群 2忽略请求 3拒绝入群并添加黑名单，不再接收该用户的入群申请 4忽略入群并添加黑名单，不再接收该用户的入群申请
     * @return bool|mixed|null 返回状态码
     */
	public function memberJoinRequestEvent($eventId, $fromId, $groupId, $operate){
		return $this->sendData('resp/memberJoinRequestEvent', '
			"eventId": '.$eventId.',
			"fromId": '.$fromId.',
			"groupId": '.$groupId.',
			"operate": '.($operate).'
			"message": ""
		');
	}

    /**
     * 同意好友申请
     * @param $eventId string 事件id
     * @param $fromId int 申请人qq号
     * @param $groupId int 群号 可忽略
     * @param $operate int 是否同意 1同于 2拒绝 3拒绝添加好友并添加黑名单，不再接收该用户的好友申请
     * @return bool|mixed|null 返回状态码
     */
	public function newFriendRequestEvent($eventId, $fromId, $groupId = 0, $operate = 1){
		return $this->sendData('resp/newFriendRequestEvent', '
			"eventId": '.$eventId.',
			"fromId": '.$fromId.',
			"groupId": '.$groupId.',
			"operate": '.((int)$operate).'
			"message": ""
		');
	}

    /**
     * 发送群消息
     * @param $group int 群号
     * @param $msg string 消息链
     * @param null $quote int 引用的消息id
     * @return bool|mixed|null 返回消息id 失败返回null
     */
	public function sendGroupMessage($group, $msg, $quote=null){
		$msg = escapeToMessageChain($msg);
		if($msg==null){
			return null;
		}
		return $this->sendData('sendGroupMessage', 
			'"group": '.$group.',
			'.($quote!=null?('"quote": '.$quote.', '):'').'
			"messageChain": [
				'.$msg.'
			]', 'messageId');
	}

    /**
     * 发送好友消息
     * @param $friend int 好友qq
     * @param $msg string 消息链
     * @param null $quote int 引用的消息id
     * @return bool|mixed|null 返回消息id 失败返回null
     */
	public function sendFriendMessage($friend, $msg, $quote=null){
		$msg = escapeToMessageChain($msg);
		if($msg==null){
			return null;
		}
		return $this->sendData('sendFriendMessage', 
			'"target": '.$friend.',
			'.($quote!=null?('"quote": '.$quote.', '):'').'
			"messageChain": [
				'.$msg.'
			]', 'messageId');
	}

    /**
     * 发送临时消息消息
     * @param $group int 群号
     * @param $qq int 好友qq
     * @param $msg string 消息链
     * @param null $quote int 引用的消息id
     * @return bool|mixed|null 返回消息id 失败返回null
     */
	public function sendTempMessage($group, $qq, $msg, $quote=null){
		$msg = escapeToMessageChain($msg);
		if($msg==null){
			return null;
		}
		return $this->sendData('sendTempMessage', 
			'"group": '.$group.',
			"qq": '.$qq.',
			'.($quote!=null?('"quote": '.$quote.', '):'').'
			"messageChain": [
				'.$msg.'
			]', 'messageId');
	}

    /**
     * 撤回消息
     * @param $msgID int 消息id
     * @return bool|mixed|null 返回状态码
     */
	public function recallMessage($msgID){
		return $this->sendData('recall', '"target": '.$msgID);
	}

    /**
     * 发送数据到mirai
     * @param $type
     * @param string $json
     * @param null $return
     * @param null $cookies
     * @param string[] $headers
     * @return bool|mixed|null
     */
	public function sendData($type, $json = '', $return = null, $cookies = null, $headers = array('Content-Type: application/json;charset=utf8')){
		$json = '{
			"sessionKey": "'.$this->key.'",
			'.$json.'
		}';
		$re = getHttpData($this->url.'/'.$type, $json, $cookies, $headers);
		if(!$re)return null;
		$re = json_decode($re, true);
		if($return===null)
			if(isset($re['code']))
				return $re['code'];
			else
				return $re;
		else
			return isset($re[$return])?$re[$return]:false;
	}

    /**
     * 验证key
     * @return bool|string
     */
	public static function validationKey(){
		global $key, $qq, $authKey, $url;
        $results = json_decode(getHttpData($url.'/config?sessionKey='.$key), true);//没办法 没提供验证接口
		if(isset($results['sessionKey'])){
			return true;
		}else{
			$results = json_decode(getHttpData($url.'/auth', json_encode(['authKey' => $authKey])), true);
			if($results['code']==0){
				$key = $results['session'];
                $results = json_decode(getHttpData($url.'/verify', json_encode(['sessionKey' => $key, 'qq' => $qq])), true);
                if($results['code']==0) {
                    file_put_contents('key.php', "<?php\n\$key='" . $key . "';");
                    return true;
                }
                return false;
			}else{
				return false;
			}
		}
	}
}