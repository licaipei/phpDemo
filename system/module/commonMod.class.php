<?php
/*
 * Founded in 2017-03-16
 * Author: hardlcp
 * e-mail:464018128@qq.com
 */
class commonMod{
	protected $model = NULL; //数据库模型
	protected $layout = NULL; //布局视图
	protected $config = array();
	private $_data = array();
	static $adminqx=array();

	//初始化权限检测
	protected function init(){
	    //忽略权限
	    $ignorelist=$this->model->table('power')->field('module,action')->where("`ignore`='1'")->select();
	    if(!empty($ignorelist)) foreach ($ignorelist as $vo) {
	        $ignore[]=$vo['module'].'/'.$vo['action'];
	    }
	    //取用户权限
	    $role_id=$_SESSION['role_id'];
	    if(empty($role_id) and(cpApp::$action!='login')){
	    	header("Location: ".__APP__.'/index/login.html');
	    	exit;
	    }elseif($role_id==-1){//超级管理员，取所有操作
	        $powerlist=$this->model->table('power')->field('module,action')->where("`ignore`='0'")->select();
    	    if(!empty($powerlist)) foreach ($powerlist as $vo) {
    	        $ignore[]=$vo['module'].'/'.$vo['action'];
    	    }
	    }else{
    	    $power = $this->model->table('roles')->field('power')->where("`role_id`='$role_id'")->find();
    	    $userpowerids = rtrim($power['power'],',');
    	    if(!empty($userpowerids)){
    	        $powerlist=$this->model->table('power')->field('module,action')->where("`ignore`='0' AND id IN({$userpowerids})")->select();
        	    if(!empty($powerlist)) foreach ($powerlist as $vo) {
        	        $ignore[]=$vo['module'].'/'.$vo['action'];
        	    }
    	    }
	    }
	    $controller = cpApp::$module.'/'.cpApp::$action;
	    if(!in_array($controller, $ignore)) $this->error("没有操作仅限！",__APP__.'/index/index.html');
	}

	public function __construct(){
		session_start();//开启session
		global $config;
		$this->config = $config;
		$this->model = self::initModel( $this->config );
		$this->init();
		if(!empty($_SESSION['admin_uid'])){
		    $role_id = $_SESSION['role_id'];
		    if($role_id==-1){
				$qxmenu=$this->model->table('menus')->order("display_order ASC")->select();
				if(!empty($qxmenu)) foreach ($qxmenu as $vo){
					$menuid=$vo['menu_id'];
					$childmenu[$menuid]=$this->model->table('power')->where("menu_id='$menuid' and is_menu='1'")->order('display_order ASC')->select();
				}
		    }else{
		    	$rolesinfo=$this->model->table('roles')->field('power,powertit,menus,name')->where("`role_id`='$role_id'")->find();
	    		if(!empty($rolesinfo['menus'])) $menuids = rtrim($rolesinfo['menus'],',');
	    		$this->assign('menuids', $menuids);
	    	    if(empty($menuids)) die("没有操作权限！请联系管理员分配。");
	    		if(!empty($rolesinfo['power'])) $ids = rtrim($rolesinfo['power'],',');
	    		if(!empty($rolesinfo['powertit'])) $idsname = rtrim($rolesinfo['powertit'],',');
	    		$this->assign('powerids', $ids);
	    		$this->assign('idsname', $idsname);
	    		$qxmenu = $this->model->table('menus')->where("menu_id IN($menuids)")->order("display_order ASC")->select();
		    	if(!empty($qxmenu)) foreach ($qxmenu as $vo){
					$menuid=$vo['menu_id'];
					$childmenu[$menuid]=$this->model->table('power')->where("menu_id='$menuid' and id IN($ids) and is_menu='1'")->order('display_order ASC')->select();
				}
				
		    }
		    $leftmenu=getAllMenuShow($qxmenu);
			$this->assign('leftmenu',$leftmenu);
			$this->assign('childmenu',$childmenu);
			//print_r($childmenu);
			$adminid=$_SESSION['admin_uid'];
			$adminname=$_SESSION['admin_username'];
			$admintruename=$_SESSION['admin_realname'];
			$this->assign('adminid', $adminid);
			$this->assign('adminname', $adminname);
			$this->assign('admintruename', $admintruename);
	        $sysmodel=$_GET['_module'];
	        $sysaction=$_GET['_action'];
	    	$menuhover=$this->model->table('power')->field('module')->where("module='$sysmodel' and is_menu='1'")->find();
	    	$this->assign('onmenu',$menuhover['menu_id']);
	    	$this->assign('onpowername',$menuhover['title']);
	        //echo $sysmodel.'-'.$sysaction;
	        
	        $this->assign('sysmodel', $sysmodel);
	        $this->assign('sysaction', $sysaction);
	        $this->assign('menuhover', $menuhover);
		}
	}

