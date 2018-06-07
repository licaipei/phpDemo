<?php if (!defined('CANPHP')) exit;?>  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <?php if(!empty($leftmenu)) { ?>
      <ul class="sidebar-menu">
        <?php $n=1;if(is_array($leftmenu)) foreach($leftmenu AS $vo) { ?>
        <li class="treeview active">
          <a href="#">
            <i class="fa <?php if(!empty($vo['icon'])) { ?><?php echo $vo['icon']; ?><?php } else { ?>fa-list-alt<?php } ?>"></i> <span><?php echo $vo['title']; ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <?php if(empty($vo['children'])) { ?>
          <?php if(!empty($childmenu[$vo['menu_id']])) { ?>
          <ul class="treeview-menu">
            <?php $n=1;if(is_array($childmenu[$vo['menu_id']])) foreach($childmenu[$vo['menu_id']] AS $ho) { ?>
            <li <?php if($ho['menu_id']==$onmenu) { ?>class="active"<?php } ?>><a href="<?php echo __APP__; ?>/<?php echo $ho['module']; ?>/<?php echo $ho['action']; ?>"><i class="fa <?php if(!empty($ho['icon'])) { ?><?php echo $ho['icon']; ?><?php } else { ?>fa-circle-o<?php } ?>"></i> <?php echo $ho['title']; ?></a></li>
            <?php $n++;}unset($n); ?>
          </ul>
          <?php } ?>
          <?php } ?>
          <?php if(!empty($vo['children'])) { ?>
          <ul class="treeview-menu">
            <?php $n=1;if(is_array($vo['children'])) foreach($vo['children'] AS $vo1) { ?>
            <li>
              <a href="#"><i class="fa <?php if(!empty($vo1['icon'])) { ?><?php echo $vo1['icon']; ?><?php } else { ?>fa-list-alt<?php } ?>"></i> <?php echo $vo1['title']; ?>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <?php if(empty($vo1['children'])) { ?>
              <?php if(!empty($childmenu[$vo1['menu_id']])) { ?>
              <ul class="treeview-menu">
                <?php $n=1;if(is_array($childmenu[$vo1['menu_id']])) foreach($childmenu[$vo1['menu_id']] AS $ho1) { ?>
                <li <?php if($ho1['action']==$sysaction) { ?>class="active"<?php } ?>><a href="<?php echo __APP__; ?>/<?php echo $ho1['module']; ?>/<?php echo $ho1['action']; ?>"><i class="fa fa-circle-o"></i> <?php echo $ho1['title']; ?></a></li>
                <?php $n++;}unset($n); ?>
              </ul>
              <?php } ?>
              <?php } ?>
              <?php if(!empty($vo1['children'])) { ?>
              <ul class="treeview-menu">
                <?php $n=1;if(is_array($vo1['children'])) foreach($vo1['children'] AS $vo2) { ?>
                <li>
                  <a href="#"><i class="fa <?php if(!empty($vo2['icon'])) { ?><?php echo $vo2['icon']; ?><?php } else { ?>fa-list-alt<?php } ?>"></i> <?php echo $vo2['title']; ?>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                  </a>
                  <?php if(empty($vo2['children'])) { ?>
                  <?php if(!empty($childmenu[$vo2['menu_id']])) { ?>
                  <ul class="treeview-menu">
                    <?php $n=1;if(is_array($childmenu[$vo2['menu_id']])) foreach($childmenu[$vo2['menu_id']] AS $ho2) { ?>
                    <li <?php if($ho2['action']==$sysaction) { ?>class="active"<?php } ?>><a href="<?php echo __APP__; ?>/<?php echo $ho2['module']; ?>/<?php echo $ho2['action']; ?>"><i class="fa fa-circle-o"></i> <?php echo $ho2['title']; ?></a></li>
                    <?php $n++;}unset($n); ?>
                  </ul>
                  <?php } ?>
                  <?php } ?>
                  <?php if(!empty($vo2['children'])) { ?>
                  <ul class="treeview-menu">
                    <?php $n=1;if(is_array($vo2['children'])) foreach($vo2['children'] AS $vo3) { ?>
                    <li>
                      <a href="#"><i class="fa <?php if(!empty($vo3['icon'])) { ?><?php echo $vo3['icon']; ?><?php } else { ?>fa-list-alt<?php } ?>"></i> <?php echo $vo3['title']; ?>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-left pull-right"></i>
                        </span>
                      </a>
                      <?php if(empty($vo3['children'])) { ?>
                      <?php if(!empty($childmenu[$vo3['menu_id']])) { ?>
                      <ul class="treeview-menu">
                        <?php $n=1;if(is_array($childmenu[$vo3['menu_id']])) foreach($childmenu[$vo3['menu_id']] AS $ho3) { ?>
                        <li <?php if($ho3['action']==$sysaction) { ?>class="active"<?php } ?>><a href="<?php echo __APP__; ?>/<?php echo $ho3['module']; ?>/<?php echo $ho3['action']; ?>"><i class="fa fa-circle-o"></i> <?php echo $ho3['title']; ?></a></li>
                        <?php $n++;}unset($n); ?>
                      </ul>
                      <?php } ?>
                      <?php } ?>
                    </li>
                    <?php $n++;}unset($n); ?>
                  </ul>
                  <?php } ?>
                </li>
                <?php $n++;}unset($n); ?>
              </ul>
              <?php } ?>
            </li>
            <?php $n++;}unset($n); ?>
          </ul>
          <?php } ?>
        </li>
        <?php $n++;}unset($n); ?>
      </ul>
      <?php } ?>
    </section>
    <!-- /.sidebar -->
  </aside>
