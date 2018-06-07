<?php
class testMod extends commonMod
{
	public function __construct()
	{
		parent::__construct();
	}
	public function index()
	{
		$qxmenu=$this->model->table('menus')->order("display_order ASC")->select();
		if(!empty($qxmenu)) foreach ($qxmenu as $vo){
			$menuid=$vo['menu_id'];
			$childmenu[$menuid]=$this->model->table('power')->where("menu_id='$menuid' and is_menu='1'")->order('display_order ASC')->select();
		}
		$leftmenu=getAllMenuShow($qxmenu);
		print_r($leftmenu);
		$this->assign('leftmenu',$leftmenu);
		$this->assign('childmenu',$childmenu);
		$this->display('test');
		
	}
	public function pinyin()
	{
		$str='李才配';
		echo Pinyin::ChineseToPinyin($str);
		echo '<br/>';
		echo Pinyin::ChineseToPinyin($str,false, true);
		
	}
	public function tihuan()
    {
        $badword = array(
            '张三','张三丰','张三丰田'
        );
        $badword1 = array_combine($badword,array_fill(0,count($badword),''));
        $bb = '我今天开着田上班张三丰';
        $str = strtr($bb, $badword1);
        echo $str;
    }
}
