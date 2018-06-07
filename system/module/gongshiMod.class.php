<?php
class gongshiMod extends commonMod{
    public function __construct(){
        parent::__construct();
    }
    /**
     * @title 考核公式管理
     * @remark 考核公式管理
     */
    public function index(){
        $listRows=16;
        $condition=array();
        $url=__URL__.'/index.html';
        $page=new Page();
        $cur_page=$page->getCurPage($url);
        $limit_start=($cur_page-1)*$listRows;
        $limit=$limit_start.','.$listRows;

        $where="";
        $condition=array();
        //获取行数
        $count=$this->model->table('gongshi')->where($where)->count();
        $list=$this->model->table('gongshi')->where($where)->order('id ASC')->limit($limit)->select();
        $this->assign('list',$list);
        $this->assign('page',$page->show($url,$count,$listRows));
        $this->display('gongshi/index');

    }
    /**
     * @title 考核公式编辑
     * @remark 考核公式编辑
     */
    public function edit(){
        if($this->isPost()){
            //获取数据
            //获取数据
            $data=array();
            $id=intval($_POST['id']);
            $data['name']=in($_POST['name']);
            $data['gongshi']=$_POST['gongshi'];
            //验证数据
            if(empty($data['name'])) $this->error('公式名称');
            if(empty($data['gongshi'])) $this->error('考核公式不能为空');
            if($id>0) {
                $info = $this->model->table('gongshi')->where("id='$id'")->find();
                if ($this->model->table('gongshi')->data($data)->where("`id`='{$id}'")->update()) {
                    $this->inslog('修改考核公式“<font color="#ff0000">' . $_POST['name'] . '</font>”。');
                    $this->success('信息修改成功');
                } else {
                    $this->error('信息修改失败');
                }
            }else{
                if($this->model->table('gongshi')->data($data)->insert()){
                    $this->inslog('添加考核公式“<font color="#ff0000">'.$_POST['name'].'</font>”。');
                    $this->success('信息添加成功');
                }else{
                    $this->error('信息添加失败');
                }
            }

        }else{
            $id=intval($_GET[0]);
            if($id>0){
                $condition['id']=$id;
                $info=$this->model->table('gongshi')->where($condition)->find();
            }
            $this->assign('info',$info);
            $this->display('gongshi/edit');
        }
    }
    /**
     * @title 考核公式删除
     * @remark 考核公式删除
     */
    public function del(){
        if($this->isPost()){
            $id = intval($_POST['id']);

            $info=$this->model->table('gongshi')->where("`id`='$id'")->find();
            if($this->model->table('gongshi')->where("`id`='$id'")->delete()){
                $this->inslog('删除考核公式“<font color="#ff0000">'.$info['gongshi'].'</font>”。');
                $this->success('信息删除成功');
            }else{
                $this->error('信息删除失败');
            }
        }
    }
}