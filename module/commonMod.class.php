<?php
/*
 * Founded in 2013-08-16
 * Author: hardlcp
 * e-mail:464018128@qq.com
 */
class commonMod{
	protected $model = NULL; //数据库模型
	protected $layout = NULL; //布局视图
	protected $config = array();
	static $user;
	static $vipuser;
	public $loginfo;
	public $viplog;
	private $_data = array();

	protected function init(){}

	public function __construct(){
		session_start();//开启session
		global $config;
		$this->config = $config;
		$this->model = self::initModel( $this->config );
		$this->init();
	}

	//初始化模型
	static public function initModel($config){
		static $model = NULL;
		if( empty($model) ){
			$model = new cpModel($config);
		}
		return $model;
	}

	public function __get($name){
		return isset( $this->_data[$name] ) ? $this->_data[$name] : NULL;
	}

	public function __set($name, $value){
		$this->_data[$name] = $value;
	}

	//获取模板对象
	public function view(){
		static $view = NULL;
		if( empty($view) ){
			$view = new cpTemplate( $this->config );
		}
		return $view;
	}

	//模板赋值
	protected function assign($name, $value){
		return $this->view()->assign($name, $value);
	}

	//模板显示
	protected function display($tpl = '', $return = false, $is_tpl = true ){
		if( $is_tpl ){
			$tpl = empty($tpl) ? $_GET['_module'] . '_'. $_GET['_action'] : $tpl;
			if( $is_tpl && $this->layout ){
				$this->__template_file = $tpl;
				$tpl = $this->layout;
			}
		}
		$this->view()->assign( $this->_data );
		$this->assign('sys', $this->config);//全局配置输出 用于网站配置参数的直接调取
		return $this->view()->display($tpl, $return, $is_tpl);
	}

	protected function set_cookie($var, $value = '', $time = 0) {
		$time = $time > 0 ? $time : 0;
		$port = $_SERVER['SERVER_PORT'] == '443' ? 1 : 0;
		$var = $this->config['COOKIE_PRE'].$var;
		return setcookie($var, $value, $time, $this->config['COOKIE_PATH'], $this->config['COOKIE_RANGE'], $port);
	}

	protected function get_cookie($var) {
		$var = $this->config['COOKIE_PRE'].$var;
		return isset($_COOKIE[$var]) ? $_COOKIE[$var] : '';
	}

	
	//判断是否是数据提交
	protected function isPost(){
		return $_SERVER['REQUEST_METHOD'] == 'POST';
	}

	//操作成功之后跳转,默认三秒钟跳转
	protected function success($msg,$url=NULL,$waitSecond=3)
	{
		if($url==NULL) $url=__URL__;
		$this->assign('message',$msg);
		$this->assign('url',$url);
		$this->assign('waitSecond',$waitSecond);
		$this->display('success');
		exit;
	}
	//操作出错,默认三秒钟跳转
	protected function error($msg,$url=NULL,$waitSecond=3)
	{
		if($url==NULL) $url=__URL__;
		$this->assign('message',$msg);
		$this->assign('url',$url);
		$this->assign('waitSecond',$waitSecond);
		$this->display('error');
		exit;
	}
	
	//直接跳转
	protected function redirect( $url, $code=302) {
		header('location:' . $url, true, $code);
		exit;
	}

