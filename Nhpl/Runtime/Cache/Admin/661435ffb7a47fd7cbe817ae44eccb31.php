<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo ($CONFIG["site"]["title"]); ?>管理后台</title>
        <meta name="description" content="<?php echo ($CONFIG["site"]["title"]); ?>管理后台" />
        <meta name="keywords" content="<?php echo ($CONFIG["site"]["title"]); ?>管理后台" />
        <!-- <link href="__TMPL__statics/css/index.css" rel="stylesheet" type="text/css" /> -->
        <link href="__TMPL__statics/css/style.css" rel="stylesheet" type="text/css" />
        <link href="__TMPL__statics/css/land.css" rel="stylesheet" type="text/css" />
        <link href="__TMPL__statics/css/pub.css" rel="stylesheet" type="text/css" />
        <link href="__TMPL__statics/css/main.css" rel="stylesheet" type="text/css" />
        <link href="__PUBLIC__/js/jquery-ui.css" rel="stylesheet" type="text/css" />
        <script> var BAO_PUBLIC = '__PUBLIC__'; var BAO_ROOT = '__ROOT__'; </script>
        <script src="__PUBLIC__/js/jquery.js"></script>
        <script src="__PUBLIC__/js/jquery-ui.min.js"></script>
        <script src="__PUBLIC__/js/my97/WdatePicker.js"></script>
        <script src="/Public/js/layer/layer.js"></script>
        <script src="__PUBLIC__/js/admin.js?v=20150409"></script>
    </head>
    
    
    </head>
<style type="text/css">
#ie9-warning{ background:#F00; height:38px; line-height:38px; padding:10px;
position:absolute;top:0;left:0;font-size:12px;color:#fff;width:97%;text-align:left; z-index:9999999;}
#ie6-warning a {text-decoration:none; color:#fff !important;}
</style>

<!--[if lte IE 9]>
<div id="ie9-warning">您正在使用 Internet Explorer 9以下的版本，请用谷歌浏览器访问后台、部分浏览器可以开启极速模式访问！不懂点击这里！ <a href="http://www.fengmiyuanma.com/10478.html" target="_blank">查看为什么？</a>
</div>
<script type="text/javascript">
function position_fixed(el, eltop, elleft){  
       // check if this is IE6  
       if(!window.XMLHttpRequest)  
              window.onscroll = function(){  
                     el.style.top = (document.documentElement.scrollTop + eltop)+"px";  
                     el.style.left = (document.documentElement.scrollLeft + elleft)+"px";  
       }  
       else el.style.position = "fixed";  
}
       position_fixed(document.getElementById("ie9-warning"),0, 0);
</script>
<![endif]-->


    <body>
         <iframe id="baocms_frm" name="baocms_frm" style="display:none;"></iframe>
   <div class="main">
<div class="mainBt">
    <ul>
        <li class="li1">系统</li>
        <li class="li2">管理员管理</li>
        <li class="li2 li3">管理员管理</li>
    </ul>
</div>
<div class="main-jsgl">
    <p class="attention"><span>注意：</span>每个角色有对应的权限，默认超级管理员角色不能删除！&#28304;&#12288;&#30721;&#12288;&#30001;&#12288;&#25240;&#12288;&#32764;&#12288;&#22825;&#12288;&#20351;&#12288;&#36164;&#12288;&#28304;&#12288;&#31038;&#12288;&#21306;&#12288;&#25552;&#12288;&#20379;</p>
    <div class="jsglNr">
        <div class="selectNr">
            <div class="left">
                <?php echo BA('admin/create','','添加管理员','load','',500,450);?>
            </div>
            <div class="right">
                <form method="post" action="<?php echo U('admin/index');?>">
                    <input type="text"  class="inptText" name="keyword" value="<?php echo ($keyword); ?>"  /><input type="submit" value="   搜索"  class="inptButton" />
                </form>
            </div>
        </div>
        <form target="baocms_frm" method="post">
            <div class="tableBox">
                <table bordercolor="#dbdbdb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;"  >
                    <tr>
                        <td class="w50">ID</td>
                        <td>用户名</td>
                        <td>角色</td>
                        <td>手机</td>
                        <td>创建时间</td>
                        <td>创建IP</td>
                        <td>最后登录时间</td>
                        <td>最后登录IP</td>
                        <td>操作</td>   
                    </tr>
                    <?php if(is_array($list)): foreach($list as $key=>$var): ?><tr>
                            <td><?php echo ($var["admin_id"]); ?></td>
                            <td><?php echo ($var["username"]); ?> <?php if($var["city_id"] > 0): ?>(<?php echo ($citys[$var['city_id']]['name']); ?>)<?php endif; ?></td>
                            <td><?php echo ($var["role_name"]); ?></td>
                            <td><?php echo ($var["mobile"]); ?></td>
                            <td><?php echo (date("Y-m-d H:i:s",$var["create_time"])); ?></td>
                            <td><?php echo ($var["create_ip"]); ?>(<?php echo ($var["create_ip_area"]); ?>)</td>
                            <td><?php echo (date("Y-m-d H:i:s",$var["last_time"])); ?></td>
                            <td><?php echo ($var["last_ip"]); ?>(<?php echo ($var["last_ip_area"]); ?>)</td>
                            <td>
                                <?php echo BA('admin/edit',array("admin_id"=>$var["admin_id"]),'编辑','load','remberBtn',500,450);?>
                                <?php echo BA('admin/delete',array("admin_id"=>$var["admin_id"]),'删除','act','remberBtn');?>
                            </td>
                        </tr><?php endforeach; endif; ?>

                </table>
                <?php echo ($page); ?>
            </div>
        </form>

    </div>
</div>

     
        
</div>
</body>
</html>