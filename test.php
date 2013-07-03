<?php
//一个实例，需要先到ge.tt.class.php填入配置信息
require_once('ge.tt.class.php');

$gett = new GettApi();
$accesstoken = $gett->GetAccesstoken(); //获取accesstoken

$info = $gett->GetInfo($accesstoken);   //获取用户基本信息
print_r($info);


$title = 'A new share';					//创建一个新分享
$share = $gett->CreateShare($accesstoken,$title);
$sharename = $share->sharename;


										//上传一张图片到上面的分享中
$imgurl = "http://www.baidu.com/img/bdlogo.gif";
$image = file_get_contents($imgurl);
file_put_contents('/temp' ,$image);
$file = realpath('/temp');
$filename = 'baidu.gif';

$result = $gett->UPLOAD($filename,$file,$sharename,$accesstoken);
print_r($result);//成功将返回：computer says yes 