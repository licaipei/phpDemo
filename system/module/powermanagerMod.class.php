<?php
class powermanagerMod extends commonMod
{
	public function __construct()
	{
		parent::__construct();
	}
	public function powermenu(){
		$role_id = $_SESSION['role_id'];
		if($role_id==-1){
			$menus = $this->model->table('menus')->order("parent_id ASC,display_order ASC")->select();
			$list=$this->model->table('power')->where("`is_menu` = '1'")->order("menu_id ASC,display_order ASC")->select();
		}else{
			$info=$this->model->table('roles')->field('power,menus,name')->where("`role_id`='$role_id'")->find();
			if(!empty($info['menus'])) $menuids = rtrim($info['menus'],',');
			if(empty($menuids)) die("没有操作权限！请联系管理员分配。");
			$menus = $this->model->table('menus')->where("menu_id IN($menuids)")->order("parent_id ASC,display_order ASC")->select();
			if(!empty($info['power'])) $ids = rtrim($info['power'],',');
			if(!empty($ids)) $list=$this->model->table('functrl')->where("`id` IN($ids) AND `is_menu` = '1'")->order("menu_id ASC,display_order ASC")->select();
		}
		//操作
		if(!empty($list)) foreach ($list as $vo) {
			if($vo['menu_id']>0){
				$ctrltree[$vo['menu_id']][]=array('id'=>$vo['menu_id'].'_'.$vo['id'],'pId'=>$vo['menu_id'],'name'=>$vo['title'],'url'=>__APP__.'/'.$vo['module'].'/'.$vo['action'],'target'=>'rightFrame','icon'=>__PUBLIC__.'/images/list.gif');
			}
		}
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
	
		if(!empty($datas)) foreach ($datas as $vo) {
			if(!empty($ctrltree[$vo['menu_id']])) {
				$tree[]=array('id'=>$vo['menu_id'],'pId'=>$vo['parent_id'],'name'=>$vo['title'],'open'=>false);
				foreach ($ctrltree[$vo['menu_id']] as $subtree) $tree[]=$subtree;
			}else{
				$tree[]=array('id'=>$vo['menu_id'],'pId'=>$vo['parent_id'],'name'=>$vo['title'],'isParent'=>true,'icon'=>__PUBLIC__.'/images/leftico01.png');
			}
		}
		$this->assign('tree', json_encode($tree));
		print_r($tree);
	}
	/**
	 * @title 权限管理
	 * @remark 权限管理说明
	 * Enter description here ...
	 */
	public function index(){
		if(!$this->isPost()){
		    $menus = $this->model->table('menus')->order("parent_id ASC,display_order ASC")->select();
		    $datas = array();
    		if(!empty($menus)) foreach ($menus as &$result) {
    			$parents[$result['menu_id']] = $result['title'];
    			$datas[$result['menu_id']] = $result;
    			$datas[$result['menu_id']]['order'] = $result['display_order'];
    			if($result['parent_id']>0){
    			    $parents[$result['menu_id']] = $parents[$result['parent_id']] . ' &gt; ' . $result['title'];
    				$datas[$result['menu_id']]['path'] = $parents[$result['parent_id']] . ' &gt; ';
    				$newsort[] = $datas[$result['menu_id']]['order'] = $datas[$result['parent_id']]['order'];
    			}else{
    				$newsort[]=$result['display_order'];
    			}
    		}
    		unset($result);
    		//排序
    		if(!empty($newsort)) array_multisort($newsort,SORT_ASC,$datas);
		    
			$list=$this->model->table('power')->where("`ignore`='0'")->order("menu_id ASC,display_order ASC")->select();
			if(!empty($list)) foreach ($list as $vo) {
			    if($vo['menu_id']>0){
			        $ctrltree[$vo['menu_id']][]=array('id'=>$vo['menu_id'].'_'.$vo['id'],'pId'=>$vo['menu_id'],'name'=>$vo['title'],'menu'=>$vo['is_menu']);
			    }
			}
			
			if(!empty($datas)) foreach ($datas as $vo) {
		        $menu[$vo['menu_id']] = $vo['path'].$vo['title'];
		        if(!empty($ctrltree[$vo['menu_id']])) {
		            $tree[]=array('id'=>$vo['menu_id'],'pId'=>$vo['parent_id'],'name'=>$vo['title'],'open'=>true);
		            foreach ($ctrltree[$vo['menu_id']] as $subtree) $tree[]=$subtree;
		        }else{
		            $tree[]=array('id'=>$vo['menu_id'],'pId'=>$vo['parent_id'],'name'=>$vo['title'],'open'=>true);
		        }
		    }
		    $this->assign('menu', $menu);
			$this->assign('tree', json_encode($tree));
			
			$this->assign('title','权限管理');
			$this->assign('list',$list);
			$this->display('powermanager/index');
		}else{
		    
		}
	}
	
	/**
	 * @title 菜单分组
	 * @remark 添加/修改菜单分组
	 * 
	 */
	public function menus(){
		if($this->isPost()){
    		$data=array();
    		$data['title']=in($_POST['title']);
    		$data['parent_id']=intval($_POST['parent_id']);
    		$data['display_order']=intval($_POST['display_order']);
    		
    		if(empty($data['title'])) die(json_encode(array('error'=>1,'msg'=>'菜单分组名称不能为空')));
    		
    		if($this->model->table('menus')->data($data)->insert()) die(json_encode(array('error'=>0)));
    		else die(json_encode(array('error'=>1,'msg'=>'添加失败')));
		}else{
		    $list = $this->model->table('menus')->order("parent_id ASC,display_order ASC")->select();
		    $datas = array();
    		if(!empty($list)) foreach ($list as &$result) {
    			$parents[$result['menu_id']] = $result['title'];
    			$datas[$result['menu_id']] = $result;
    			$datas[$result['menu_id']]['order'] = $result['display_order'];
    			if($result['parent_id']>0){
    			    $parents[$result['menu_id']] = $parents[$result['parent_id']] . ' &gt; ' . $result['title'];
    				$datas[$result['menu_id']]['path'] = $parents[$result['parent_id']] . ' &gt; ';
    				$newsort[] = $datas[$result['menu_id']]['order'] = $datas[$result['parent_id']]['order'];
    			}else{
    				$newsort[]=$result['display_order'];
    			}
    		}
    		unset($result);
    		//排序
    		if(!empty($newsort)) array_multisort($newsort,SORT_ASC,$datas);
			
			$this->assign('title','操作菜单分组');
			$this->assign('list',$datas);
			$this->display('powermanager/menus');
		}
	}
	
