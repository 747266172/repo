<?php
include "wechat.class.php";
$options = array(
    'token' => 'ncvt_ea' //填写你设定的key
);

require_once dirname(__FILE__) . "/BaeLog.class.php";
$secret = array("user"=>"MG2aYMnT4rmU8up9ToZcaRP0","passwd"=>"cG3kajTqR1mIlAubnGH0LdU8mUf23MYZ" );
$log = BaeLog::getInstance($secret);
$log->setLogLevel(16);

$log->Notice("baelog");

$weObj = new Wechat($options);
//$weObj->valid();
$type = $weObj->getRev()->getRevType();
//获取用户微信号
$user = $weObj->getRevFrom();

switch($type) {
           case Wechat::MSGTYPE_TEXT:
                //获取用户发过来的关键词
                $keyword = $weObj->getRevContent();
                $log->Notice($user." type=".$type." keyword=".$keyword);

                $data = keyquery($keyword);

                //$weObj->text("回复")->reply();

                //$log->Notice(implode(" ", $array));

                if($data && is_array($data)){
                    if($data['type'] == 0){
                        $weObj->text($data['content'])->reply();
                    }else{
                        $msg = array();
                        $msg[] = array(
                            'Title' => $data['title'],
                            'Description' => $data['content'],
                            'PicUrl' => $data['picurl'],
                            'Url' => $data['url'].$user
                            );
                        $weObj->news($msg)->reply();
                    }
                    return;

                }

                $data = keyquery('帮助');
                $weObj->text($data["content"])->reply();

               exit;
              break;
           case Wechat::MSGTYPE_EVENT:

               break;
           case Wechat::MSGTYPE_IMAGE:

               break;
           default:
               $weObj->text("help info")->reply();
}

$weObj->text("您好！欢迎关注风信微信教务！我们将竭诚为您服务！请先<a href='http://ncvteduwx.duapp.com/index.php/Home/Index/login?wxno=".$user."'>捆绑账号</a>。输入“帮助”显示功能列表。")->reply();


function keyquery($key) {
    $sql = "select title,content,picurl,url,type from keyword where keyword='". $key."'";
    echo $sql;

    $con = mysql_connect("sqld.duapp.com:4050", "MG2aYMnT4rmU8up9ToZcaRP0", "cG3kajTqR1mIlAubnGH0LdU8mUf23MYZ");
    if (!$con) {
        die('Could not connect: ' . mysql_error());
    }

    mysql_select_db("IMgbSkTpuOtFCjvVpsdO", $con);

    $result = mysql_query($sql);

    $data = null;
    while ($row = mysql_fetch_array($result)) {
        $data =$row;
        break;
    }

    mysql_close($con);

    return $data;
}