	//初始化模型
	static protected function initModel($config){
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
	protected function view(){
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

	//判断是否是数据提交
	protected function isPost(){
		return $_SERVER['REQUEST_METHOD'] == 'POST';
	}

	//写日志
	protected function inslog($msg)
	{
		if(empty($_SESSION['admin_uid'])||empty($_SESSION['admin_username'])||empty($_SESSION['admin_manageqx']))
		{
			$this->redirect(__APP__.'/index/login.html');
		}else{
			$data=array();
			$data['adminid']=$_SESSION['admin_uid'];
			$data['adminname']=$_SESSION['admin_username'];
			$data['logtext']=$msg;
			$data['addtime']=time();
			$data['ip']=get_client_ip();
			if($this->model->table('log')->data($data)->insert()){
				return true;
			}else
			return false;
		}
	}
	
	//模块执行时间
    protected function runtime(){
        $GLOBALS['_endTime'] = microtime(true);
        $runTime = number_format($GLOBALS['_endTime'] - $GLOBALS['_startTime'], 4);
        echo $runTime;
    }
	protected function success($msg,$url=NULL,$waitSecond=2)
	{
		if($url==NULL) $url=__URL__;
		$this->assign('message',$msg);
		$this->assign('url',$url);
		$this->assign('waitSecond',$waitSecond);
		$this->display('success');
		exit;
	}
	protected function error($msg,$url=NULL,$waitSecond=2)
	{
		if($url==NULL) $url=__URL__;
		$this->assign('message',$msg);
		$this->assign('url',$url);
		$this->assign('waitSecond',$waitSecond);
		$this->display('error');
		exit;
	}

	//分页 $url:基准网址，$totalRows: $listRows列表每页显示行数$rollPage 分页栏每页显示的页数
	protected  function  page($url,$totalRows,$listRows=10,$rollPage=15)
	{
		require_once(CX_PATH.'lib/page.class.php');
		$page=new page();
		return $page->show($url,$totalRows,$listRows,$rollPage,3);
	}
	protected  function  ajaxpage($url,$totalRows,$ajaxpage=false,$ajaxaction='',$listRows=10,$rollPage=8)
	{
		require_once(CX_PATH.'lib/Page.class.php');
		$page=new page();
		if($ajaxpage) $page->open_ajax($ajaxaction);
		return $page->show($url,$totalRows,$listRows,$rollPage,3);
	}

	//直接跳转
	protected function redirect($url,$errorcode=301) {
		header('location:'.$url,true,$errorcode);
		exit;
	}

	//弹出信息
	protected function alert($msg,$url=NULL)
	{
		header("Content-type: text/html; charset=utf-8");
		$msg="alert('$msg');";
		if($url==NULL) echo "<script>$msg history.go(-1);</script>";
		else echo "<script>$msg window.location.href='$url';</script>";
		exit;
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
  public function getMember($uid,$type){
  	$info=$this->model->table('admin')->where("id='$uid'")->find();
  	return $info[$type];
  }
  
  public function getDepartMent($id,$filed){
  	 $info=$this->model->table('department')->where("id='$id'")->find();
  	 return $info[$filed];
  }
    public function getScore($fields){
        $info=$this->model->table('score')->where("fields='$fields'")->find();
        return $info['scores'];
    }
    public function getGuize($id){
        $info=$this->model->table('gongshi')->where("id='$id'")->find();
        return $info['name'];
    }
    public function getVisitHits($id,$day){
        $sql="select SUM(hits) AS hitnums from zt_visit_hits WHERE visitid='$id' and times='$day'";
        $hits=$this->model->query($sql);
        return $hits[0]['hitnums'];
    }
    public function getVisitAllHits($id){
        $sql="select SUM(hits) AS hitnums from zt_visit_hits WHERE visitid='$id'";
        $hits=$this->model->query($sql);
        return $hits[0]['hitnums'];
    }
    public function getVisitIps($id,$day){
        $sql="select COUNT(ip) AS ipnums from zt_visit_ip WHERE visitid='$id' and times='$day'";
        $ips=$this->model->query($sql);
        return $ips[0]['ipnums'];
    }
    public function getVisitAllIps($id){
        $sql="select COUNT(ip) AS ipnums from zt_visit_ip WHERE visitid='$id'";
        $ips=$this->model->query($sql);
        return $ips[0]['ipnums'];
    }
    public function getHits($id,$type,$day){
        if(!empty($day)){
            $istime=$day;
        }else{
            $istime=time();
        }
        $thetime=strtotime(date('Y-m-d',$istime));
        $thetime1=$thetime-86400;
        $thetime2=$thetime+86400;
        $info = $this->model->table('site_visit')->where("id='$id'")->find();
        $url = $info['url'];
        if($type=='2'){
            $hits = $this->model->query("SELECT SUM(hits) AS hits FROM {$this->model->pre}site_visit WHERE url='$url' AND uptime>='$thetime1' and uptime<='$thetime'");
        }elseif($type=='1'){
            $hits = $this->model->query("SELECT SUM(hits) AS hits FROM {$this->model->pre}site_visit WHERE url='$url' AND uptime>='$thetime' and uptime<='$thetime2'");
        }else {
            $hits = $this->model->query("SELECT SUM(hits) AS hits FROM {$this->model->pre}site_visit WHERE url='$url' AND uptime<='$thetime'");
        }
        return $hits[0]['hits'];
    }
    public function getIpNums($id,$type,$day){
        if(!empty($day)){
            $istime=$day;
        }else{
            $istime=time();
        }
        $thetime=strtotime(date('Y-m-d',$istime));
        $thetime1=$thetime-86400;
        $thetime2=$thetime+86400;

        $info=$this->model->table('site_visit')->where("id='$id'")->find();
        $url=$info['url'];
        if($type=='2'){
            $count = count($this->model->query("SELECT ip,url FROM {$this->model->pre}site_visit WHERE ip!='' AND url='$url' AND uptime>='$thetime1' and uptime<='$thetime' GROUP BY ip"));
        }elseif($type=='1'){
            $count = count($this->model->query("SELECT ip,url FROM {$this->model->pre}site_visit WHERE ip!='' AND url='$url' AND uptime>='$thetime' and uptime<='$thetime2' GROUP BY ip"));
        }else {
            $count = count($this->model->query("SELECT ip,url FROM {$this->model->pre}site_visit WHERE ip!='' AND url='$url' AND uptime<='$thetime' GROUP BY ip"));
        }
        return $count-1;
    }
	public function getallhits($url)
	{
        $hits = $this->model->query("SELECT SUM(hits) AS hits FROM {$this->model->pre}site_visit WHERE url='$url'");
		return $hits[0]['hits'];
	}
	public function getallIps($url){
		$count = count($this->model->query("SELECT ip,url FROM {$this->model->pre}site_visit WHERE ip!='' AND url='$url' GROUP BY ip"));
		return $count-1;
	}
	

}