	public function updatefunctrl(){
	    $files = glob(WEBROOT . '/system/module/*.php');
        //所有控制器
        foreach ($files as $file) {
    		if ( is_file($file) ) {   
    			require_once($file); 
    			$controller = str_replace('Mod.class.php', '', basename($file));
    			$data = array();
    			$dbfunctrl = array();
    			
        		$class = new ReflectionClass($controller.'Mod'); //建立实体类的反射类
        		$properties = $class->getMethods(ReflectionProperty::IS_PUBLIC);
        		$funlist = $this->model->table('power')->field('id,action')->where("module='{$controller}'")->select();
        		if(!empty($funlist)) foreach ($funlist as $vo)  $dbfunctrl[$vo['id']] = $vo['action'];
        		
        		foreach ($properties as $key => $property) {
        		    $_action = $property->getName();
            		$docblock = $property->getDocComment();//取方法注释
        		    preg_match('/\@title\s(.*?)\s\*/s', $docblock, $title);
        		    $title = trim($title[1]);
            		$ignore = empty($title)?1:0;
        		    if(strpos($_action, '__')!==0){//&&!empty($title)
        		        $data[] = $_action;
            		    preg_match("/\@remark\s(.*?)\s\*/s", $docblock, $matches); 
            		    $remark = trim($matches[1]);
        		        if(in_array($_action, $dbfunctrl)){//更新
        		            $upid = array_search($_action, $dbfunctrl);
        		            $upfunctrl['ignore'][$upid] = $ignore;
        		            $upfunctrl['title'][$upid] = $title;
        		            $upfunctrl['remark'][$upid] = $remark;
        		        }else{//新增
        		            $values[] = "('{$ignore}','{$title}','{$controller}','{$_action}','{$remark}')";
        		        }
        		    }
        		}
        		$delids=implode(',', array_flip(array_diff($dbfunctrl, $data)));
        		if(!empty($delids)) $delfunctrl[] = "DELETE FROM {pre}power WHERE `id` IN($delids)";
    		} 
        }
	    if(is_array($values)&&!empty($values)){//插入
            $inssql = "INSERT INTO {pre}power(`ignore`,`title`,`module`,`action`,`remark`) VALUES ";
	        $inssql.= implode(',', $values).';';
			unset($values);
			$this->model->query($inssql);
		}
	    if(is_array($upfunctrl)&&!empty($upfunctrl)){//更新
	        $ids = implode(',', array_keys($upfunctrl['title']));
            $upsql = "UPDATE {pre}power SET `ignore` = CASE id ";
            foreach ($upfunctrl['ignore'] as $id => $ignore) {
                $upsql .= sprintf("WHEN %d THEN %d ", $id, $ignore);
            }
            $upsql .= "END, `title` = CASE id ";
            foreach ($upfunctrl['title'] as $id => $title) {
                $upsql .= sprintf("WHEN %d THEN '%s' ", $id, $title);
            }
            $upsql .= "END, `remark` = CASE id ";
            foreach ($upfunctrl['remark'] as $id => $remark) {
                $upsql .= sprintf("WHEN %d THEN '%s' ", $id, $remark);
            }
            $upsql .= "END WHERE id IN ($ids)";

			unset($upfunctrl);
			$this->model->query($upsql);
		}
	    if(is_array($delfunctrl)&&!empty($delfunctrl)){//删除
	        $delsql.= implode(';', $delfunctrl).';';
			unset($delfunctrl);
			$this->model->query($delsql);
		}
							
		$this->success('更新成功',__URL__.'/index.html');
	}
	
	public function upfield(){
	    if($this->isPost()){
	        $id = intval($_POST['upkeyid']);
	        $field = trim($_POST['field']);
	        $value = trim($_POST['value']);
	        $upsql = "UPDATE {pre}power SET `{$field}`='{$value}' WHERE `id`='{$id}'";
	        if($this->model->query($upsql)) die(json_encode(array('error'=>0)));
	    }
	}
	
    public function upmenufield(){
	    if($this->isPost()){
	        $id = intval($_POST['upkeyid']);
	        $field = trim($_POST['field']);
	        $value = trim($_POST['value']);
	        $upsql = "UPDATE {pre}menus SET `{$field}`='{$value}' WHERE `menu_id`='{$id}'";
	        if($this->model->query($upsql)) die(json_encode(array('error'=>0)));
	    }
	}
	/**
	 * @title 菜单删除
	 * @remark 删除菜单分组
	 */
	public function delmenu(){
	    if($this->isPost()){
	        $id = intval($_POST['menu_id']);
	       
	        $upsql = "UPDATE {pre}power SET `menu_id` = NULL WHERE `menu_id`='{$id}'";
	        $this->model->query($upsql);
	        if($this->model->table('menus')->where("`menu_id`='{$id}'")->delete()) die(json_encode(array('error'=>0)));
	    }
	}
}
?>