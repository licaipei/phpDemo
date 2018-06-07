<?php
class clearMod extends commonMod{
	public function __construct()
	{
		parent::__construct();
	}
	/**
	 * @title 缓存清理
	 * @remark 网站数据缓存清理
	 */
	public function index()
	{
		$path=dirname(__FILE__).'/../../data/';
		$db=explode('/',$this->config['DB_CACHE_PATH']);
		$template=explode('/',$this->config['TPL_CACHE_PATH']);
		$html=explode('/',$this->config['HTML_CACHE_PATH']);
		if(empty($_GET['type'])){
			$this->assign('dbf', $db[2]);
			$this->assign('templatef', $template[2]);
			$this->assign('htmlf', $html[2]);
			$dbsize=holdersize($path.$db[2]);
			$temsize=holdersize($path.$template[2]);
			$htmlsize=holdersize($path.$html[2]);
			$this->assign('dbsize', $dbsize);
			$this->assign('temsize', $temsize);
			$this->assign('htmlsize', $htmlsize);
			$this->display('clear/index');
		}else{
			$file=$_GET['type'];
			if($file==$db[2]||$file==$template[2]||$file==$html[2]){
				if(del_dir($path.$file)){ 
					$this->inslog('清除系统缓存操作！');
					echo '<div class="inputhelp">清空成功~</div>';
				}
				else echo '<div class="inputhelp">已经是空里了~</div>';
			}else echo '参数非法~';
		}
	}		
}
?>