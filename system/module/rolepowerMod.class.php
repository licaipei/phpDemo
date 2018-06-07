<?php
class rolepowerMod extends commonMod
{
	public function __construct()
	{
		parent::__construct();
	}
	/**
	 * @title 角色管理
	 * @remark 添加配置角色权限
	 */
	public function index(){
	    $this->assign('title','角色管理');
	    $list = $this->model->table('roles')->select();
	    $this->assign('list', $list);
	    $this->display('rolepower/index');
	}
	public function role(){
	    if($this->isPost()){
    		$data=array();
    		$role_id=intval($_POST['role_id']);
    		$data['name']=in($_POST['name']);
    		$data['remark']=in($_POST['remark']);
    		$data['power']=in($_POST['power']);
    		$data['powertit']=in($_POST['powertit']);
    		$data['menus']=in($_POST['menus']);
    		
    		if(empty($data['name'])) $this->error('角色名称不能为空');
    		if(empty($data['power'])) $this->error('未配置权限');
    		
    		if(empty($role_id)){
    			if($this->model->table('roles')->data($data)->insert()){
    				$this->inslog('添加角色“<font color="#ff0000">'.$data['name'].'</font>”。');
    				$this->success('添加成功','index.html');
    			}
    			else
    				$this->error('添加失败');
    		}else{
    			if($this->model->table('roles')->data($data)->where("`role_id`='".$role_id."'")->update()){
    				$this->inslog('修改角色“<font color="#ff0000">'.$data['name'].'</font>”权限。');
    				$this->success('修改成功','index.html');
    			}
    			else
    				$this->error('修改失败');
    		}
	    }else{
			$role_id=intval($_GET[0]);
			$selpower = array();
			if($role_id>0){
				$info=$this->model->table('roles')->where("`role_id`='$role_id'")->find();
				if(!empty($info['power'])) $selpower = explode(',', rtrim($info['power'],','));
				$this->assign('info',$info);
				$this->assign('title','角色修改');
			}else{
			    $this->assign('title','角色添加');
			}
			$menus = $this->model->table('menus')->order("parent_id ASC,display_order ASC")->select();
		    $datas = array();
    		if(!empty($menus)) foreach ($menus as &$result) {
    			$parents[$result['menu_id']] = $result['menu_id'];
    			$datas[$result['menu_id']] = $result;
    			$datas[$result['menu_id']]['order'] = $result['display_order'];
    			if($result['parent_id']>0){
    			    $parents[$result['menu_id']] = $parents[$result['parent_id']] . '_' . $result['menu_id'];
    				$datas[$result['menu_id']]['path'] = $parents[$result['parent_id']] . '_';
    				$newsort[] = $datas[$result['menu_id']]['order'] = $datas[$result['parent_id']]['order'];
    			}else{
    				$newsort[]=$result['display_order'];
    			}
    		}
    		unset($result);
    		//排序
    		if(!empty($newsort)) array_multisort($newsort,SORT_ASC,$datas);
		    
			$list=$this->model->table('power')->order("menu_id ASC,display_order ASC")->select();
			if(!empty($list)) foreach ($list as $vo) {
			    if($vo['menu_id']>0){
			        $ctrltree[$vo['menu_id']][]=array('id'=>$vo['menu_id'].'_'.$vo['id'],'pId'=>$vo['menu_id'],'name'=>$vo['title'],'checked'=>in_array($vo['id'], $selpower));
			    }
			}
			if(!empty($datas)) foreach ($datas as $vo) {
		        if(!empty($ctrltree[$vo['menu_id']])) {
		            $tree[]=array('id'=>$vo['menu_id'],'pId'=>$vo['parent_id'],'name'=>$vo['title'],'open'=>true);
		            foreach ($ctrltree[$vo['menu_id']] as $subtree){
		                $subtree['id']=$vo['path'].$subtree['id'];//加上菜单ID路径
		                $tree[]=$subtree;
		            }
		        }else{
		            $tree[]=array('id'=>$vo['menu_id'],'pId'=>$vo['parent_id'],'name'=>$vo['title'],'isParent'=>true,'open'=>true);
		        }
		    }
			$this->assign('tree', json_encode($tree));
			
			$this->assign('list',$list);
			$this->display('rolepower/role');
		}
	}
	
