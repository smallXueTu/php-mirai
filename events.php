<?php
if(!defined('SITE_PATH')) exit;
if(isset($data['type'])){
	if(isset($data['messageChain'])){
		$msgInfo = escapeToString($data['messageChain']);
		$msg = $msgInfo[2];
	}
	switch($data['type']){
		case 'GroupMessage'://群消息
			eventGroupMsg($bot, $data['sender']['group']['id'], $msg, $data['sender']['id'], $msgInfo[1], $msgInfo[0], $data);
		break;
		case 'FriendMessage'://好友消息
			eventFriendMsg($bot, $msg, $data['sender']['id'], $msgInfo[1], $msgInfo[0], $data);
		break;
		case 'TempMessage'://临时消息
			eventTempMsg($bot, $data['sender']['group']['id'], $msg, $data['sender']['id'], $msgInfo[1], $msgInfo[0], $data);
		break;
		case 'BotInvitedJoinGroupRequestEvent'://机器人被邀请加群
			eventBotInvitedJoinGroupRequest($bot, $data['eventId'], $data['message'], $data['fromId'], $data['groupId'], $data['groupName'], $data);
		break;
		case 'MemberJoinRequestEvent'://群成员申请加群 操作需要管理员权限
			eventMemberJoinRequest($bot, $data['eventId'], $data['fromId'], $data['groupId'], $data['groupName'], $data['nick'], $data['message'], $data);
		break;
		case 'NewFriendRequestEvent'://群成员申请加群 操作需要管理员权限 groupId
            eventNewFriendRequest($bot, $data['eventId'], $data['fromId'], $data['groupId'], $data['nick'], $data['message'], $data);
		break;
		case 'MemberJoinEvent'://新人加群
			eventMemberJoin($bot, $data['member']['id'], $data['member']['group']['id'], $data['member']['memberName'], $data);
		break;
		case 'MemberLeaveEventQuit'://成员退群
			eventMemberLeaveQuit($bot, $data['member']['id'], $data['member']['group']['id'], $data['member']['memberName'], $data);
		break;
		case 'MemberLeaveEventKick'://成员被踢
			eventMemberLeaveKick($bot, $data['member']['id'], $data['member']['group']['id'], $data['member']['memberName'], $data['operator']['id'], $data['operator']['memberName'], $data);
		break;
		case 'MemberMuteEvent'://成员被禁言 不会是Bot
			eventMemberMute($bot, $data['durationSeconds'], $data['member']['id'], $data['member']['group']['id'], $data['member']['memberName'], $data['operator']['id'], $data['operator']['memberName'], $data);
		break;
	}
}

/**
 * 机器人被邀请加群
 * @param Bot $bot 机器人类
 * @param $eventId int 事件id
 * @param $message string 邀请信息 一般为空
 * @param $fromId int 邀请者qq号
 * @param $groupId int 邀请的群
 * @param $groupName string 邀请的群名
 * @param $data array 事件的全部信息
 */
function eventBotInvitedJoinGroupRequest(Bot $bot, $eventId, $message, $fromId, $groupId, $groupName, $data){
	$bot->respMemberJoinRequestEvent($eventId, $fromId, $groupId, true);//同意申请
	//$bot->respMemberJoinRequestEvent($eventId, $fromId, $groupId, false);//拒绝申请
}

/**
 * 群成员申请加群 操作需要管理员权限
 * @param Bot $bot 机器人类
 * @param $eventId int 事件id
 * @param $fromId int 申请人qq号
 * @param $groupId int 邀请的群
 * @param $groupName string 邀请的群名
 * @param $nick string 申请者昵称
 * @param $message string 验证信息 申请消息
 * @param $data array 事件的全部信息
 */
function eventMemberJoinRequest(Bot $bot, $eventId, $fromId, $groupId, $groupName, $nick, $message, $data){
	$bot->memberJoinRequestEvent($eventId, $fromId, $groupId, 0);//同意申请
	//$bot->memberJoinRequestEvent($eventId, $fromId, $groupId, 1);//拒绝申请
}

/**
 * 群成员申请加群 操作需要管理员权限
 * @param Bot $bot 机器人类
 * @param $eventId int 事件id
 * @param $fromId int 申请人qq号
 * @param $groupId int 如果该用户通过qq群添加的机器人 则为群号 否则为0
 * @param $nick string 申请者昵称
 * @param $message string 验证信息 申请消息
 * @param $data array 事件的全部信息
 */
function eventNewFriendRequest(Bot $bot, $eventId, $fromId, $groupId, $nick, $message, $data){
    $bot->newFriendRequestEvent($eventId, $fromId, $groupId, 0);//同意申请
	//$bot->newFriendRequestEvent($eventId, $fromId, $groupId, 1);//拒绝申请
}

/**
 * 机器人收到好友消息
 * @param Bot $bot 机器人类
 * @param $msg string 消息链
 * @param $fromQQ int 发送消息者id
 * @param $time int 消息发送时间 单位：秒
 * @param $msgID int 消息id 用于引用
 * @param $data array 事件的全部信息
 */
