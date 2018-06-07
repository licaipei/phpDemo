<?php
class settingsMod extends commonMod
{
	public function __construct()
	{
		parent::__construct();
		//if(!in_array($_GET['_action'],explode('-', self::$adminqx[$_GET['_module']]))&&$_SESSION['admin_manageqx']!='all') $this->alert('无权限操作！');
	}
	/**
	 * @title 系统设置
	 * @remark 系统信息设置
	 */
	public function index()
	{
		require(dirname(__FILE__).'/../../inc/config.php');//后台部分配置固定，需要重加载配置
		if($this->isPost()){
			$config = $_POST; //接收表单数据
			$config_array = array();
			foreach ($config as $key => $value) {
				$config_array["config['" . $key . "']"] = $value;
			}
			if ($this->set_config($config_array)) {
				$this->inslog('进行了系统设置操作！');
				$this->success('设置修改成功',__URL__.'/index');
			} else {
				$this->error('设置修改失败');
			}
		}else{
			$this->assign('config', $config);
			$this->assign('title','网站设置');
			$this->assign('username',$_SESSION['admin_username']);
			$this->display('settings/index');
		}
	}

	// 修改配置的函数
    function set_config($array, $config_file = '../inc/config.php')
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
}
?>