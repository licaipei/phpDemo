<?php
class visitMod extends commonMod{
    public function __construct(){
        parent::__construct();
    }
    /**
     * @title 点击量统计管理
     * @remark 点击量统计管理
     */
    public function index(){
        $admind=$_SESSION['admin_uid'];
        $uinfo=$this->model->table('admin')->where("id='$admind'")->find();
        $tongjiqx=$uinfo['tongjiqx'];
        $listRows=20;
        $url=__URL__.'/index.html';
        $page=new Page();
        $cur_page=$page->getCurPage($url);
        $limit_start=($cur_page-1)*$listRows;
        $limit=$limit_start.','.$listRows;
        $domainid=intval($_GET['domainid']);
        $time=strtotime(date('Ymd',time()));
        $yestime=date('Ymd',$time-86400);
        $nowtime=date('Ymd',time());
		
        //if(empty($uinfo['tongjiqx'])) {
        //    if (!empty($domainid)) {
         //       $csql="select A.id,A.domainid,A.sitename,A.domain,A.articleid,A.author,A.title,A.url,COUNT(distinct C.ip) AS ipnums,SUM(distinct B.hits) AS hitnums,B.times from ({$this->model->pre}visit AS A , {$this->model->pre}visit_hits AS B) , {$this->model->pre}visit_ip AS C  WHERE A.domainid='$domainid' AND A.id=B.visitid AND A.id=C.visitid /*AND B.times='$nowtime'*/ GROUP BY A.id";
        //        $sql="select A.id,A.domainid,A.sitename,A.domain,A.articleid,A.author,A.title,A.url,COUNT(distinct C.ip) AS ipnums,SUM(distinct B.hits) AS hitnums,B.times from ({$this->model->pre}visit AS A , {$this->model->pre}visit_hits AS B) , {$this->model->pre}visit_ip AS C  WHERE A.domainid='$domainid' AND A.id=B.visitid AND A.id=C.visitid /*AND B.times='$nowtime'*/ GROUP BY A.id ORDER BY A.id DESC limit $limit";
        //        $count = count($this->model->query($csql));
        //        $list = $this->model->query($sql);
        //    } else {
        //        $csql="select A.id,A.domainid,A.sitename,A.domain,A.articleid,A.author,A.title,A.url,COUNT(distinct C.ip) AS ipnums,SUM(distinct B.hits) AS hitnums,B.times from ({$this->model->pre}visit AS A , {$this->model->pre}visit_hits AS B) , {$this->model->pre}visit_ip AS C  WHERE A.id=B.visitid AND A.id=C.visitid /*AND B.times='$nowtime'*/ GROUP BY A.id";
        //        $sql="select A.id,A.domainid,A.sitename,A.domain,A.articleid,A.author,A.title,A.url,COUNT(distinct C.ip) AS ipnums,SUM(distinct B.hits) AS hitnums,B.times from ({$this->model->pre}visit AS A , {$this->model->pre}visit_hits AS B) , {$this->model->pre}visit_ip AS C  WHERE A.id=B.visitid AND A.id=C.visitid /*AND B.times='$nowtime'*/ GROUP BY A.id ORDER BY A.id DESC limit $limit";
        //        $count = count($this->model->query($csql));
        //        $list = $this->model->query($sql);
        //    }
        //}else{
        //    if(!empty($domainid)) {
        //        $csql="select A.id,A.domainid,A.sitename,A.domain,A.articleid,A.author,A.title,A.url,COUNT(distinct C.ip) AS ipnums,SUM(distinct B.hits) AS hitnums,B.times from ({$this->model->pre}visit AS A , {$this->model->pre}visit_hits AS B) , {$this->model->pre}visit_ip AS C  WHERE A.domainid='$domainid' AND A.id=B.visitid AND A.id=C.visitid /*AND B.times='$nowtime'*/ GROUP BY A.id";
        //        $sql="select A.id,A.domainid,A.sitename,A.domain,A.articleid,A.author,A.title,A.url,COUNT(distinct C.ip) AS ipnums,SUM(distinct B.hits) AS hitnums,B.times from ({$this->model->pre}visit AS A , {$this->model->pre}visit_hits AS B) , {$this->model->pre}visit_ip AS C  WHERE A.domainid='$domainid' AND A.id=B.visitid AND A.id=C.visitid /*AND B.times='$nowtime'*/ GROUP BY A.id ORDER BY A.id DESC limit $limit";
        //        $count = count($this->model->query($csql));
        //        $list = $this->model->query($sql);
        //    } else {
        //        $csql="select A.id,A.domainid,A.sitename,A.domain,A.articleid,A.author,A.title,A.url,COUNT(distinct C.ip) AS ipnums,SUM(distinct B.hits) AS hitnums,B.times from ({$this->model->pre}visit AS A , {$this->model->pre}visit_hits AS B) , {$this->model->pre}visit_ip AS C  WHERE A.domainid IN ($tongjiqx) AND A.id=B.visitid AND A.id=C.visitid /*AND B.times='$nowtime'*/ GROUP BY A.id";
        //        $sql="select A.id,A.domainid,A.sitename,A.domain,A.articleid,A.author,A.title,A.url,COUNT(distinct C.ip) AS ipnums,SUM(distinct B.hits) AS hitnums,B.times from ({$this->model->pre}visit AS A , {$this->model->pre}visit_hits AS B) , {$this->model->pre}visit_ip AS C  WHERE A.domainid IN ($tongjiqx) AND A.id=B.visitid AND A.id=C.visitid /*AND B.times='$nowtime'*/ GROUP BY A.id ORDER BY A.id DESC limit $limit";
        //        $count = count($this->model->query($csql));
        //        $list = $this->model->query($sql);
        //    }

        //}
        if(empty($uinfo['tongjiqx'])) {
            if (!empty($domainid)) {
				$count=$this->model->table('visit')->where("domainid='$domainid'")->count();
				$list=$this->model->table('visit')->where("domainid='$domainid'")->order("id desc")->limit($limit)->select();
            } else {
				$count=$this->model->table('visit')->count();
				$list=$this->model->table('visit')->order("id desc")->limit($limit)->select();
            }
        }else{
            if(!empty($domainid)) {
				$count=$this->model->table('visit')->where("domainid='$domainid'")->count();
				$list=$this->model->table('visit')->where("domainid='$domainid'")->order("id desc")->limit($limit)->select();
            } else {
				$count=$this->model->table('visit')->where("domainid IN ($tongjiqx)")->count();
				$list=$this->model->table('visit')->where("domainid IN ($tongjiqx)")->order("id desc")->limit($limit)->select();
            }
        }
        if(!empty($list)){
            foreach ($list as $key => &$val){
                $list[$key]['hits1']=$this->getVisitHits($val['id'],$yestime);
                $list[$key]['hits2']=$this->getVisitHits($val['id'],$nowtime);
                $list[$key]['ipnums1']=$this->getVisitIps($val['id'],$yestime);
                $list[$key]['ipnums2']=$this->getVisitIps($val['id'],$nowtime);
                $list[$key]['hitnums']=$this->getVisitAllHits($val['id']);
                $list[$key]['ipnums']=$this->getVisitAllIps($val['id']);
            }
        }
        $this->assign('list',$list);
        $this->assign('page',$page->show($url,$count,$listRows));
        $this->display('visit/index');
    }
    /**
     * @title 查看单页统计
     * @remark 查看单页统计
     */
    public function detail()
    {
        $time=strtotime(date('Ymd',time()));
        $yestime=date('Ymd',$time-86400);
        $nowtime=date('Ymd',time());
        $id=intval($_GET[0]);
		$info=$this->model->table('visit')->where("id='$id'")->find();
        //$sql="select A.id,A.domainid,A.sitename,A.domain,A.articleid,A.author,A.title,A.url,COUNT(distinct C.ip) AS ipnums,SUM(distinct B.hits) AS hitnums,B.times from ({$this->model->pre}visit AS A , {$this->model->pre}visit_hits AS B) , {$this->model->pre}visit_ip AS C  WHERE A.id=B.visitid and A.id=C.visitid and A.id='$id' GROUP BY A.id";
        //$info=$this->model->query($sql);
        $info['hits1']=$this->getVisitHits($info['id'],$yestime);
        $info['hits2']=$this->getVisitHits($info['id'],$nowtime);
        $info['ipnums1']=$this->getVisitIps($info['id'],$yestime);
        $info['ipnums2']=$this->getVisitIps($info['id'],$nowtime);
        $info['hitnums']=$this->getVisitAllHits($info['id']);
        $info['ipnums']=$this->getVisitAllIps($info['id']);
        $this->assign('info', $info);

        $this->assign('ondate',$time);
        $d1time=strtotime(date('Y-m-d',$time));
        $date = array();
        $curtime=time();
        for ($i=0;$i<=30;$i++){
            $date[]=date('Ymd',$curtime-$i*24*60*60);
        }
        if(!empty($date)) {
            $daypv='';
            $dayip='';
            $categories='';
            $date1=array_reverse($date);
            foreach ($date1 as $key => &$val) {
                $hits=$this->model->query("select hits from zt_visit_hits WHERE visitid='$id' and times='$val'");
                if($hits[0]['hits']>0){
                    $daypv.=$hits[0]['hits'].',';
                }else{
                    $daypv.='0,';
                }
                $ips=$this->model->query("select COUNT(DISTINCT ip) AS ipnums from zt_visit_ip WHERE visitid='$id' and times='$val'");
                if($ips[0]['ipnums']>0){
                    $dayip.=$ips[0]['ipnums'].',';
                }else{
                    $dayip.='0,';
                }
                $categories.="'".date('m-d',strtotime($val))."',";
            }
        }
        $this->assign('d7time',date('Y-m-d',time()-30*24*60*60));
        $this->assign('d1time',$d1time);
        $this->assign('daypv',rtrim($daypv,','));
        $this->assign('dayip',rtrim($dayip,','));
        $this->assign('categories',rtrim($categories,','));
        $this->display('visit/detail');
    }
    /**
     * @title 稿件查询
     * @remark 稿件查询
     */
    public function search(){
        $admind=$_SESSION['admin_uid'];
        $uinfo=$this->model->table('admin')->where("id='$admind'")->find();
        $tongjiqx=$uinfo['tongjiqx'];
        $keyword=in($_GET['keywords']);
        $listRows=18;
        $url='search.html?keyword='.(urlencode($keyword));
        $page=new Page();
        $cur_page=$page->getCurPage($url);
        $limit_start=($cur_page-1)*$listRows;
        $limit=$limit_start.','.$listRows;
        $keywords=stripsearchkey($keyword);
        $time=strtotime(date('Ymd',time()));
        $yestime=date('Ymd',$time-86400);
        $nowtime=date('Ymd',time());
        if(empty($tongjiqx)){
			$csql="select * from {$this->model->pre}visit where title like '%$keywords%' or author like '%$keywords%' or url like '%$keywords%'";
			$sql="select * from {$this->model->pre}visit where title like '%$keywords%' or author like '%$keywords%' or url like '%$keywords%' ORDER BY id DESC limit $limit";
			/*
			$csql="select A.id,A.domainid,A.sitename,A.domain,A.articleid,A.author,A.title,A.url,COUNT(distinct C.ip) AS ipnums,SUM(distinct B.hits) AS hitnums,B.times from ({$this->model->pre}visit AS A , {$this->model->pre}visit_hits AS B) , {$this->model->pre}visit_ip AS C  WHERE A.id=B.visitid AND A.id=C.visitid AND A.title like '%$keywords%' GROUP BY A.id";
            $sql="select A.id,A.domainid,A.sitename,A.domain,A.articleid,A.author,A.title,A.url,COUNT(distinct C.ip) AS ipnums,SUM(distinct B.hits) AS hitnums,B.times from ({$this->model->pre}visit AS A , {$this->model->pre}visit_hits AS B) , {$this->model->pre}visit_ip AS C  WHERE A.id=B.visitid AND A.id=C.visitid AND A.title like '%$keywords%' GROUP BY A.id ORDER BY A.id DESC limit $limit";
            */
			$count = count($this->model->query($csql));
            $list = $this->model->query($sql);
        }else{
			$csql="select * from {$this->model->pre}visit where domainid IN ($tongjiqx) and title like '%$keywords%' or author like '%$keywords%' or url like '%$keywords%'";
			$sql="select * from {$this->model->pre}visit where domainid IN ($tongjiqx) and title like '%$keywords%' or author like '%$keywords%' or url like '%$keywords%' ORDER BY id DESC limit $limit";
            /*
            $csql="select A.id,A.domainid,A.sitename,A.domain,A.articleid,A.author,A.title,A.url,COUNT(distinct C.ip) AS ipnums,SUM(distinct B.hits) AS hitnums,B.times from ({$this->model->pre}visit AS A , {$this->model->pre}visit_hits AS B) , {$this->model->pre}visit_ip AS C  WHERE A.domainid IN ($tongjiqx) AND A.id=B.visitid AND A.id=C.visitid AND A.title like '%$keywords%' GROUP BY A.id";
            $sql="select A.id,A.domainid,A.sitename,A.domain,A.articleid,A.author,A.title,A.url,COUNT(distinct C.ip) AS ipnums,SUM(distinct B.hits) AS hitnums,B.times from ({$this->model->pre}visit AS A , {$this->model->pre}visit_hits AS B) , {$this->model->pre}visit_ip AS C  WHERE A.domainid IN ($tongjiqx) AND A.id=B.visitid AND A.id=C.visitid AND A.title like '%$keywords%' GROUP BY A.id ORDER BY A.id DESC limit $limit";
            */
			$count = count($this->model->query($csql));
            $list = $this->model->query($sql);
        }
        if(!empty($list)) {
            foreach ($list as $key => &$val) {
                $list[$key]['hits1']=$this->getVisitHits($val['id'],$yestime);
                $list[$key]['hits2']=$this->getVisitHits($val['id'],$nowtime);
                $list[$key]['ipnums1']=$this->getVisitIps($val['id'],$yestime);
                $list[$key]['ipnums2']=$this->getVisitIps($val['id'],$nowtime);
                $list[$key]['hitnums']=$this->getVisitAllHits($val['id']);
                $list[$key]['ipnums']=$this->getVisitAllIps($val['id']);
           }
        }
        $this->inslog('查询关键词为<font color="#ff0000">'.$keywords.'</font>的稿件统计信息！');
        $this->assign('list',$list);
        $this->assign('page',$page->show($url,$count,$listRows));
        $this->display('visit/search');

    }
    /**
     * @title 筛选统计查询
     * @remark 筛选统计查询
     */
    public function searchviews()
    {
        $id=intval($_GET[0]);
        $startday=$_GET['startday'];
        $endday=$_GET['endday'];
        $this->assign('startday',$startday);
        $this->assign('endday',$endday);
        $sql="select A.id,A.domainid,A.sitename,A.domain,A.articleid,A.author,A.title,A.url,COUNT(distinct C.ip) AS ipnums,SUM(distinct B.hits) AS hitnums,B.times from ({$this->model->pre}visit AS A , {$this->model->pre}visit_hits AS B) , {$this->model->pre}visit_ip AS C  WHERE A.id=B.visitid and A.id=C.visitid and A.id='$id' GROUP BY A.id";
        $info=$this->model->query($sql);
        $stimestamp = strtotime($startday);
        $etimestamp = strtotime($endday);
        if(empty($startday)){
            $this->alert('请选择起始日期');
        }
        if(empty($endday)){
            $this->alert('请选择结束日期');
        }
        if($stimestamp==$etimestamp){
            $this->alert('起始日期和结束日期不能为同一天');
        }
        if($stimestamp>$etimestamp){
            $this->alert('起始日期不能大于结束日期');
        }
         // 计算日期段内有多少天
        $days = ($etimestamp-$stimestamp)/86400+1;

        // 保存每天日期
        $date = array();
        for($i=0; $i<$days; $i++){
            $date[] = date('Ymd', $stimestamp+(86400*$i));
        }
        if(count($date)>31){
            $this->alert('最多可查询31天的数据');
        }

        //print_r($date);
        if(!empty($date)) {
            $daypv='';
            $dayip='';
            $categories='';
            foreach ($date as $key => &$val) {
                $hits=$this->model->query("select hits from zt_visit_hits WHERE visitid='$id' and times='$val'");
                if($hits[0]['hits']>0){
                    $daypv.=$hits[0]['hits'].',';
                }else{
                    $daypv.='0,';
                }
                $ips=$this->model->query("select COUNT(ip) AS ipnums from zt_visit_ip WHERE visitid='$id' and times='$val'");
                if($ips[0]['ipnums']>0){
                    $dayip.=$ips[0]['ipnums'].',';
                }else{
                    $dayip.='0,';
                }
                $categories.="'".date('m-d',strtotime($val))."',";
            }
        }
        $this->assign('daypv',rtrim($daypv,','));
        $this->assign('dayip',rtrim($dayip,','));
        $this->assign('categories',rtrim($categories,','));
        $this->assign('info', $info[0]);
        $this->display('visit/searchviews');


    }
}