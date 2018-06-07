<?php
class siteMod extends commonMod{
	public function __construct(){
		parent::__construct();
	}
	/**
	 * @title 统计站点管理
	 * @remark 统计站点管理
	 */
	public function index(){
		$listRows=16;
		$condition=array();
		$url=__URL__.'/index.html';
		$page=new Page();
		$cur_page=$page->getCurPage($url);
		$limit_start=($cur_page-1)*$listRows;
		$limit=$limit_start.','.$listRows;
		
		$where="";
		$condition=array();
		//获取行数
		$count=$this->model->table('site')->where($where)->count();
		$list=$this->model->table('site')->where($where)->order('id DESC')->limit($limit)->select();
		$this->assign('list',$list);
		$this->assign('page',$page->show($url,$count,$listRows));
		$this->display('site/index');
		
	}
	/**
	 * @title 站点信息添加
	 * @remark 站点信息添加
	 */
	public function add(){
		if($this->isPost()){
			//获取数据
			$data=array();
			$data['website']=trim($_POST['website']);
			$data['domain']=trim($_POST['domain']);
			if($_POST['iswww']==1){
				$data['domain1']='www.'.trim($_POST['domain']);
			}
			$data['uptime']=time();
			//验证数据
			if(empty($data['website'])) $this->error('站点名称不能为空');
			if(empty($data['domain'])) $this->error('域名不能为空');
			//数据库操作
			if($this->model->table('site')->data($data)->insert()){
				$this->inslog('添加站点信息“<font color="#ff0000">'.$_POST['webiste'].'</font>”的考核信息。');
				$this->success('站点信息添加成功');
			}else{
				$this->error('站点信息添加失败');
			}
		}else{
			$this->display('site/edit');
		}
	}
	/**
	 * @title 站点信息修改
	 * @remark 站点信息修改
	 */
	public function edit(){
		if($this->isPost()){
			//获取数据
			//获取数据
			$data=array();
			$id=$id=intval($_POST['id']);
			$data['website']=trim($_POST['website']);
			$data['domain']=trim($_POST['domain']);
			if($_POST['iswww']==1){
				$data['domain1']='www.'.trim($_POST['domain']);
			}
			$data['uptime']=time();
		
			//验证数据
			if(empty($data['website'])) $this->error('站点名称不能为空');
			if(empty($data['domain'])) $this->error('域名不能为空');
			//数据库操作
			$info=$this->model->table('site')->where("id='$id'")->find();
			if($this->model->table('site')->data($data)->where("`id`='{$id}'")->update()){
				$this->inslog('修改“<font color="#ff0000">'.$_POST['ewbsite'].'</font>”的站点信息。');
				$this->success('站点信息修改成功');
			}else{
				$this->error('站点信息修改失败');
			}
		
		}else{
			$id=intval($_GET[0]);
			if($id>0){
				$condition['id']=$id;
				$info=$this->model->table('site')->where($condition)->find();
			}
			$this->assign('info',$info);
			$this->display('site/edit');
		}
	}
	/**
	 * @title 站点信息删除
	 * @remark 站点信息删除
	 */
	public function del(){
	    $id = intval($_GET[0]);
	    $info=$this->model->table('site')->where("id='$id'")->find();
   		if($this->model->table('site')->where("id='$id'")->delete()){
    			$this->inslog('删除统计站点“<font color="#ff0000">'.$info['website'].'</font>”。');
    			$this->success('站点信息删除成功');
    		}else{
			   $this->error('站点信息删除失败');
    		}
	    }
	/**
	 * @title 站点开启关闭
	 * @remark 站点开启关闭
	 */
	public function play()
	{
		if(empty($_GET[0])) $this->error('参数传递错误');
		
		if(in_array($_GET[1],array(0,1)))
		{
			$id=intval($_GET[0]);
			$state=intval($_GET[1])>0?0:1;
			$data['status']=$state;
			$condition['id']=intval($_GET[0]);
            $info=$this->model->table('site')->field('id,website')->where("id='$id'")->find();
			if($this->model->table('site')->data($data)->where($condition)->update())
			{
				$this->inslog('对站点“<font color="#ff0000">'.$info['website'].'</font>”进行关闭开启操作。');
				$this->success('操作成功');
			}
			else $this->error('操作失败');
		}else $this->error('非法操作');
	}
}