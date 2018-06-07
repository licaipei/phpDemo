<?php
class statisticsMod extends commonMod
{
	public function __construct()
	{
		parent::__construct();
	}
	public function index()
	{
		header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
		/*
        $urlreplace = array(
            '?from=singlemessage',
            '?from=timeline',
            '?from=groupmessage',
            '?from=timeline&isappinstalled=0',
            '?from=groupmessage&isappinstalled=0',
            '?from=singlemessage&isappinstalled=0',
			'?t=xet',
			'?mType=Group'
        );
        $urlreplace1 = array_combine($urlreplace,array_fill(0,count($urlreplace),''));*/
		
		$data=array();
		$data['domain']=$domain=trim($_GET['domain']);
        $data['ip']=$ip=trim($_GET['fromip']);
        $data['address']=$address=trim($_GET['fromaddress']);
		$data['title']=$title=trim($_GET['title']);
		$url=$_GET['pageurl'];
		$data['url']=preg_replace('/([^:])[\/\\\\]{2,}/','$1/',strpos($url,'?')?substr($url,0,strpos($url,'?')):''.$url.'');
        //$data['url']=strtr($_GET['pageurl'], $urlreplace1);
		$data['fromurl']=trim($_GET['fromurl']);
		$data['hits']=1;
		$data['uptime']=time();
		//数据库操作
		if(!empty($domain) and !empty($title) and strpos($title,'?') == false){
			$info=$this->model->table('site')->where("domain='$domain' or domain1='$domain'")->find();
			if(!empty($info) and $info['status']==1){
				$data['domainid']=$info['id'];
				$data['sitename']=$info['website'];
				$this->model->table('site_visit')->data($data)->insert();	
			}
		}else{
			echo 'error';
		}	
	}
}
