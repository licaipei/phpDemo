<?php
class logMod extends commonMod
{
	public function __construct()
	{
		parent::__construct();
	}
	/**
	 * @title 操作日志管理
	 * @remark 系统操作日志
	 */
	public function index()
    {
        $userid=$_SESSION['admin_uid'];
        $groupid=$_SESSION['role_id'];
        $this->assign('groupid',$groupid);
        $urole = $this->model->table('roles')->where("role_id='$groupid'")->find();
        $powertit=explode(',',rtrim($urole['powertit'],','));
        $this->assign('powertit',$powertit);
		$listRows=16;
		$where="";
		$condition=array();
		$url=__URL__.'/index.html';
		$page=new Page();
		$cur_page=$page->getCurPage($url);
		$limit_start=($cur_page-1)*$listRows;
		$limit=$limit_start.','.$listRows;
		
		$userid=$_SESSION['admin_uid'];
    	$qx=$_SESSION['admin_manageqx'];
    	$userlist=$this->model->table('admin')->field('id,username')->where("id!='1'")->select();
    	$this->assign('userlist', $userlist); 
		$user=$_GET['user'];
		$this->assign('user',$user);
		if($user){
			$url.='?user='.$user;
			$where='where adminid like "'.$user.'%"';
            $contwhere='adminid like "'.$user.'%"';
			$this->assign('user', $user);
		}
		if($_SESSION['role_id']==-1){
    		$count=$this->model->table('log')->where($contwhere)->count();	
			$sql="SELECT id,adminname,logtext,ip,addtime FROM {$this->model->pre}log {$where} ORDER BY id desc LIMIT {$limit}";
    	}
    	else{
	    	$count=$this->model->table('log')->WHERE("adminid='$userid'")->count();	
			$sql="SELECT id,adminname,logtext,ip,addtime FROM {$this->model->pre}log WHERE adminid='$userid' ORDER BY id desc LIMIT {$limit}";
    		
    	}
		$list=$this->model->query($sql);
		$this->assign('list',$list);
		$this->assign('qx',$qx);
		$this->assign('userid',$userid);
		$this->assign('groupid',$_SESSION['role_id']);
		$this->assign('page',$page->show($url,$count,$listRows));
		$this->display('log/index');

    	
    }
	/**
	 * @title 操作日志删除
	 * @remark 操作日志删除
	 */
    
    public function del()
    {
    	header('Access-Control-Allow-Origin:*');
		if(empty($_POST['del_id']))echo '请选择要删除的内容'; 
		$del_id=implode(",",array_filter($_POST['del_id']));//array_filter()函数的作用是去除数组中的空值
		if($del_id!=""){     	
			if($this->model->table('log')->where("id in ($del_id)")->delete()){
				$this->inslog('删除系统日志！');
				echo '删除成功';
			}
			else echo '删除失败';			
		}else{ 
			echo '请选择要删除的内容'; 
		} 
	}
    /**
	 * @title 操作日志查询
	 * @remark 操作日志查询
	 */
	public function search()
	{
		$userid=$_SESSION['admin_uid'];
		$groupid=$_SESSION['role_id'];
        if($groupid==-1){
            $logdel=1;
            $logsearch=1;
        }
        if($groupid!=-1) {
            $urole = $this->model->table('roles')->where("role_id='$groupid'")->find();
            $urole = $this->model->table('roles')->where("role_id='$groupid'")->find();
            $powerarr=explode(',',$urole['power']);
            if(in_array(41,$powerarr,TRUE)){
                $logdel=1;
            }else{
                $logdel=0;
            }
            if(in_array(42,$powerarr,TRUE)){
                $logsearch=1;
            }else{
                $logsearch=0;
            }
        }
        $this->assign('logdel',$logdel);
        $this->assign('logsearch',$logsearch);
		$keyword=in($_GET['keywords']);
        $qx=$_SESSION['admin_manageqx'];
        $this->assign('qx', $qx);
        $listRows=18;
        $url='index.html?keyword='.(urlencode($keyword));
        $page=new Page();
        $cur_page=$page->getCurPage($url);
        $limit_start=($cur_page-1)*$listRows;
        $limit=$limit_start.','.$listRows;
        $keywords=stripsearchkey($keyword);
        $conwhere="logtext like '%$keywords%'";
        if($_SESSION['role_id']==-1){
            $userlist=$this->model->table('admin')->field('id,username')->where("id!='11' and id!='1'")->select();
            $this->assign('userlist', $userlist);
            $count=$this->model->table('log')->where($conwhere)->count();
            $list=$this->model->table('log')->where($conwhere)->order("addtime desc")->limit($limit)->select();
        }else{
            $count=$this->model->table('log')->where($conwhere." and adminid='$userid'")->count();
            $list=$this->model->table('log')->where($conwhere." and adminid='$userid'")->order("addtime desc")->limit($limit)->select();
        }
        $this->inslog('查询关键词为<font color="#ff0000">'.$keywords.'</font>的操作日志信息！');
        $this->assign('list',$list);
        $this->assign('public',__PUBLIC__);
        $this->assign('root',__ROOT__);
        $this->assign('url',__URL__);
        $this->assign('page',$page->show($url,$count,$listRows));
        $this->display('log/index');
	}
	
}
?>