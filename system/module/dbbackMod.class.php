<?php
class dbbackMod extends commonMod
{
	static protected  $db='';
	public function __construct()
	{
		parent::__construct();
		self::$db=new Dbbak($this->config['DB_HOST'].':'.$this->config['DB_PORT'],$this->config['DB_USER'],$this->config['DB_PWD'],$this->config['DB_NAME'],'utf8','../data/db_back/');
	}

	/**
	 * @title 数据备份管理
	 * @remark 数据备份
	 */
	public function index()
	{
		$list=self::$db->getTables($this->config['DB_NAME']);//数据库表名
		if($this->isPost()){
			@set_time_limit(0);
			$backtype=intval($_POST['backtype']);
			$table=$_POST['table'];
			$db_size=$_POST['size'];
			if($backtype)
			{
				$table=$list;
			}
			else {if(empty($table)) $this->error('请选择需要备份的表~');}
			if(self::$db->exportSql($table,$db_size)){
				$this->inslog('对数据库进行了备份操作！');
				$this->success('备份成功',__URL__.'/index.html');
			}
			else $this->error('备份失败');
		}else{
			$this->assign('table',$list);
			//$this->assign('list',$this->getFileName('../data/db_back'));//文件夹下所有文件信息
			$this->assign('files',getDir('../data/db_back'));//获得文件夹列表
			$this->assign('url',__URL__);
			$this->display('dbback/index');
		}

	}
	/**
	 * @title 备份数据恢复
	 * @remark 备份数据信息恢复
	 */
	
	public function recover()
	{
		@set_time_limit(0);
		$file=$_GET[0];
		if(empty($file)) $this->error('参数错误');
		if(self::$db->importSql($file.'/'))
		{
			$this->inslog('进行了数据恢复操作！');
			$this->success('数据恢复成功！',__URL__.'/index.html');
		}
		else{
			$this->error('数据恢复失败！');
		}
	}
	
	//ajax显示备份详细信息
	public function detail(){
		$file=$_GET[0];
		if(empty($file)) {echo '参数错误'; return;}
		$list=getFileName(self::$db->dataDir.$file.'/');
		if(empty($list)) echo '<br/>没有数据备份信息';
		else{
		$str.='<table style="margin-top:5px; width:400px; margin-left:10px; margin-right:10px; line-height:30px;"><tr><td style="border-bottom:1px solid #ddd; font-weight:bold;">分卷</td><td style="border-bottom:1px solid #ddd; height:30px; font-weight:bold;">大小</td><td style="border-bottom:1px solid #ddd; font-weight:bold;">数据备份日期</td></tr>';
		foreach($list as $vo)
		   $str.='<tr><td>'.$vo['name'].'</td><td>'.$vo['size'].'kb</td><td>'.$vo['time'].'</td></tr>';
		$str.='</table>';
		echo $str;
		}
	}
	/**
	 * @title 删除备份数据
	 * @remark 备份数据信息删除
	 */
	public function del()
	{
		$file=$_GET[0];
		if(empty($file)) $this->error('参数错误');
		if(del_dir('../data/db_back/'.$file)){
			$this->inslog('删除备份信息！');
			$this->success('删除成功',__URL__.'/index.html');
		}
		else $this->error('删除失败');
	}
}
?>