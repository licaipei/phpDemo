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
        $condition=array();
        $url=__URL__.'/index.html';
        $page=new Page();
        $cur_page=$page->getCurPage($url);
        $limit_start=($cur_page-1)*$listRows;
        $limit=$limit_start.','.$listRows;
        $id=intval($_GET[0]);
        $domainid=intval($_GET['domainid']);
        $this->assign('id', $id);
        $startdate=strtotime($_GET['startdate']);
        $enddate=strtotime($_GET['enddate'].' 23:59:59');
        if(empty($uinfo['tongjiqx'])) {
            if (!empty($id)) {
                $info = $this->model->table('site_visit')->where("id='$id'")->find();
                $pageurl = $info['url'];
                $count = $this->model->table('site_visit')->where("url='$pageurl'")->count();
                $list = $this->model->table('site_visit')->where("url='$pageurl'")->order('id DESC')->limit($limit)->select();
            } elseif (!empty($startdate) and !empty($enddate)) {
                if ($startdate < $enddate) {
                    $sql = "SELECT id,sitename,title,domain,url,fromurl,uptime,SUM(hits) AS hits FROM {$this->model->pre}site_visit WHERE uptime>=$startdate and uptime<=$enddate GROUP BY url ORDER BY hits";
                    $count = count($this->model->query($sql));
                    $sql1 = "SELECT id,domainid,sitename,title,domain,url,fromurl,uptime,SUM(hits) AS hits FROM {$this->model->pre}site_visit WHERE uptime>=$startdate and uptime<=$enddate GROUP BY url ORDER BY hits desc LIMIT {$limit}";
                    $list = $this->model->query($sql1);
                } elseif ($startdate == $enddate) {
                    $enddate = strtotime($_POST['startdate'] . ' 23:59:59');
                    $sql = "SELECT id,domainid,sitename,title,domain,url,fromurl,uptime,SUM(hits) AS hits FROM {$this->model->pre}site_visit WHERE uptime>=$startdate and uptime<=$enddate GROUP BY url ORDER BY hits";
                    $count = count($this->model->query($sql));
                    $sql1 = "SELECT id,domainid,sitename,title,domain,url,fromurl,uptime,SUM(hits) AS hits FROM {$this->model->pre}site_visit WHERE uptime>=$startdate and uptime<=$enddate GROUP BY url ORDER BY hits desc LIMIT {$limit}";
                    $list = $this->model->query($sql1);
                } else {
                    $this->alert('起始时间不能比结束时间大');
                }
            } elseif (!empty($domainid)) {
                $sql = "SELECT id,sitename,title,domain,url,fromurl,uptime,SUM(hits) AS hits FROM {$this->model->pre}site_visit WHERE domainid='$domainid' GROUP BY url ORDER BY hits";
                $count = count($this->model->query($sql));
                $sql1 = "SELECT id,domainid,sitename,title,domain,url,fromurl,uptime,SUM(hits) AS hits FROM {$this->model->pre}site_visit WHERE domainid='$domainid' GROUP BY url ORDER BY hits desc LIMIT {$limit}";
                $list = $this->model->query($sql1);
            } else {
                $sql = "SELECT id,sitename,title,domain,url,fromurl,uptime,SUM(hits) AS hits FROM {$this->model->pre}site_visit GROUP BY url ORDER BY hits";
                $count = count($this->model->query($sql));
                $sql1 = "SELECT id,domainid,sitename,title,domain,url,fromurl,uptime,SUM(hits) AS hits FROM {$this->model->pre}site_visit GROUP BY url ORDER BY hits desc LIMIT {$limit}";
                $list = $this->model->query($sql1);
            }
        }else{
            if (!empty($id)) {
                $info = $this->model->table('site_visit')->where("id='$id'")->find();
                $pageurl = $info['url'];
                $count = $this->model->table('site_visit')->where("url='$pageurl'")->count();
                $list = $this->model->table('site_visit')->where("url='$pageurl'")->order('id DESC')->limit($limit)->select();
            } elseif (!empty($startdate) and !empty($enddate)) {
                if ($startdate < $enddate) {
                    $sql = "SELECT id,sitename,title,domain,url,fromurl,uptime,SUM(hits) AS hits FROM {$this->model->pre}site_visit WHERE domainid IN ($tongjiqx) AND uptime>=$startdate and uptime<=$enddate AND domainid IN($tongjiqx) GROUP BY url ORDER BY hits";
                    $count = count($this->model->query($sql));
                    $sql1 = "SELECT id,domainid,sitename,title,domain,url,fromurl,uptime,SUM(hits) AS hits FROM {$this->model->pre}site_visit WHERE domainid IN ($tongjiqx) AND uptime>=$startdate and uptime<=$enddate GROUP BY url ORDER BY hits desc LIMIT {$limit}";
                    $list = $this->model->query($sql1);
                } elseif ($startdate == $enddate) {
                    $enddate = strtotime($_POST['startdate'] . ' 23:59:59');
                    $sql = "SELECT id,domainid,sitename,title,domain,url,fromurl,uptime,SUM(hits) AS hits FROM {$this->model->pre}site_visit WHERE domainid IN ($tongjiqx) AND uptime>=$startdate and uptime<=$enddate GROUP BY url ORDER BY hits";
                    $count = count($this->model->query($sql));
                    $sql1 = "SELECT id,domainid,sitename,title,domain,url,fromurl,uptime,SUM(hits) AS hits FROM {$this->model->pre}site_visit WHERE domainid IN ($tongjiqx) AND uptime>=$startdate and uptime<=$enddate GROUP BY url ORDER BY hits desc LIMIT {$limit}";
                    $list = $this->model->query($sql1);
                } else {
                    $this->alert('起始时间不能比结束时间大');
                }
            }elseif(!empty($domainid)) {
                $sql = "SELECT id,sitename,title,domain,url,fromurl,uptime,SUM(hits) AS hits FROM {$this->model->pre}site_visit WHERE domainid='$domainid' GROUP BY url ORDER BY hits";
                $count = count($this->model->query($sql));
                $sql1 = "SELECT id,domainid,sitename,title,domain,url,fromurl,uptime,SUM(hits) AS hits FROM {$this->model->pre}site_visit WHERE domainid='$domainid' GROUP BY url ORDER BY hits desc LIMIT {$limit}";
                $list = $this->model->query($sql1);
            } else {
                $sql = "SELECT id,sitename,title,domain,url,fromurl,uptime,SUM(hits) AS hits FROM {$this->model->pre}site_visit WHERE domainid IN ($tongjiqx) GROUP BY url ORDER BY hits";
                $count = count($this->model->query($sql));
                $sql1 = "SELECT id,domainid,sitename,title,domain,url,fromurl,uptime,SUM(hits) AS hits FROM {$this->model->pre}site_visit WHERE domainid IN($tongjiqx) GROUP BY url ORDER BY hits desc LIMIT {$limit}";
                $list = $this->model->query($sql1);
            }

        }
        if(!empty($list)){
            foreach ($list as $key => &$val){
                $list[$key]['hits1']=$this->getHits($val['id'],2,'');
                $list[$key]['hits2']=$this->getHits($val['id'],1,'');
                $list[$key]['ipnums1']=$this->getIpNums($val['id'],2,'');
                $list[$key]['ipnums2']=$this->getIpNums($val['id'],1,'');
                $list[$key]['ipnums']=$this->getallIps($val['url']);
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
        $day=$_GET['ondate'];
        $id=intval($_GET[0]);
        $info = $this->model->table('site_visit')->where("id='$id'")->find();
        $url=$info['url'];
        //$count=count($this->model->query("SELECT ip,url FROM {$this->model->pre}site_visit WHERE ip!='' AND url='$url' GROUP BY ip"));
        $info['hits1']=$this->getHits($info['id'],2,strtotime($day));
        $info['hits2']=$this->getHits($info['id'],1,strtotime($day));
        $info['ipnums1']=$this->getIpNums($info['id'],2,strtotime($day));
        $info['ipnums2']=$this->getIpNums($info['id'],1,strtotime($day));
		if(!empty($day)){
			$info['hits']=$this->getHits($info['id'],0,strtotime($day));
			$info['ipnums']=$this->getIpNums($info['id'],0,strtotime($day));
		}else{
			$info['hits']=$this->getallhits($info['url']);
			$info['ipnums']=$this->getallIps($info['url']);
		}
        $this->assign('info', $info);
        if(!empty($day)){
            $time=strtotime($day);
        }else {
            $time = time();
        }
        $this->assign('ondate',$time);
        $d1time=strtotime(date('Y-m-d',$time));

        $d2time1=$d1time+86400;
        $d2time=$d1time-86400;
        $d3time=$d1time-172800;
        $d4time=$d1time-259200;
        $d5time=$d1time-345600;
        $d6time=$d1time-432000;
        $d7time=$d1time-518400;
        $d8time=$d1time-604800;

        //点击量(PV)
        $jcountd1=$this->model->query("SELECT SUM(hits) AS hits FROM {$this->model->pre}site_visit WHERE url='$url' AND uptime>='$d1time' AND uptime<='$d2time1'");
        $jcountd2=$this->model->query("SELECT SUM(hits) AS hits FROM {$this->model->pre}site_visit WHERE url='$url' AND uptime<='$d1time' and uptime>='$d2time'");
        $jcountd3=$this->model->query("SELECT SUM(hits) AS hits FROM {$this->model->pre}site_visit WHERE url='$url' AND uptime<='$d2time' and uptime>='$d3time'");
        $jcountd4=$this->model->query("SELECT SUM(hits) AS hits FROM {$this->model->pre}site_visit WHERE url='$url' AND uptime<='$d3time' and uptime>='$d4time'");
        $jcountd5=$this->model->query("SELECT SUM(hits) AS hits FROM {$this->model->pre}site_visit WHERE url='$url' AND uptime<='$d4time' and uptime>='$d5time'");
        $jcountd6=$this->model->query("SELECT SUM(hits) AS hits FROM {$this->model->pre}site_visit WHERE url='$url' AND uptime<='$d5time' and uptime>='$d6time'");
        $jcountd7=$this->model->query("SELECT SUM(hits) AS hits FROM {$this->model->pre}site_visit WHERE url='$url' AND uptime<='$d6time' and uptime>='$d7time'");

        //IP数(独立IP)
        $ipnums1=count($this->model->query("SELECT ip,url FROM {$this->model->pre}site_visit WHERE ip!='' AND url='$url' AND uptime>='$d1time' AND uptime<='$d2time1' GROUP BY ip"));
        $ipnums2=count($this->model->query("SELECT ip,url FROM {$this->model->pre}site_visit WHERE ip!='' AND url='$url' AND uptime<='$d1time' and uptime>='$d2time' GROUP BY ip"));
        $ipnums3=count($this->model->query("SELECT ip,url FROM {$this->model->pre}site_visit WHERE ip!='' AND url='$url' AND uptime<='$d2time' and uptime>='$d3time' GROUP BY ip"));
        $ipnums4=count($this->model->query("SELECT ip,url FROM {$this->model->pre}site_visit WHERE ip!='' AND url='$url' AND uptime<='$d3time' and uptime>='$d4time' GROUP BY ip"));
        $ipnums5=count($this->model->query("SELECT ip,url FROM {$this->model->pre}site_visit WHERE ip!='' AND url='$url' AND uptime<='$d4time' and uptime>='$d5time' GROUP BY ip"));
        $ipnums6=count($this->model->query("SELECT ip,url FROM {$this->model->pre}site_visit WHERE ip!='' AND url='$url' AND uptime<='$d5time' and uptime>='$d6time' GROUP BY ip"));
        $ipnums7=count($this->model->query("SELECT ip,url FROM {$this->model->pre}site_visit WHERE ip!='' AND url='$url' AND uptime<='$d6time' and uptime>='$d7time' GROUP BY ip"));



        $this->assign('d1time',$d1time);
        $this->assign('d2time',$d2time);
        $this->assign('d3time',$d3time);
        $this->assign('d4time',$d4time);
        $this->assign('d5time',$d5time);
        $this->assign('d6time',$d6time);
        $this->assign('d7time',$d7time);
        if(!empty($jcountd1[0]['hits'])){
            $jcountd11=$jcountd1[0]['hits'];
        }else{
            $jcountd11=0;
        }
        if(!empty($jcountd1[0]['hits'])){
            $jcountd11=$jcountd1[0]['hits'];
        }else{
            $jcountd11=0;
        }
        if(!empty($jcountd2[0]['hits'])){
            $jcountd22=$jcountd2[0]['hits'];
        }else{
            $jcountd22=0;
        }
        if(!empty($jcountd3[0]['hits'])){
            $jcountd33=$jcountd3[0]['hits'];
        }else{
            $jcountd33=0;
        }
        if(!empty($jcountd4[0]['hits'])){
            $jcountd44=$jcountd4[0]['hits'];
        }else{
            $jcountd44=0;
        }
        if(!empty($jcountd5[0]['hits'])){
            $jcountd55=$jcountd5[0]['hits'];
        }else{
            $jcountd55=0;
        }
        if(!empty($jcountd6[0]['hits'])){
            $jcountd66=$jcountd6[0]['hits'];
        }else{
            $jcountd66=0;
        }
        if(!empty($jcountd7[0]['hits'])){
            $jcountd77=$jcountd7[0]['hits'];
        }else{
            $jcountd77=0;
        }

        $this->assign('jcountd1',$jcountd11);
        $this->assign('jcountd2',$jcountd22);
        $this->assign('jcountd3',$jcountd33);
        $this->assign('jcountd4',$jcountd44);
        $this->assign('jcountd5',$jcountd55);
        $this->assign('jcountd6',$jcountd66);
        $this->assign('jcountd7',$jcountd77);

        $this->assign('tcountd1',$ipnums1-1);
        $this->assign('tcountd2',$ipnums2-1);
        $this->assign('tcountd3',$ipnums3-1);
        $this->assign('tcountd4',$ipnums4-1);
        $this->assign('tcountd5',$ipnums5-1);
        $this->assign('tcountd6',$ipnums6-1);
        $this->assign('tcountd7',$ipnums7-1);

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
        $conwhere="title like '%$keywords%' or url like '%$keywords%'";
        if(!empty($tongjiqx)){
            $sql = "SELECT id,sitename,title,domain,url,fromurl,uptime,SUM(hits) AS hits FROM {$this->model->pre}site_visit WHERE domainid IN ($tongjiqx) AND {$conwhere} GROUP BY url ORDER BY hits";
            $count = count($this->model->query($sql));
            $sql1 = "SELECT id,domainid,sitename,title,domain,url,fromurl,uptime,SUM(hits) AS hits FROM {$this->model->pre}site_visit WHERE domainid IN ($tongjiqx) AND {$conwhere} GROUP BY url ORDER BY hits desc LIMIT {$limit}";
            $list = $this->model->query($sql1);
        }else{
            $sql = "SELECT id,sitename,title,domain,url,fromurl,uptime,SUM(hits) AS hits FROM {$this->model->pre}site_visit WHERE {$conwhere} GROUP BY url ORDER BY hits";
            $count = count($this->model->query($sql));
            $sql1 = "SELECT id,domainid,sitename,title,domain,url,fromurl,uptime,SUM(hits) AS hits FROM {$this->model->pre}site_visit WHERE {$conwhere} GROUP BY url ORDER BY hits desc LIMIT {$limit}";
            $list = $this->model->query($sql1);
        }
        if(!empty($list)) {
            foreach ($list as $key => &$val) {
                $list[$key]['hits1'] = $this->getHits($val['id'], 2, '');
                $list[$key]['hits2'] = $this->getHits($val['id'], 1, '');
                $list[$key]['ipnums1'] = $this->getIpNums($val['id'], 2, '');
                $list[$key]['ipnums2'] = $this->getIpNums($val['id'], 1, '');
                $list[$key]['ipnums'] = $this->getallIps($val['url']);
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
        $info = $this->model->table('site_visit')->where("id='$id'")->find();
        $url=$info['url'];
        //$count=count($this->model->query("SELECT ip,url FROM {$this->model->pre}site_visit WHERE ip!='' AND url='$url' GROUP BY ip"));

        $stimestamp = strtotime($startday);
        $etimestamp = strtotime($endday);
        $overday=$etimestamp+86400;
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
        $allhits=$this->model->query("SELECT SUM(hits) AS hits FROM {$this->model->pre}site_visit WHERE url='$url' AND uptime>='$stimestamp' AND uptime<='$overday'");
        $allipnums=count($this->model->query("SELECT ip,url FROM {$this->model->pre}site_visit WHERE ip!='' AND url='$url' AND uptime>='$stimestamp' AND uptime<='$overday' GROUP BY ip"));
        $info['hits']=$allhits[0]['hits'];
        $info['ipnums']=$allipnums-1;

        // 保存每天日期
        $date = array();
        for($i=0; $i<$days; $i++){
            $date[] = date('Y-m-d', $stimestamp+(86400*$i));
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
                $time=strtotime($val);
                $endtime=$time+86400;
                $hits=$this->model->query("SELECT SUM(hits) AS hits FROM {$this->model->pre}site_visit WHERE url='$url' AND uptime>='$time' AND uptime<='$endtime'");
                if(!empty($hits[0]['hits'])){
                    $daypv.=$hits[0]['hits'].',';
                }else{
                    $daypv.='0,';
                }
                $ipnums=count($this->model->query("SELECT ip,url FROM {$this->model->pre}site_visit WHERE ip!='' AND url='$url' AND uptime>='$time' AND uptime<='$endtime' GROUP BY ip"));
                $dayip.=($ipnums-1).',';
                $categories.="'".date('m-d',strtotime($val))."',";
            }
        }
        //echo $daypv.'--'.$dayip.'--'.$categories;
        //print_r($date);
        $this->assign('daypv',rtrim($daypv,','));
        $this->assign('dayip',rtrim($dayip,','));
        $this->assign('categories',rtrim($categories,','));
        $this->assign('info', $info);
        $this->display('visit/searchviews');


    }
}