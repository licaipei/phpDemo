<?php
class workersMod extends commonMod
{
	public function __construct()
	{
		parent::__construct();
        $result=mysql_query("SHOW FULL FIELDS FROM {$this->model->pre}examine");        
		$fields=array();         
		while ($rows=mysql_fetch_array($result)){             
			$fields[]=$rows;    
		}  
		//print_r($fields);       
		$this->assign('fields',$fields);
		$guize=$this->model->table('gongshi')->order('id asc')->select();
		$this->assign('guize',$guize);
	}
	/**
	 * @title 员工信息管理
	 * @remark 员工信息管理
	 */
	public function index()
	{
		$listRows=16;
		$condition=array();
		$url=__URL__.'/index.html';
		$page=new Page();
		$cur_page=$page->getCurPage($url);
		$limit_start=($cur_page-1)*$listRows;
		$limit=$limit_start.','.$listRows;
		
		$userid=$_SESSION['admin_uid'];
		$where="id!='1' and id!='$userid' and type='1'";
		$condition=array();
		//获取行数
		$count=$this->model->table('admin')->where($where)->count();	
		$list=$this->model->table('admin')->where($where)->order('id DESC')->limit($limit)->select();
		if(!empty($list)){
			foreach ($list as $key => &$val){
                $list[$key]['dapartment']=$this->getDepartMent($val['departid'], 'name');
                $list[$key]['guize']=$this->getGuize($val['gongshiID']);
			}
		}
		$this->assign('list',$list);
		$this->assign('page',$page->show($url,$count,$listRows));
		$this->display('workers/index');
	}
	/**
	 * @title 员工信息添加
	 * @remark 员工信息添加
	 */
	public function add(){
		if($this->isPost()){
    		//获取数据
    		$data=array();
    		
    		$id=intval($_POST['id']);
    		$data['type']=1;
    		$data['departid']=$departid=intval($_POST['departid']);
    		$info=$this->model->table('department')->where("id='$departid'")->find();
    		$data['departpath']=$info['parth'];
   			$data['realname']=$realname=in($_POST['realname']);
            $data['nation']=in($_POST['nation']);
            $data['gongshiID']=intval($_POST['gongshiID']);
    		$data['borth']=strtotime($_POST['borth']);
    		$data['photo']=in($_POST['photo']);
    		$data['address']=in($_POST['address']);
    		$data['entrydate']=strtotime($_POST['entrydate']);
    		$data['leavedate']=strtotime($_POST['leavedate']);
    		$data['username']=Pinyin::ChineseToPinyin($realname,false, true);
     		$data['group_id']=intval($_POST['group_id']);
    		$data['departqx']='all';
    		$data['manageqx']='all';
    		if(!empty($_POST['kaoheqx'])){
    			$data['kaoheqx']=implode(",",$_POST['kaoheqx']);
    		}else{
    			$data['kaoheqx']='';
    		}
    		if(!empty($_POST['password'])){
    			$data['password']=md5($_POST['password']);
    		}else{
    			$str='111111';
    			$data['password']=md5($str);
    		}
    		//验证数据
    		if(empty($data['realname'])) $this->error('姓名不能为空');
    		//数据库操作
		   	if($this->model->table('admin')->data($data)->insert()){
        		$this->inslog('添加员工“<font color="#ff0000">'.$_POST['realname'].'</font>”的信息。');
			    $this->success('员工信息添加成功');
        	}else{
        		$this->error('员工信息添加失败');
        	}
    	}else{
			$sort=$this->model->table('department')->select();
			$this->assign('sort',$sort);
			$list = $this->model->table('roles')->select();
			$this->assign('list',$list);
			$this->display('workers/edit');
		}
		
	}
	/**
	 * @title 员工信息修改
	 * @remark 员工信息修改
	 */
	public function edit(){
		if($this->isPost()){
    		//获取数据
    		$data=array();
    		$id=intval($_POST['id']);
    		$data['departid']=$departid=intval($_POST['departid']);
    		$info=$this->model->table('department')->where("id='$departid'")->find();
    		$data['departpath']=$info['parth'];
   			$data['realname']=$realname=in($_POST['realname']);
    		$data['nation']=in($_POST['nation']);
            $data['gongshiID']=intval($_POST['gongshiID']);
            $data['borth']=strtotime($_POST['borth']);
    		$data['photo']=in($_POST['photo']);
    		$data['address']=in($_POST['address']);
    		$data['entrydate']=strtotime($_POST['entrydate']);
    		$data['leavedate']=strtotime($_POST['leavedate']);
    		$data['username']=Pinyin::ChineseToPinyin($realname,false, true);
     		$data['group_id']=intval($_POST['group_id']);
    		$data['departqx']=$_POST['departqx'];
    		if(!empty($_POST['kaoheqx'])){
    			$data['kaoheqx']=implode(",",$_POST['kaoheqx']);
    		}else{
    			$data['kaoheqx']='';
    		}
    		
    		//验证数据
    		if(empty($data['realname'])) $this->error('姓名不能为空');
    		//数据库操作
    		if($this->model->table('admin')->data($data)->where("`id`='{$id}'")->update()){
    			$this->inslog('修改员工“<font color="#ff0000">'.$_POST['realname'].'</font>”的信息。');
    			$this->success('信息修改成功');
    		}else{
    			$this->error('修改失败');
    		}
    		
 		}else{
			$id=intval($_GET[0]);
			if($id>0){
    			$condition['id']=$id;
    			$info=$this->model->table('admin')->where($condition)->find();
			}
			$sort=$this->model->table('department')->select();
			$this->assign('sort',$sort);
			
			$list = $this->model->table('roles')->select();
			$this->assign('list',$list);
			$this->assign('info',$info);
			$this->display('workers/edit');
		}
		
	}
	/**
     * @title 员工信息冻结
     * @remark 员工信息冻结
     */
	public function islock(){
	    if($this->isPost()){
	        $id = intval($_POST['id']);
	        $value = trim($_POST['value']);
	        $data['islock']=$value;
	        $condition['id']=$id;
	        $info=$this->model->table('admin')->field('islock,realname')->where($condition)->find();
	        if($this->model->table('admin')->data($data)->where($condition)->update()){
    			if($value==0) $this->inslog('对员工“<font color="#ff0000">'.$info['realname'].'</font>”信心进行了冻结解除操作。');
    			else $this->inslog('对员工“<font color="#ff0000">'.$info['realname'].'</font>”信息进行了冻结操作。');
    			echo '设置成功'; return;
    		}else{
    			echo '冻结失败'; return;
    		}
	    }
	}
	/**
	 * @title 员工信息删除
	 * @remark 员工信息删除
	 */
	public function del(){
	    if($this->isPost()){
	        $id = intval($_POST['id']);
	       
	        $info=$this->model->table('admin')->where("`id`='$id'")->find();
    		if($this->model->table('admin')->where("`id`='$id'")->delete()){
    		    //清除关联
    		    //$this->model->table('log')->where("`adminid`='$id'")->delete();
    		    //$this->model->table('dblog')->where("`admin_id`='$id'")->delete();//保留操作日志
    			$this->inslog('删除员工信息“<font color="#ff0000">'.$info['realname'].'</font>”。');
    			echo '删除成功'; return;
    		}else{
			    echo '删除失败！'; return;
    		}
	    }
    }
	
}
?>