	//删除文件
	protected function delfile($delarr,$path)
	{
		if(!empty($delarr)){
			foreach ($delarr as $value){
				$filepath=$path.$value;
				if(file_exists($filepath)) unlink($filepath);
			}
		}
	}
	private function alert($msg) {
		header('Content-type: text/html; charset=UTF-8');
		$json = new Services_JSON();
		echo $json->encode(array('error' => 1, 'message' => $msg));
		exit;
	}
	//弹出信息
	protected function alertbox($msg,$url=NULL)
	{
		header("Content-type: text/html; charset=utf-8");
		$msg="alert('$msg');";
		if($url==NULL) echo "<script>$msg history.go(-1);</script>";
		else echo "<script>$msg window.location.href='$url';</script>";
		exit;
	}
	private function mkdirs($dir){
		if(!is_dir($dir)){
			if(!$this->mkdirs(dirname($dir))){
				return false;
			}
			if(!mkdir($dir,0777)){
				return false;
			}
		}
		return true;
	}
	public function getSortMode($id)
	{
		$info=$this->model->table('category')->field('id,mx')->where("id='$id'")->find();
		if(!empty($info)){
			$name=$info['mx'];
		}
		return $name;
	}
	public function getSortName($id)
	{
		$info=$this->model->table('category')->field('id,name')->where("id='$id'")->find();
		if(!empty($info)){
			$name=$info['name'];
		}
		return $name;
	}
	public function getSortTopName($id)
	{
		$info=$this->model->table('category')->field('id,pid,name')->where("id='$id'")->find();
		if(!empty($info)){
			$pid=$info['pid'];
			$pinfo=$this->model->table('category')->field('id,name')->where("id='$pid'")->find();
			$name=$pinfo['name'];
		}
		return $name;
	}
	protected function webnav($selid='')
	{
		 $webstr[]='<li><a href="'.__APP__.'/">首页</a></li>';
		 $webstr[]='<li><a href="'.__APP__.'/news/index-1.html">政务信息</a></li>';
		 $webstr[]='<li><a href="'.__APP__.'/about/index-1.html">组织机构</a></li>';
		 $webstr[]='<li><a href="'.__APP__.'/news/index-3.html">政策法规</a></li>';
		 $webstr[]='<li><a href="'.__APP__.'/news/index-4.html">服务指南</a></li>';
		 $webstr[]='<li><a href="'.__APP__.'/news/index-6.html">规划公示</a></li>';
		 $webstr[]='<li><a href="'.__APP__.'/news/index-17.html">经营城市</a></li>';
		 $webstr[]='<li><a href="'.__APP__.'/news/index-22.html">城市管理</a></li>';
		 $webstr[]='<li><a href="'.__ROOT__.'/system">广告审批</a></li>';
		 $webstr[]='<li><a href="'.__APP__.'/board">全民参与</a></li>';

		 $selmenu[]='<li><a href="'.__APP__.'/" class="hover">首页</a></li>';
		 $selmenu[]='<li><a href="'.__APP__.'/news/index-1.html" class="hover">政务信息</a></li>';
		 $selmenu[]='<li><a href="'.__APP__.'/about/index-1.html" class="hover">组织机构</a></li>';
		 $selmenu[]='<li><a href="'.__APP__.'/news/index-3.html" class="hover">政策法规</a></li>';
		 $selmenu[]='<li><a href="'.__APP__.'/news/index-4.html" class="hover">服务指南</a></li>';
		 $selmenu[]='<li><a href="'.__APP__.'/news/index-6.html" class="hover">规划公示</a></li>';
		 $selmenu[]='<li><a href="'.__APP__.'/news/index-17.html" class="hover">经营城市</a></li>';
		 $selmenu[]='<li><a href="'.__APP__.'/news/index-22.html" class="hover">城市管理</a></li>';
		 $selmenu[]='<li><a href="'.__ROOT__.'/system" class="hover">广告审批</a></li>';
		 $selmenu[]='<li><a href="'.__APP__.'/board" class="hover">全民参与</a></li>';
        
        $webstr[$selid]=$selmenu[$selid];
        return implode(' ',$webstr);
	}
  public function getWeixinUserInfo($openid,$type)
  {
    /*
     * @type 0昵称1性别2城市3头像
     */
    $options = array(
              'token'=>$this->config['wxtoken'],
              'appid'=>$this->config['wxappid'],
              'appsecret'=>$this->config['wxappsecret'],
          );
          $weObj = new Wechat($options);
    $re=$weObj->getUserInfo($openid);
    if($type==3){
      $arr=$re[headimgurl];
    }elseif($type==2){
      $arr=$re[country].'-'.$re[province].'-'.$re[city];
    }elseif($type==1){
      $sex=$re[sex];
      if($sex==1){
        $arr='男';
      }else{
        $arr='女';
      }
    }else $arr=$re[nickname];
    return $arr;
  }
	public function getWxinfo($openid,$type){
		$info=$this->model->table('wxuser')->field('head,petname')->where("openid='$openid'")->find();
		if($type==1){
			return $info['head'];	
		}else{
			return $info['petname'];	
		}
	
	}
}