<?php
class examineMod extends commonMod{
	public function __construct()
    {
        parent::__construct();
        $adminid=$_SESSION['admin_uid'];
        $admininfo=$this->model->table('admin')->where("id='$adminid'")->find();
        $this->assign('kaoheqx',$admininfo['kaoheqx']);
        mysql_query("SET NAMES 'utf8'");
        $checkfieldresult = mysql_query("SHOW FULL FIELDS FROM {$this->model->pre}examine");
        $checkfield = array();
        while ($rows = mysql_fetch_array($checkfieldresult)) {
            $checkfield[] = $rows;

        }
        $this->assign('checkfield',$checkfield);
        $this->assign('nowmoth',date('Y-m',time()));
    }
    /**
     * @title 考核信息管理
     * @remark 考核信息管理
     */
    public function index(){
        $adminid=$_SESSION['admin_uid'];
        if(!empty($adminid)){
            $uinfo=$this->model->table('admin')->where("id='$adminid'")->find();
            $this->assign('utype', $uinfo['type']);
        }
        $listRows=16;
        $url=__URL__.'/index.html';
        $page=new Page();
        $cur_page=$page->getCurPage($url);
        $limit_start=($cur_page-1)*$listRows;
        $limit=$limit_start.','.$listRows;

        $dataarr=explode( '-', $_GET['entrydate']);
        $nowtime=$dataarr[0].'-'.$dataarr[1];
        $entrydate=strtotime($nowtime,TRUE);
        if(!empty($entrydate)){
            $this->assign('entrydate',$entrydate);
        }
        $departid=$_GET['department'];
        $workerid=$_GET['workers'];

        $userid=$_SESSION['admin_uid'];
        if($uinfo['type']==1){
            if(!empty($entrydate)){
                $where="userid='$userid' and entrydate='$entrydate'";
            }else{
                $where="userid='$userid'";
            }
            $count=$this->model->table('examine')->where($where)->count();
            $list=$this->model->table('examine')->where($where)->order('id DESC')->limit($limit)->select();
        }else{
            $nowdate=date('Y-m',time());
            $nowdatearr=explode( '-', $nowdate);
            $year=$nowdatearr[0];
            $morth=$nowdatearr[1];
            $nowtime=$year.'-'.$morth;
            //echo $nowtime;
            $nowriqi=strtotime($nowtime,TRUE);


            if(!empty($entrydate) and !empty($departid) and !empty($workerid)){
                $where="entrydate='$entrydate' and departid='$departid' and userid='$workerid'";
            }elseif(!empty($entrydate) and !empty($departid) and empty($workerid)){
                $where="entrydate='$entrydate' and departid='$departid'";
            }elseif(!empty($entrydate) and empty($departid) and !empty($workerid)){
                $where="entrydate='$entrydate' and userid='$workerid'";
            }elseif(empty($entrydate) and !empty($departid) and !empty($workerid)){
                $where="departid='$departid' and userid='$workerid'";
            }elseif(!empty($entrydate) and empty($departid) and empty($workerid)){
                $where="entrydate='$entrydate'";
            }elseif(empty($entrydate) and !empty($departid) and empty($workerid)){
                $where="departid='$departid'";
            }elseif(empty($entrydate) and empty($departid) and !empty($workerid)){
                $where="userid='$workerid'";
            }else{
                $where="entrydate='$nowriqi'";
            }

            $sql="SELECT * FROM {$this->model->pre}examine GROUP BY userid";
            $count=count($this->model->query($sql));
            $list=$this->model->table('examine')->where($where)->group("userid")->order('id DESC')->limit($limit)->select();
        }

        if(!empty($list)){
            foreach ($list as $key => &$val){
                $userid=$val['userid'];
                $admin=$this->model->table('admin')->where("id='$userid'")->find();
                $gongshiid=$admin['gongshiID'];
                $gsinfo=$this->model->table('gongshi')->where("id='$gongshiid'")->find();
                $usergongshi=$gsinfo['gongshi'];
                $list[$key]['username']=$this->getMember($val['userid'], 'realname');
                $list[$key]['dapartment']=$this->getDepartMent($val['departid'], 'name');
                $list[$key]['thismoth']=date('Y-m',$val['entrydate']);

                $list[$key]['score']=round(eval("return $usergongshi;"));

            }
        }
        if(!empty($list)) foreach ($list as $vo){
            $userid=$vo['userid'];
            $admin=$this->model->table('admin')->where("id='$userid'")->find();
            $gongshiid=$admin['gongshiID'];
            $gsinfo=$this->model->table('gongshi')->where("id='$gongshiid'")->find();
            $usergongshi=$gsinfo['gongshi'];
            //$kaoheqx[$userid]=$this->model->table('admin')->where("id='$userid'")->find();
            //$this->assign('kaoheqx',$kaoheqx);
            $ulist[$userid]=$this->model->table('examine')->where("userid='$userid' and $where")->order('id DESC')->select();
            if(!empty($ulist[$userid])){
                foreach ($ulist[$userid] as $key => &$val){
                    $ulist[$userid][$key]['username']=$this->getMember($val['userid'], 'realname');
                    $ulist[$userid][$key]['dapartment']=$this->getDepartMent($val['departid'], 'name');
                    $ulist[$userid][$key]['thismoth']=date('Y-m',$val['entrydate']);

                    $ulist[$userid][$key]['score']=round(eval("return $usergongshi;"));

                }
            }
        }
        $this->assign('list',$list);
        $this->assign('ulist',$ulist);
        $worker=$this->model->table('admin')->where("type='1'")->order('id DESC')->select();
        $this->assign('worker', $worker);
        $depart=$this->getCat();
        $this->assign('depart', $depart);
        $this->assign('page',$page->show($url,$count,$listRows));
        if($uinfo['type']==1){
            $this->display('examine/index');
        }else{
            $this->display('examine/20170601');
        }

    }
    /**
     * @title 员工考核信息获取
     * @remark 员工考核信息获取
     */
	public function views(){
	    $userid=intval($_GET['userid']);
        $admin=$this->model->table('admin')->where("id='$userid'")->find();
        $gongshiid=$admin['gongshiID'];
        $gsinfo=$this->model->table('gongshi')->where("id='$gongshiid'")->find();
        $usergongshi=$gsinfo['gongshi'];
        $entrydate=intval($_GET['entrydate']);
	    $uinfo=$this->model->table('admin')->where("id='$userid'")->find();
	    $this->assign('uinfo',$uinfo);
	    if(!empty($entrydate)){
            $list=$this->model->table('examine')->where("userid='$userid' and entrydate='$entrydate'")->select();
        }else{
            $list=$this->model->table('examine')->where("userid='$userid'")->select();
        }
        if(!empty($list)){
            foreach ($list as $key => &$val){
                $list[$key]['username']=$this->getMember($val['userid'], 'realname');
                $list[$key]['dapartment']=$this->getDepartMent($val['departid'], 'name');
                $list[$key]['score']=round(eval("return $usergongshi;"));
            }
        }
        $this->assign('userkaohe',$list);
        $this->display('examine/views');

    }