	public function showpowertree(){
	    $role_id=intval($_GET[0]);
		
		if($role_id>0){
			$info=$this->model->table('roles')->field('power,menus,name')->where("`role_id`='$role_id'")->find();
			if(!empty($info['menus'])) $menuids = rtrim($info['menus'],',');
			if(!empty($info['power'])) $ids = rtrim($info['power'],',');
			$this->assign('title',$info['name'].' - 权限');
		    if(empty($menuids)) $this->error("没有设置权限！请修改角色配置权限。");
    	    $menus = $this->model->table('menus')->where("menu_id IN($menuids)")->order("parent_id ASC,display_order ASC")->select();
    	    $datas = array();
    		if(!empty($menus)) foreach ($menus as &$result) {
    			//$parents[$result['menu_id']] = $result['title'];
    			$datas[$result['menu_id']] = $result;
    			$datas[$result['menu_id']]['order'] = $result['display_order'];
    			if($result['parent_id']>0){
    			    //$parents[$result['menu_id']] = $parents[$result['parent_id']] . ' &gt; ' . $result['title'];
    				//$datas[$result['menu_id']]['path'] = $parents[$result['parent_id']] . ' &gt; ';
    				$newsort[] = $datas[$result['menu_id']]['order'] = $datas[$result['parent_id']]['order'];
    			}else{
    				$newsort[]=$result['display_order'];
    			}
    		}
    		unset($result);
    		//排序
    		if(!empty($newsort)) array_multisort($newsort,SORT_ASC,$datas);
    	    
    		$list=$this->model->table('power')->where("id IN($ids)")->order("menu_id ASC,display_order ASC")->select();
    		if(!empty($list)) foreach ($list as $vo) {
    		    if($vo['menu_id']>0){
    		        $ctrltree[$vo['menu_id']][]=array('id'=>$vo['menu_id'].'_'.$vo['id'],'pId'=>$vo['menu_id'],'name'=>$vo['title']);
    		    }
    		}
    		
    		if(!empty($datas)) foreach ($datas as $vo) {
    	        if(!empty($ctrltree[$vo['menu_id']])) {
    	            $tree[]=array('id'=>$vo['menu_id'],'pId'=>$vo['parent_id'],'name'=>$vo['title'],'open'=>true);
    	            foreach ($ctrltree[$vo['menu_id']] as $subtree) $tree[]=$subtree;
    	        }else{
    	            $tree[]=array('id'=>$vo['menu_id'],'pId'=>$vo['parent_id'],'name'=>$vo['title'],'isParent'=>true,'open'=>true);
    	        }
    	    }
    		$this->assign('tree', json_encode($tree));
    		$this->display('rolepower/showpowertree');
		}
	}
	
    public function upfield(){
	    if($this->isPost()){
	        $id = intval($_POST['upkeyid']);
	        $field = trim($_POST['field']);
	        $value = trim($_POST['value']);
	        $upsql = "UPDATE {pre}roles SET `{$field}`='{$value}' WHERE `role_id`='{$id}'";
	        if($this->model->query($upsql)) die(json_encode(array('error'=>0)));
	    }
	}
	
	public function delrole(){
	        $id = intval($_GET[0]);
	       
	        $info=$this->model->table('roles')->field('name')->where("`role_id`='$id'")->find();
    		if($this->model->table('roles')->where("`role_id`='$id'")->delete()){
    		    //清除关联用户
    		    $this->model->table('admin')->data(array('group_id'=>0))->where("`group_id`='$id'")->update();
    			$this->inslog('删除角色“<font color="#ff0000">'.$info['name'].'</font>”。');
    			$this->success('删除角色成功！');
    		}else{
			    $this->error('删除角色失败！');
    		}
	}
	
}
?>