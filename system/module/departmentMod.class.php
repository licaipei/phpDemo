<?php
class departmentMod extends commonMod
{
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * @title 部门信息管理
	 * @remark 部门信息管理
	 */
	public function index()
	{
		$this->assign('cat',$this->getCat());
		$this->display('department/index');
	}
	
	/**
	 * @title 部门信息添加
	 * @remark 部门信息添加
	 */
	public function add()
	{	
		if(empty($_POST['do']))
		{
			$pid=intval($_GET[0]);
			$this->assign('pid',$pid);
			$this->assign('cat',$this->getCat());//获取格式化后的分类树			
			$this->display('department/edit');
			return;
		}
		$data=array();
		$data['pid']=intval($_POST['pid']);
		$parentid=intval($_POST['pid']);
		if($parentid==0){
			$data['parth']=',0';
			$data['deep']=1;
		}else{
			$parent=$this->model->table('department')->field('id,parth,deep')->where("id='{$parentid}'")->find();
			$data['parth']=$parent['parth'].','.$parent['id'];
			$data['deep']=$parent['deep']+1;
		}
		
		$data['name']=in($_POST['name']);
		$data['description']=in($_POST['description']);
		$data['uptime']=time();
		
		if(empty($data['name']))
		{
			$this->error('部门名称不能为空');
		}
		if($this->model->table('department')->data($data)->insert()){
			$this->inslog('添加部门信息“<font color="#ff0000">'.$_POST['name'].'</font>”。');
			$this->success('信息添加成功',__URL__);
		}
		else
			$this->error('信息添加失败');
	}
	/**
	 * @title 部门信息编辑
	 * @remark 部门信息编辑
	 */
	public function edit()
	{
		if(empty($_POST['do']))
		{
			$id=intval($_GET[0]);
			if(empty($id))
			{
				$this->error('参数传递错误');
			}		
			$condition['id']=$id;
			$info=$this->model->table('department')->where($condition)->find();//获取当前分类信息
			if(empty($info))
			{
				$this->error('该部门不存在或者已被删除');
			}
			$this->assign('info',$info);
			$this->assign('cat',$this->getCat());//获取格式化后的分类树
			$this->display('department/edit');
			return;
		}
		
		$data=array();
		$condition=array();
		$data['id']=intval($_POST['id']);
		$data['pid']=intval($_POST['pid']);
		$parentid=intval($_POST['pid']);
		if($parentid==0){
		$data['parth']=',0';
		$data['deep']=1;
		}else{
			$parent=$this->model->table('department')->field('id,parth,deep')->where("id='{$parentid}'")->find();
			$data['parth']=$parent['parth'].','.$parent['id'];
			$data['deep']=$parent['deep']+1;
		}
		
		$data['name']=in($_POST['name']);
		$data['description']=in($_POST['description']);
		$data['uptime']=time();
		
		if(empty($data['name']))
		{
			$this->error('部门名称不能为空');
		}

		if($data['pid']==$data['id'])
		{
			$this->error('不可以将当前部门设置为上一级部门');	
		}
		$cat=$this->getCat($data['id']);//获取$data['id']的所有下级栏目
		if(!empty($cat))
		{
			foreach($cat as $vo)
			{
				if($data['pid']==$vo['id'])
				{
					$this->error('不可以将上级部门移动到子部门');
				}
			}
		}
		
		$condition['id']=$data['id'];
		if($this->model->table('department')->data($data)->where($condition)->update()){
			$this->inslog('修改部门信息“<font color="#ff0000">'.$_POST['name'].'</font>”。');
			$this->success('信息修改成功',__URL__);
		}
		else
			$this->error('信息修改失败');
	}
	/**
	 * @title 部门信息删除
	 * @remark 部门信息删除
	 */
	public function del()
	{
		$id=intval($_POST['id']);
		if(empty($id))
		{
			echo '参数传递错误'; return;
		}
		$condition=array();
		//检测子栏目是否存在
		$condition['pid']=$id;
		if($this->model->table('department')->where($condition)->count())
		{
			echo '请先删除该部门下面的子部门'; return;	
		}
		unset($condition);//将上一次查询条件清空
		$condition['id']=$id;
		$info=$this->model->table('department')->field('id,name')->where("id='$id'")->find();
		if($this->model->table('department')->where($condition)->delete()){
			$this->inslog('删除部门信息“<font color="#ff0000">'.$info['name'].'</font>”。');
			echo '删除成功'; return;
		}
		else
			echo '删除失败';
	}
	
	//获取分类树，$id，分类id,$id=0，获取所有分类结构树
	public function getCat($id=0)
	{
		require(CX_PATH.'lib/Category.class.php');//导入Category.class.php无限分类
		//查询分类信息
		$data=$this->model->field('id,pid,name,uptime')->table('department')->select();		
		//array('id','pid','name','cname'),字段映射，格式化后的分类名次问cname
		$cat=new Category(array('id','pid','name','cname','uptime'));//初始化无限分类
		
		return $cat->getTree($data,$id);//获取分类数据树结构
	}
}
?>