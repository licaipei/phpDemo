<?php if (!defined('CANPHP')) exit;?><!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $sys['systemname']; ?></title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo __ROOT__; ?>/../public/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo __ROOT__; ?>/../public/font-awesome4.5.0/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo __ROOT__; ?>/../public/bootstrap/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo __ROOT__; ?>/../public/bootstrap/dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo __ROOT__; ?>/../public/bootstrap/dist/css/skins/_all-skins.min.css">
  <!--[if lt IE 9]>
  <script src="<?php echo __ROOT__; ?>/../public/js/html5shiv.min.js"></script>
  <script src="<?php echo __ROOT__; ?>/../public/js/respond.min.js"></script>
  <![endif]-->
</head>
<body class="hold-transition fixed skin-blue sidebar-mini">
<div class="wrapper">
  <!--头部公用部分-->
  <?php $file=explode('.', "top"); ?><?php $cpTemplate->display("$file[0]"); ?>
  <!--左部菜单公用部分-->
  <?php $file=explode('.', "menu"); ?><?php $cpTemplate->display("$file[0]"); ?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        系统首页
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo __APP__; ?>/index"><i class="fa fa-dashboard"></i> 系统首页</a></li>
        <li class="active">欢迎界面</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <?php if(in_array('部门信息管理',$powertit) or $groupid==-1){?>
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3><?php echo $departnums; ?><sup style="font-size: 20px">个部门</sup></h3>
              <p>部门信息管理</p>
            </div>
            <div class="icon">
              <i class="fa fa-sitemap"></i>
            </div>
            <a href="<?php echo __APP__; ?>/department/index.html" class="small-box-footer">点击进入<i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <?php }?>
        <?php if(in_array('员工信息管理',$powertit) or $groupid==-1){?>
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h3><?php echo $workernums; ?><sup style="font-size: 20px">人</sup></h3>

              <p>员工信息管理</p>
            </div>
            <div class="icon">
              <i class="ion ion-person-add"></i>
            </div>
            <a href="<?php echo __APP__; ?>/workers/index.html" class="small-box-footer">点击进入<i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <?php }?>
        <?php if(in_array('考核信息管理',$powertit) or $groupid==-1){?>
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3><?php echo $examnums; ?><sup style="font-size: 20px">条考核信息</sup></h3>

              <p>考核信息管理</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
            <a href="<?php echo __APP__; ?>/examine/index.html" class="small-box-footer">点击进入<i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <?php }?>
        <?php if(in_array('点击量统计管理',$powertit) or $groupid==-1){?>
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-red">
            <div class="inner">
              <h3><?php echo $visitnums; ?><sup style="font-size: 20px">条记录</sup></h3>

              <p>稿件统计管理</p>
            </div>
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
            <a href="<?php echo __APP__; ?>/visit/index.html" class="small-box-footer">点击进入<i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <?php }?>
      </div>
      <!-- /.row -->
     </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <?php $file=explode('.', "copy"); ?><?php $cpTemplate->display("$file[0]"); ?>
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- jQuery 2.2.3 -->
<script src="<?php echo __ROOT__; ?>/../public/bootstrap/plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="<?php echo __ROOT__; ?>/../public/bootstrap/js/bootstrap.min.js"></script>
<!-- FastClick -->
<script src="<?php echo __ROOT__; ?>/../public/bootstrap/plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo __ROOT__; ?>/../public/bootstrap/dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo __ROOT__; ?>/../public/bootstrap/dist/js/demo.js"></script>
</body>
</html>