function eventFriendMsg(Bot $bot, $msg, $fromQQ, $time, $msgID, $data){
	if($msg=='你好'){
		$bot->sendFriendMessage($fromQQ, '你好~');
	}
}

/**
 * 机器人收到群消息
 * @param Bot $bot 机器人类
 * @param $group int 群号
 * @param $msg string 消息链
 * @param $fromQQ int 发送消息者id
 * @param $time int 消息发送时间 单位：秒
 * @param $msgID int 消息id 用于引用和撤回
 * @param $data array 事件的全部信息
 */
function eventGroupMsg(Bot $bot, $group, $msg, $fromQQ, $time, $msgID, $data){
	$qq = 2665337794;
	if($msg=='你好' and $fromQQ==$qq){//比如
		$bot->sendGroupMessage($group, $bot->at(2665337794).'你好');
	}
	if($msg=='亲亲' and $fromQQ==$qq){
		$bot->sendGroupMessage($group, $bot->face(109));
	}
	if($msg=='撤回我消息'){
		$bot->recallMessage($msgID);
	}
	if($msg=='回复我'){
		$bot->sendGroupMessage($group, '干嘛', $msgID);
	}
	if($msg=='全员禁言' and $fromQQ==$qq){
		$bot->muteAll($group);
	}
	if($msg=='请禁言我'){
		$bot->sendGroupMessage($group, '没有问题，禁言时间：30天。');
		$bot->mute($group, $fromQQ, 60*60*24*30);
		sleep(100);
		$bot->unmute($group, $fromQQ);
	}
	if($msg=='色图' and $fromQQ==$qq){
		$bot->sendGroupMessage($group, $bot->imageForUrl(getHttpData('http://pc.ltcraft.cn/api/hsoApi.php')));
		$bot->sendImageMessageToGroup($group, [getHttpData('http://pc.ltcraft.cn/api/hsoApi.php')]);
	}
	if($msg=='请踢我'){
		$bot->sendGroupMessage($group, '满足你的要求。');
		$bot->kick($group, $fromQQ);
	}
	if($msg=='请退群' and $fromQQ==$qq){
		$bot->sendGroupMessage($group, '呜呜呜，人家走就是了。');
		sleep(3);
		$bot->quit($group);
	}
}

/**
 * 机器人收到临时消息
 * @param Bot $bot 机器人类
 * @param $group int 群号
 * @param $msg string 消息链
 * @param $fromQQ int 发送消息者id
 * @param $time int 消息发送时间 单位：秒
 * @param $msgID int 消息id 用于引用
 * @param $data array 事件的全部信息
 */
function eventTempMsg(Bot $bot, $group, $msg, $fromQQ, $time, $msgID, $data){
	if($msg=='你好'){//比如
		$bot->sendGroupMessage($group, $bot->at(2665337794).'你好~');
	}
}

/**
 * 群成员加群事件
 * @param Bot $bot 机器人类
 * @param $memberQQ int 成员qq号
 * @param $group int 群号
 * @param $memberName string 群名称
 * @param $data array 事件的全部信息
 */
function eventMemberJoin(Bot $bot, $memberQQ, $group, $memberName, $data){
	$bot->sendGroupMessage($group, '欢迎'.$memberName);
}

/**
 * 群友退群事件
 * @param Bot $bot 机器人类
 * @param $memberQQ int 成员qq号
 * @param $group int 群号
 * @param $memberName string 群名称
 * @param $data array 事件的全部信息
 */
function eventMemberLeaveQuit(Bot $bot, $memberQQ, $group, $memberName, $data){
	$bot->sendGroupMessage($group, $memberName.'滚蛋了。');
}

/**
 * 群成员被踢
 * @param Bot $bot 机器人类
 * @param $memberQQ int 群成员qq号
 * @param $group int 群号
 * @param $memberName string 群成员昵称
 * @param $operatorQQ int 操作者qq号
 * @param $operatorName string 操作者昵称
 * @param $data array 事件的全部信息
 */
function eventMemberLeaveKick(Bot $bot, $memberQQ, $group, $memberName, $operatorQQ, $operatorName, $data){
	$bot->sendGroupMessage($group, $memberName.'被'.$operatorName.'踢了。');
}

/**
 * @param Bot $bot 机器人类
 * @param $time int 禁言时间 单位：秒
 * @param $membeQQ int 被禁言qq
 * @param $group int 群号
 * @param $memberName string 群成员昵称
 * @param $operatorQQ int 操作者qq号
 * @param $operatorName string 操作者昵称
 * @param $data array 事件的全部信息
 */
function eventMemberMute(Bot $bot, $time, $membeQQ, $group, $memberName, $operatorQQ, $operatorName, $data){
	$bot->sendGroupMessage($group, '恭喜老哥'.$memberName.'喜提禁言套餐。');
}