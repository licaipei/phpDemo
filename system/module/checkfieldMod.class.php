<?php
class checkfieldMod extends commonMod
{
	public function __construct()
	{
		parent::__construct();
	}
	/**
	 * @title 考核字段管理
	 * @remark 考核字段管理
	 */
	public function index()
	{
		$userid=$_SESSION['admin_uid'];
		mysql_query("SET NAMES 'utf8'"); 
        $result=mysql_query("SHOW FULL FIELDS FROM {$this->model->pre}examine");        
		$fields=array();         
		while ($rows=mysql_fetch_array($result)){             
			$fields[]=$rows;    
		}  
		//print_r($fields);       
        $scores=$this->model->table('score')->select();
        if(!empty($scores)) foreach ($scores as $vo) $scores[$vo['fields']]=$vo['scores'];
        $this->assign('scores', $scores);

        $this->assign('list',$fields);
		$this->display('checkfield/index');
	}
	/**
	 * @title 考核字段添加
	 * @remark 考核字段添加
	 */
	public function add(){
		if($this->isPost()){
    		//获取数据
            $data['fields']=$field=$_POST['field'];
    		$type=$_POST['type'];
            $comment=$_POST['comment'];
            $data['scores']=$scores=$_POST['scores'];
            //echo $comment;
    		//验证数据
    		
    		if(empty($field)) $this->error('字段名不能为空');
    		if(empty($type)) $this->error('请选择字段类型');
            if(empty($comment)) $this->error('字段注释不能为空');
            //if(empty($scores)) $this->error('考核分值不能为空');

            if(preg_match("/[\x7f-\xff]/", $newfield)) $this->error('不能使用含有中文的字段名');
    		$sql="ALTER TABLE zt_examine ADD `$field` $type NOT NULL COMMENT '$comment'";
    		//数据库操作
		   	if($this->model->query($sql)){
		   	    $this->model->table('score')->data($data)->insert();
        		$this->inslog('添加考核字段“<font color="#ff0000">'.$field.'</font>”的信息。');
			    $this->success('考核字段信息添加成功');
        	}else{
        		$this->error('考核字段信息添加失败');
        	}
    	}else{
			$this->display('checkfield/edit');
		}
		
	}
	/**
	 * @title 考核字段修改
	 * @remark 考核字段修改
	 */
	public function edit(){
		if($this->isPost()){
    		//获取数据
            $oldfield=$_POST['oldfield'];
            $data['fields']=$newfield=$_POST['field'];
    		$type=$_POST['type'];
    		$comment=$_POST['comment'];
            $data['scores']=$scores=$_POST['scores'];
    		//验证数据
    		if(empty($oldfield)) $this->error('参数传递出错');
    		if(empty($newfield)) $this->error('字段名不能为空');
    		if(empty($type)) $this->error('请选择字段类型'); 
    		if(empty($comment)) $this->error('字段注释不能为空'); 
    		if(preg_match("/[\x7f-\xff]/", $newfield)) $this->error('不能使用含有中文的字段名'); 
    		$sql="ALTER TABLE zt_examine CHANGE `$oldfield` `$newfield` $type NOT NULL COMMENT '$comment'";
    		//数据库操作
		   	if($this->model->query($sql)){
                $this->model->table('score')->data($data)->where("fields='$oldfield'")->update();
    		 	$this->inslog('修改考核字段“<font color="#ff0000">'.$id.'</font>”的信息。');
    			$this->success('信息修改成功');
    		}else{
    			$this->error('修改失败');
    		}
    		
 		}else{
			$field=urldecode($_GET['field']);
			$type=urldecode($_GET['type']);
			$comment=urldecode($_GET['comment']);
            $info=$this->model->table('score')->where("fields='$field'")->find();
            //print_r($info);
            $this->assign('info',$info);
			$this->assign('field', $field);
			$this->assign('type', $type);
			$this->assign('comment', $comment);
			if(!empty($field)){
				if(empty($field)) $this->error('参数传递出错');
			}
			$this->display('checkfield/edit');
		}
		
	}
	/**
	 * @title 考核字段删除
	 * @remark 考核字段删除
	 */
	public function del(){
	    if($this->isPost()){
	        $fieldname = $_POST['fieldname'];
	        $sql="ALTER TABLE zt_examine DROP $fieldname ";
    		if($this->model->query($sql)){
                $this->model->table('score')->where("`fields`='$fieldname'")->delete();
    			$this->inslog('删除考核字段“<font color="#ff0000">'.$fieldname.'</font>”。');
    			echo '删除成功'; return;
    		}else{
			    echo '删除失败！'; return;
    		}
	    }
    }
	
}
?>