	public function score()
	{
		require(dirname(__FILE__).'/../../inc/site.php');//后台部分配置固定，需要重加载配置
		if($this->isPost()){
			$config = $_POST; //接收表单数据
			$config_array = array();
			foreach ($config as $key => $value) {
				$config_array["config['" . $key . "']"] = $value;
			}
			if ($this->setconfig($config_array)) {
				$this->inslog('进行了考核分值设置操作！');
				$this->success('设置修改成功',__URL__.'/score.html');
			} else {
				$this->error('设置修改失败');
			}
		}else{
			$this->assign('config', $config);
			$this->display('examine/score');
		}
	}
	
	function setconfig($array, $config_file = '../inc/site.php')
	{
		if (empty($array) || !is_array($array)) {
			return false;
		}
	
		$config = file_get_contents($config_file); //读取配置
		foreach ($array as $name => $value) {
			$name = str_replace(array("'", '"', '['), array("\\'", '\"', '\['), $name); //转义特殊字符，再传给正则替换
			if (is_string($value) && !in_array($value, array('true', 'false', '3306'))) {
				$value = "'" . $value . "'"; //如果是字符串，加上单引号
			}
			$config = preg_replace("/(\\$" . $name . ")\s*=\s*(.*?);/i", "$1={$value};", $config); //查找替换
		}
		// 写入配置
		if (file_put_contents($config_file, $config))
			return true;
		else
			return false;
	}
	
