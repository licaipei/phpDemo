<?php
/*
 * Founded in 2016-08-16
 * Author: hardlcp
 * e-mail:464018128@qq.com
 */
define('WEBROOT',realpath(dirname(__FILE__).'/../'));
define('CX_PATH',WEBROOT.'/Collocation/');//注意目录后面加“/”

require(WEBROOT.'/inc/config.php');//加载配置
require(CX_PATH.'core/cpApp.class.php');//加载应用控制类

$config['URL_ACTION_DEPR']='-';//操作分隔符，一般不需要修改
$config['URL_PARAM_DEPR']='-';//参数分隔符，一般不需要修改
$config['URL_HTML_SUFFIX']='.html';//伪静态后缀设置，，例如 .html ，一般不需要修改
		
$config['DB_CACHE_ON']=false;//后台不生成数据库缓存
$config['HTML_CACHE_ON']=false;//后台不生成静态页面
$config['URL_REWRITE_ON']=false;//关闭重写
$config['LOG_ON']=false;//是否开启出错信息保存到文件，true开启，false不开启
$config['TPL_CACHE_ON']=false;//是否开启模板缓存，true开启,false不开启

$app=new cpApp($config);//实例化单一入口应用控制类
$app->run();//执行项目

?>
