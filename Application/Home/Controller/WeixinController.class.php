<?php
namespace Home\Controller;
use Think\Controller;
use COM\Wechat;
//use Think\WechatAuth;

class WeixinController extends Controller {


    public function index(){
    	$token = 'ncvt_ea'; //微信后台填写的TOKEN

        /* 加载微信SDK */
        $wechat = new Wechat($token);

        /* 获取请求信息 */
        $data = $wechat->request();

        echo "data".$data;

        if($data && is_array($data)){

            /**
             * 你可以在这里分析数据，决定要返回给用户什么样的信息
             * 接受到的信息类型有9种，分别使用下面九个常量标识
             * Wechat::MSG_TYPE_TEXT       //文本消息
             * Wechat::MSG_TYPE_IMAGE      //图片消息
             * Wechat::MSG_TYPE_VOICE      //音频消息
             * Wechat::MSG_TYPE_VIDEO      //视频消息
             * Wechat::MSG_TYPE_MUSIC      //音乐消息
             * Wechat::MSG_TYPE_NEWS       //图文消息（推送过来的应该不存在这种类型，但是可以给用户回复该类型消息）
             * Wechat::MSG_TYPE_LOCATION   //位置消息
             * Wechat::MSG_TYPE_LINK       //连接消息
             * Wechat::MSG_TYPE_EVENT      //事件消息
             *
             * 事件消息又分为下面五种
             * Wechat::MSG_EVENT_SUBSCRIBE          //订阅
             * Wechat::MSG_EVENT_SCAN               //二维码扫描
             * Wechat::MSG_EVENT_LOCATION           //报告位置
             * Wechat::MSG_EVENT_CLICK              //菜单点击
             * Wechat::MSG_EVENT_MASSSENDJOBFINISH  //群发消息成功
             */
            //$wechat->replyText('其它消息');die();
            //$wechat->replyText($data["MsgType"]."  ".$data["Event"]);die();
            if($data['MsgType'] == Wechat::MSG_TYPE_EVENT){
            	if($data['Event'] == Wechat::MSG_EVENT_CLICK){
            		if($data['EventKey'] == "XSKB"){
            			//$wechat->replyNewsOnce("我的课表","我的课表内容","http://ncvteduwx.duapp.com/index.php/Home/Index/jrkb"，"");
            		}
            	}
            }
            else{
                $wechat->replyText("欢迎关注南宁职业技术学院信息工程学院\n回复“帮助”获取更多信息。");
            }
        }

    }

    /*public function createmenu(){
    	$wechat = new WechatAuth("wxab814432de7af5b8","66c401e9bcb306ef69f5c7c1a861c2c4");
    	$button =  array(
    			array(
    				"name" => "学生查询",
    				"sub_button" => array(
    					array(
    						"type" => "view",
    						"name" => "成绩查询",
    						"url" => "http://ncvteduwx.duapp.com/index.php/Home/Index/cjcx"
    					),
    					array(
    						"type" => "view",
    						"name" => "我的课表",
    						"url" => "http://ncvteduwx.duapp.com/index.php/Home/Index/jrkb"
    					),
    					array(
    						"type" => "view",
    						"name" => "考试安排",
    						"url" => "http://ncvteduwx.duapp.com/index.php/Home/Index/ksap"
    					),
    					array(
    						"type" => "view",
    						"name" => "请假",
    						"url" => "http://ncvteduwx.duapp.com/index.php/Home/Index/qjqk"
    					),
    					array(
    						"type" => "view",
    						"name" => "评教",
    						"url" => "http://ncvteduwx.duapp.com/index.php/Home/Index/jxpj"
    					)
    				)
    			),
    			array(
    				"name" => "教师查询",
    				"sub_button" => array(
    					array(
    						"type" => "view",
    						"name" => "我的工作量",
    						"url" => "http://ncvteduwx.duapp.com/index.php/Home/Index/jsgzl"
    					),
    					array(
    						"type" => "view",
    						"name" => "我的课表",
    						"url" => "http://ncvteduwx.duapp.com/index.php/Home/Index/jrkb"
    					),
    					array(
    						"type" => "view",
    						"name" => "教学任务",
    						"url" => "http://ncvteduwx.duapp.com/index.php/Home/Index/jxrz"
    					),
    					array(
    						"type" => "view",
    						"name" => "请假审批",
    						"url" => "http://ncvteduwx.duapp.com/index.php/Home/Index/sp"
    					),
    					array(
    						"type" => "view",
    						"name" => "考勤",
    						"url" => "http://ncvteduwx.duapp.com/index.php/Home/Index/kqqk"
    					)
    				)
    			),
    			array(
    				"name" => "公共查询",
    				"sub_button" => array(
    					array(
    						"type" => "view",
    						"name" => "公共课查询",
    						"url" => "http://ncvteduwx.duapp.com/index.php/Home/Index/yxkc"
    					),
    					array(
    						"type" => "view",
    						"name" => "校历",
    						"url" => "http://ncvteduwx.duapp.com/index.php/Home/Index/xl"
    					),
    					array(
    						"type" => "view",
    						"name" => "教务通知",
    						"url" => "http://ncvteduwx.duapp.com/index.php/Home/Index/jwtz"
    					),
    					array(
    						"type" => "view",
    						"name" => "自习教室",
    						"url" => "http://ncvteduwx.duapp.com/index.php/Home/Index/zxjs"
    					)
    				)
    			)

    		);

		$wechat->getAccessToken();
		$res = $wechat->menuCreate($button);
		dump($res);
    }*/


}
