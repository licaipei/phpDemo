<?php
class visitMod extends commonMod
{
	public function __construct()
	{
		parent::__construct();
	}
    public function index()
    {
        header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
        $data=array();
        $data['domain']=$domain=trim($_GET['domain']);
        $data['articleid']=$articleid=intval($_GET['articleid']);
        $data['author']=$author=trim($_GET['author']);
        $data['title']=$title=trim($_GET['title']);
        $url=$_GET['pageurl'];
        $data['url']=$reurl=preg_replace('/([^:])[\/\\\\]{2,}/','$1/',strpos($url,'?')?substr($url,0,strpos($url,'?')):''.$url.'');
        $ipdata['ip']=$ip=trim($_GET['fromip']);
		$ipdata['ipaddress']=trim($_GET['fromaddress']);
        if(!empty($domain) and !empty($title) and strpos($title,'?') == false){
            $info=$this->model->table('site')->where("domain='$domain' or domain1='$domain'")->find();
            if(!empty($info) and $info['status']==1){
                $data['domainid']=$info['id'];
                $data['sitename']=$info['website'];
                $visit=$this->model->table('visit')->where("url='$reurl'")->find();
                if(!empty($visit)){
					$visitid=$visit['id'];
                    $ipdata['visitid']=$visit['id'];
					$hdata['visitid']=$visit['id'];
					$times=date('Ymd',time());
                    $ipdata['times']=date('Ymd',time());
					$hdata['times']=date('Ymd',time());
                    $hdata['hits']=1;
                    $ipinfo=$this->model->table('visit_ip')->where("visitid='$visitid' and ip='$ip' and times='$times'")->find();
                    $hitinfo=$this->model->table('visit_hits')->where("visitid='$visitid' and times='$times'")->find();
                    if(empty($ipinfo)){
                        $this->model->table('visit_ip')->data($ipdata)->insert();
                    }
                    if(!empty($hitinfo)){
                        $this->model->table('visit_hits')->data("hits=hits+1")->where("visitid='$visitid' and times='$times'")->update();
                    }else{
                        $this->model->table('visit_hits')->data($hdata)->insert();
                    }
                }else{
                    $this->model->table('visit')->data($data)->insert();
						$visitid=mysql_insert_id();
						if(!empty($visitid)){
							$ipdata['visitid']=$visitid;
							$hdata['visitid']=$visitid;
							$ipdata['times']=date('Ymd',time());
							$hdata['times']=date('Ymd',time());
							$hdata['hits']=1;
							$this->model->table('visit_hits')->data($hdata)->insert();
							$this->model->table('visit_ip')->data($ipdata)->insert();
						}
                }
            }
        }else{
            echo 'error';
        }
	}
}
