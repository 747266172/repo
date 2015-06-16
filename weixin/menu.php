<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include "wechat.class.php";
$options = array(
    'token' => 'nntcw', //填写你设定的key
    'appid' => 'wx9ef64ae2dfab83cd',
    'appsecret' => '4b3a410bcaf8828f70d6a1419e9e359a'
);
$weObj = new Wechat($options);

$newmenu = array(
    "button" =>
    array(
        array('type' => 'click', 'name' => '新闻动态', 'key' => 'MENU_NEWS'),
        array('type' => 'click', 'name' => '车位查询', 'key' => 'MENU_CARPORT'),
        array('type' => 'click', 'name' => '人员查询', 'key' => 'MENU_WORKER')
    )
);
$result = $weObj->createMenu($newmenu);

print_r($result);
