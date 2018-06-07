<?php
class indexMod extends commonMod
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{

        $userid=$_SESSION['admin_uid'];
		$info=$this->model->table('admin')->where("id='$userid'")->find();
        $groupid=$_SESSION['role_id'];
        $this->assign('groupid',$groupid);
        $urole = $this->model->table('roles')->where("role_id='$groupid'")->find();
        $powertit=explode(',',rtrim($urole['powertit'],','));
        //echo $powertit;
        $this->assign('powertit',$powertit);
		$this->assign('departnums', $this->model->table('department')->count());
		$this->assign('workernums', $this->model->table('admin')->where("type='1'")->count());
		if($_SESSION['admin_type']==1){
			$userid=$_SESSION['admin_uid'];
			$this->assign('examnums', $this->model->table('examine')->where("userid='$userid'")->count());
		}else{
			$this->assign('examnums', $this->model->table('examine')->count());
		}
		$tongjiqx=$info['tongjiqx'];
		if(empty($tongjiqx)){
			$this->assign('visitnums', $this->model->table('visit')->count());
		}else{
			$this->assign('visitnums',$this->model->table('visit')->where("domainid IN($tongjiqx)")->count());
		}
		$this->display('index');
	}

	public function login()
	{
		if($this->isPost()){
			$username=in($_POST['username']);
			$password=md5($_POST['password']);
	
			if(empty($username)){
				echo '请输入用户名'; return;
			}
			if(empty($_POST['password'])){
				echo '请输入密码'; return;
			}
			//if(empty($_POST['checkcode'])){ echo '请输入验证码' return;
			//if($_POST['checkcode']!=$_SESSION['code']){ echo '验证码错误，请重新输入'; return;
	
			if($this->_login($username,$password)){
				echo '系统登录成功'; return;
			}
			else{
				echo '用户名或密码错误，请重新输入'; return;
			}
		}else{
			$this->display('login');
			return;
		}
	}

	private function _login($username,$password)
	{
		$condition=array();
		$condition['username']=$username;
		$condition['password']=$password;
		$field='id,group_id,type,manageqx,departqx,kaoheqx,username,realname,password,islock';
		$user_info=$this->model->table('admin')->field($field)->where($condition)->find();
		//用户名密码正确且没有锁定
		if(($user_info['password']==$password)&&($user_info['islock']==0))
		{
			//更新帐号信息
			$data=array();
			$data['lastlogin_time']=time();
			$data['lastlogin_ip']=get_client_ip();
			$this->model->table('admin')->data($data)->where($condition)->update();
						
			//设置登录信息
			$_SESSION['admin_uid']=$user_info['id'];
			$_SESSION['role_id']=$user_info['group_id'];
			$_SESSION['admin_manageqx']=$user_info['manageqx'];
            $_SESSION['departqx']=$user_info['departqx'];
            $_SESSION['kaoheqx']=$user_info['kaoheqx'];
			$_SESSION['admin_username']=$user_info['username'];
			$_SESSION['admin_realname']=$user_info['realname'];
			$_SESSION['admin_type']=$user_info['type'];
			$_SESSION['__ROOT__']=__ROOT__;
			$this->inslog('成功登录系统！');
			return true;
		}
		return false;
	}

	public function logout()
	{   
		$this->inslog('成功退出系统！');
		unset($_SESSION['admin_uid']);
		unset($_SESSION['role_id']);
		unset($_SESSION['admin_manageqx']);
        unset($_SESSION['departqx']);
        unset($_SESSION['kaoheqx']);
		unset($_SESSION['admin_username']);
		unset($_SESSION['admin_realname']);
		unset($_SESSION['admin_type']);
		unset($_SESSION['__ROOT__']);
		
		$this->success('您已成功退出系统',__APP__.'/index/login.html');
	}
	
	/**
	 * @title 密码修改
	 * @remark 密码修改
	 */
	public function editpass()
	{
		if($this->isPost()){
			if(!empty($_SESSION['admin_uid'])){
				$data=array();
				$oldpass=$_POST['oldpass'];
				$newpass=$_POST['newpass'];
				$password=$_POST['password'];
				$data['password']=md5($password);
				
				if(empty($oldpass)) $this->error('旧密码不能留空！',__URL__.'/editpass.html');
				if(empty($newpass)||empty($password)||($newpass!=$password)) $this->error('两次新密码输入不一致或为空！',__URL__.'/editpass.html');
				
				$user_info=$this->model->table('admin')->field('password')->where("`id`='".$_SESSION['admin_uid']."'")->find();
				if($user_info['password']!=md5($oldpass)) $this->error('原密码不正确！',__URL__.'/editpass.html');
										
				if($this->model->table('admin')->data($data)->where("`id`='".$_SESSION['admin_uid']."'")->update()){
					$this->inslog('进行密码修改！');
					$this->success('修改成功！',__APP__.'/index/logout',0);
				}else{
					$this->error('修改失败！');
				}
			}else{
				$this->error('非法操作！');
			}
		}else{
			$this->assign('title', '修改密码');
			$this->display('user/editpass');
			return;
				
		}
	}

	public function checkcode()
	{
		if($_POST['checkcode']==$_SESSION['code']) echo 1;
		else echo 0;
	}

	public function code()
	{
		require_once(CX_PATH.'lib/Image.class.php');
		Image::vCode();
	}
}

?>