	/**
	 * @title 考核信息添加
	 * @remark 考核信息添加
	 */
	public function add(){
		if($this->isPost()){
			$userid=$_SESSION['admin_uid'];
			$uinfo=$this->model->table('admin')->where("id='$userid'")->find();
			//获取数据
			$data=array();
			$data['userid']=$userid;
			$data['departid']=$uinfo['departid'];
			$data['departpath']=$uinfo['departpath'];
			$data['Hits']=intval($_POST['Hits']);
			$data['ArticleNums']=intval($_POST['ArticleNums']);
			$data['picNums']=intval($_POST['picNums']);
			$data['AddScore']=trim($_POST['AddScore']);
			$dataarr=explode( '-', $_POST['entrydate']); 
			//print_r($dataarr); return;
			$nowtime=$dataarr[0].'-'.$dataarr[1];
			$data['entrydate']=$entrydate=strtotime($nowtime,TRUE);
			$data['discon']=trim($_POST['discon']);
			$data['uptime']=time();
			//验证数据
			//数据库操作
			$info=$this->model->table('examine')->where("userid='$userid' and entrydate='$entrydate'")->find();
			if(!empty($info)){
				$this->error('该月份考核信息已经存在，不能重复录入',__URL__.'/index.html');
			}
			if($this->model->table('examine')->data($data)->insert()){
				$this->inslog('添加“<font color="#ff0000">'.$_POST['entrydate'].'</font>”的考核信息。');
				$this->success('考核信息添加成功');
			}else{
				$this->error('考核信息添加失败');
			}
		}else{
			$this->display('examine/edit');
		}
	}
	/**
	 * @title 考核信息修改
	 * @remark 考核信息修改
	 */
	public function edit(){
		if($this->isPost()){
			$userid=$_SESSION['admin_uid'];
			//获取数据
			$data=array();
			$id=$id=intval($_POST['id']);
			$data['Hits']=intval($_POST['Hits']);
			$data['ArticleNums']=intval($_POST['ArticleNums']);
			$data['picNums']=intval($_POST['picNums']);
			$data['AddScore']=trim($_POST['AddScore']);
			$dataarr=explode( '-', $_POST['entrydate']); 
			//print_r($dataarr); return;
			$nowtime=$dataarr[0].'-'.$dataarr[1];
			$data['entrydate']=$entrydate=strtotime($nowtime,TRUE);
			$data['discon']=trim($_POST['discon']);
			$data['editime']=time();
			//验证数据
			//if(empty($data['Hits'])) $this->error('点击量不能为空');
			//if(empty($data['ArticleNums'])) $this->error('编辑稿件数量不能为空');
			//数据库操作
			$info=$this->model->table('examine')->where("userid='$userid' and id!='$id' and entrydate='$entrydate'")->find();
			$nowmoth=date('Y-m',time());
			$thismoth=date('Y-m',$info['entrydate']);
			if($nowmoth!=$thismoth){
                $this->error('该考核信息不是当月考核信息，不可以编辑！');
            }

			if(!empty($info)){
				$this->error('该月份考核信息已经存在，不能重复录入',__URL__.'/index.html');
			}
			if($this->model->table('examine')->data($data)->where("id='$id'")->update()){
				$this->inslog('修改“<font color="#ff0000">'.$_POST['entrydate'].'</font>”的考核信息。');
				$this->success('考核信息修改成功');
			}else{
				$this->error('考核信息修改失败');
			}
		
		}else{
			$id=intval($_GET[0]);
			if($id>0){
				$condition['id']=$id;
				$info=$this->model->table('examine')->where($condition)->find();
			}
			$this->assign('info',$info);
			$this->display('examine/edit');
		}
	}
	/**
	 * @title 考核信息删除
	 * @remark 考核信息删除
	 */
	public function del(){
	    if($this->isPost()){
	        $id = intval($_POST['id']);
	       
	        $info=$this->model->table('examine')->where("`id`='$id'")->find();
	        $entrydate=date('Y-m-d',$info['entrydate']);
    		if($this->model->table('examine')->where("`id`='$id'")->delete()){
    			$this->inslog('删除考核信息信息“<font color="#ff0000">'.$entrydate.'</font>”。');
    			$this->success('考核信息删除成功');
    		}else{
			   $this->error('考核信息删除失败');
    		}
	    }
    }
    public function ajaxdepartwokers(){
    	$departid=intval($_GET['departid']);
		$list=$this->model->table('admin')->field('id,realname')->where("departid='$departid' and type='1'")->select();
		$tmpstr='';
		if(!empty($list)){
            $tmpstr.='<option value="">请选择员工</option>';
	        foreach ($list as $key => &$val)
	        {
	        	$tmpstr.='<option value="'.$val['id'].'">'.$val['realname'].'</option>';
	        }
		}else{
			$tmpstr.='<option value="0" selected="selected">暂无数据</option>';
		}
		//print_r($sinfo);
    	echo $tmpstr;
    }
    
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