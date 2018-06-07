<?php
/*
 * Founded in 2014-08-16
 * Author: hardlcp
 * e-mail:439677078@qq.com
 */
define('WEBROOT',dirname(__FILE__)); //定义网站根目录
define('CX_PATH',dirname(__FILE__).'/Collocation/');//注意目录后面加“/”

require(dirname(__FILE__).'/inc/config.php');//加载配置
require(CX_PATH.'core/cpApp.class.php');//加载应用控制类

$app=new cpApp($config);//实例化单一入口应用控制类
$app->run();//执行项目

?>