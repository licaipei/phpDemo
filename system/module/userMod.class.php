<?php
class userMod extends commonMod
{
	public function __construct()
	{
		parent::__construct();
        $website=$this->model->table('site')->order('id DESC')->limit($limit)->select();
        $this->assign('website',$website);
	}
	/**
	 * @title 账户管理
	 * @remark 管理后台帐号
	 */
	public function index()
	{
		$listRows=20;//每页显示的信息条数
		$cur_page=isset($_GET['page'])?intval($_GET['page']):1;//获取当前分页
		$cur_page=$cur_page<1?1:$cur_page;//当前页小于1，则设当前页为1
		$limit_start=($cur_page-1)*$listRows;
		$limit=$limit_start.','.$listRows;
		$userid=$_SESSION['admin_uid'];
		$where="id!='1' and id!='$userid' and type='0'";
		$condition=array();
		
		$url=__URL__.'/index.html';//分页基准网址
		
		//获取行数
		$count=$this->model->table('admin')->where($where)->count();	
		$list=$this->model->table('admin')->field('id,group_id,username,realname,lastlogin_time,lastlogin_ip,islock')->where($where)->order('id DESC')->limit($limit)->select();
		//角色组
		$roles = $this->model->table('roles')->field('role_id,name')->select();
		if(!empty($roles)) foreach ($roles as $vo) {
		   $role[$vo['role_id']] = $vo['name'];
		}
	    $this->assign('role', $role);
	    if(!empty($list)) foreach ($list as &$vo){
			$vo['role'] = $role[$vo['group_id']];
			$vo['status'] = ($vo['islock'])?'<span style="color:red">锁定</span>':'<span style="color:green">正常</span>';
			$vo['lastlogin_time'] = ($vo['lastlogin_time']>0)?date('Y-m-d H:i:s',$vo['lastlogin_time']):'<span class="red">暂无记录</span>';
			$vo['lastlogin_ip'] = !empty($vo['lastlogin_ip'])?$vo['lastlogin_ip']:'<span class="red">暂无记录</span>';
			$vo['action'][] = '<a href="javascript:void(0)" data-id="'.$vo['id'].'" data-value="'.(!$vo['islock']).'" data-op="lock">'.($vo['islock']?'解锁':'锁定').'</a>';
			$vo['action'][] = '<a href="'.__URL__.'/admin-'.$vo['id'].'.html">修改</a>';
			$vo['action'][] = '<a href="javascript:void(0)" data-id="'.$vo['id'].'" data-op="delete">删除</a>';
		}
		$this->assign('title','账户列表');
		$this->assign('list',$list);
		$this->assign('page',$this->page($url,$count,$listRows));
		$this->display('user/index');
	}
	/**
	 * @title 账户修改
	 * @remark 账户修改操作
	 */
	public function edit(){
		if($this->isPost()){
    		//获取数据
    		$data=array();
    		
    		$id=intval($_POST['id']);
    		$data['username']=in($_POST['username']);
    		$data['realname']=in($_POST['realname']);
    		$data['group_id']=intval($_POST['group_id']);
    		$data['departqx']=$_POST['departqx'];
    		if($id==0&&empty($_POST['password'])) $this->error('账户密码不能为空');
    		if(!empty($_POST['password'])) $data['password']=md5($_POST['password']);
    		//if(empty($_POST['purview'])) $data['manageqx']='no';
    		//else $data['manageqx']=implode(",",$_POST['purview']);
            if(!empty($_POST['tongjiqx'])){
                $data['tongjiqx']=implode(",",$_POST['tongjiqx']);
            }else{
                $data['tongjiqx']='';
            }

            //验证数据
    		if(empty($data['username'])) $this->error('账户名不能为空');
    		//数据库操作
    		if($id>0){
        		if($this->model->table('admin')->data($data)->where("`id`='{$id}'")->update()){
        			$this->inslog('修改账户“<font color="#ff0000">'.$_POST['username'].'</font>”。');
        			$this->success('修改成功');
        		}else{
        			$this->error('修改失败');
        		}
    		}else{
    		    if($this->model->table('admin')->data($data)->insert()){
        			$this->inslog('添加账户“<font color="#ff0000">'.$_POST['username'].'</font>”。');
			        $this->success('账户添加成功');
        		}else{
        			$this->error('账户添加失败');
        		}
    		}
		}else{
			$id=intval($_GET[0]);
			if($id>0){
    			$condition['id']=$id;
    			$info=$this->model->table('admin')->field('id,group_id,departqx,tongjiqx,username,realname,islock')->where($condition)->find();
			}
			if(empty($info)) $this->assign('title', '添加管理帐号');
			else $this->assign('title', '修改管理帐号');
			/*$sort=$this->model->field('dpid,dpname')->table('department')->select();
			$this->assign('sort',$sort);*/
			
			$list = $this->model->table('roles')->select();
			$this->assign('list',$list);
			$this->assign('info',$info);
			$this->display('user/edit');
		}
		
	}
    /**
     * @title 账户锁定
     * @remark 账户锁定操作
     */
	public function islock(){
	    if($this->isPost()){
	        $id = intval($_POST['id']);
	        $value = trim($_POST['lock']);
	        if($value==0){
                $data['islock']= 1;
            }else {
                $data['islock'] = 0;
            }
	        $condition['id']=$id;
	        $info=$this->model->table('admin')->field('islock,username')->where($condition)->find();
	        if($this->model->table('admin')->data($data)->where($condition)->update()){
    			if($value==0){
                    $this->inslog('冻结账户“<font color="#ff0000">' . $info['username'] . '</font>”。');
                    echo '冻结成功';
                }
    			else {
                    $this->inslog('对账户“<font color="#ff0000">'.$info['username'].'</font>”进行了解冻操作。');
                    echo '解冻成功';
                }
    		}else{
    			echo '冻结操作失败';
    		}
	    }
	}
	/**
	 * @title 账户删除
	 * @remark 账户删除操作
	 */
	public function del(){
	    if($this->isPost()){
	        $id = intval($_POST['id']);
	       
	        $info=$this->model->table('admin')->field('username')->where("`id`='$id'")->find();
    		if($this->model->table('admin')->where("`id`='$id'")->delete()){
    		    //清除关联
    		    //$this->model->table('log')->where("`adminid`='$id'")->delete();
    		    //$this->model->table('dblog')->where("`admin_id`='$id'")->delete();//保留操作日志
    			$this->inslog('删除管理帐号“<font color="#ff0000">'.$info['username'].':id-'.$id.'</font>”。');
    			echo '删除成功'; return;
    		}else{
			    echo '删除失败'; return;
    		}
	    }
    }
	
}
?>