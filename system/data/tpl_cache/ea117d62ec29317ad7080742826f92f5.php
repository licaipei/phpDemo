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
                稿件统计管理
            </h1>
            <ol class="breadcrumb">
                <li><a href="<?php echo __APP__; ?>/index/"><i class="fa fa-dashboard"></i> 系统首页</a></li>
                <li>稿件统计管理</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title"><i class="fa fa-pie-chart">&nbsp;</i>稿件统计管理</h3>

                            <div class="box-tools">
                                <form action="<?php echo __URL__; ?>/search.html">
                                    <div class="input-group" style="width:200px; float:left; margin-left:10px;">
                                        <span class="input-group-addon"><i class="fa fa-tag">&nbsp;</i>关键词</span>
                                        <input type="text" name="keywords" class="form-control" placeholder="关键词或URL">
                                    </div>
                                    <button class="btn btn-sm btn-default pull-left" id="onsearch" style="float:left; margin-left:10px;"><i class="fa fa-search">&nbsp;</i>信息查询</button>
                                    <div style="clear:both"></div>
                                </form>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body table-responsive no-padding">
                            <table class="table table-hover">
                                <tr>
                                    <th>所属站点</th>
                                    <th>稿件标题</th>
                                    <th>稿件URL</th>
                                    <th<?php if(!empty($id)) { ?> style="display:none;"<?php } ?>>昨日(PV)</th>
                                    <th<?php if(!empty($id)) { ?> style="display:none;"<?php } ?>>今日(PV)</th>
                                    <th<?php if(!empty($id)) { ?> style="display:none;"<?php } ?>>总(PV)</th>
                                    <th<?php if(!empty($id)) { ?> style="display:none;"<?php } ?>>昨日(IP)</th>
                                    <th<?php if(!empty($id)) { ?> style="display:none;"<?php } ?>>今日(IP)</th>
                                    <th<?php if(!empty($id)) { ?> style="display:none;"<?php } ?>>总(IP)</th>
                                    <th <?php if(empty($id)) { ?>style="display:none"<?php } ?>>访问时间</th>
                                    <th <?php if(!empty($id)) { ?>style="display:none"<?php } ?>>
                                    管理操作
                                    </th>
                                </tr>
                                <?php if(!empty($list)) { ?><?php $n=1;if(is_array($list)) foreach($list AS $vo) { ?>
                                <tr>
                                    <td><i class="fa fa-sitemap" style="color:#39C">&nbsp;</i><a href="<?php echo __URL__; ?>/index.html?domainid=<?php echo $vo['domainid']; ?>"><?php echo $vo['sitename']; ?></a></td>
                                    <td><?php echo $vo['title']; ?></td>
                                    <td><a href="<?php echo $vo['url']; ?>" target="_blank" title="<?php echo $vo['url']; ?>"><i class="fa fa-internet-explorer" style="color:#090">&nbsp;</i>页面访问</a></td>
                                    <td<?php if(!empty($id)) { ?> style="display:none;"<?php } ?>><?php if(!empty($vo['hits1'])) { ?><?php echo $vo['hits1']; ?><?php } else { ?>0<?php } ?></td>
                                    <td<?php if(!empty($id)) { ?> style="display:none;"<?php } ?>><?php if(!empty($vo['hits2'])) { ?><?php echo $vo['hits2']; ?><?php } else { ?>0<?php } ?></td>
                                    <td<?php if(!empty($id)) { ?> style="display:none;"<?php } ?>><?php if(!empty($vo['hits'])) { ?><?php echo $vo['hits']; ?><?php } else { ?>0<?php } ?></td>
                                    <td<?php if(!empty($id)) { ?> style="display:none;"<?php } ?>><?php if(!empty($vo['ipnums1'])) { ?><?php echo $vo['ipnums1']; ?><?php } else { ?>0<?php } ?></td>
                                    <td<?php if(!empty($id)) { ?> style="display:none;"<?php } ?>><?php if(!empty($vo['ipnums2'])) { ?><?php echo $vo['ipnums2']; ?><?php } else { ?>0<?php } ?></td>
                                    <td<?php if(!empty($id)) { ?> style="display:none;"<?php } ?>><?php if(!empty($vo['ipnums'])) { ?><?php echo $vo['ipnums']; ?><?php } else { ?>0<?php } ?></td>
                                    <td <?php if(empty($id)) { ?>style="display:none"<?php } ?>><?php if(!empty($vo['uptime'])) { ?><?php echo date('Y-m-d G:i:s',$vo['uptime']);?><?php } else { ?>--<?php } ?></td>
                                    <td <?php if(!empty($id)) { ?>style="display:none"<?php } ?>>
                                    <div class="btn-group">

                                        <a href="<?php echo __URL__; ?>/detail-<?php echo $vo['id']; ?>.html" class="btn btn-xs btn-success"><i class="fa fa-eye"></i>&nbsp;查看访问统计</a>
                                    </div>
                                    </td>
                                </tr>
                                <?php $n++;}unset($n); ?><?php } else { ?>
                                <tr>
                                    <td colspan="6" class="red">
                                        <span class="label label-warning"><i class="icon-warning-sign bigger-120"></i>暂无稿件统计信息！</span>
                                    </td>
                                </tr>
                                <?php } ?>
                            </table>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer clearfix">
                            <ul class="pagination pagination-sm no-margin pull-right">
                                <?php echo $page; ?>
                            </ul>
                        </div>
                    </div>
                    <!-- /.box -->
                </div>
            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <?php $file=explode('.', "copy"); ?><?php $cpTemplate->display("$file[0]"); ?>
    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
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
<script src="<?php echo __ROOT__; ?>/../public/sweet/sweetalert.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo __ROOT__; ?>/../public/sweet/sweetalert.css">
<script type="text/javascript" src="<?php echo __ROOT__; ?>/../public/bootstrap/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<link href="<?php echo __ROOT__; ?>/../public/bootstrap/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
<link rel="stylesheet" href="<?php echo __ROOT__; ?>/../public/bootstrap/plugins/iCheck/all.css">
<script src="<?php echo __ROOT__; ?>/../public/bootstrap/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript">
    $(".form_datetime").datetimepicker({
        language:'cn',
        format: 'yyyy-mm-dd',
        minView: "month",
        autoclose: true,
        todayBtn: true
    });
    $(".input-group input[type='checkbox']").iCheck({
        checkboxClass: 'icheckbox_minimal-red',
        radioClass: 'iradio_minimal-red'
    });
</script>
</body>
</html>
