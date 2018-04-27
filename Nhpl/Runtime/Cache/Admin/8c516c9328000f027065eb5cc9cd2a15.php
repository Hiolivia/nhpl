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
        <li class="li1">酒店</li>
        <li class="li2">酒店品牌</li>
        <li class="li2 li3">品牌列表</li>
    </ul>
</div>
<div class="main-cate">
    <p class="attention"><span>注意：</span>这里是添加酒店品牌的，添加品牌后商家添加酒店的时候即可选择此品牌！</p>
    <div class="jsglNr">
    <form  target="baocms_frm" method="post">
        <div class="selectNr" style="border-top: 1px solid #dbdbdb;">
            <div class="left">
                <?php echo BA('hotelbrand/create','','添加品牌','load','',500,280);?>
            </div>
            <div class="right">
                     <?php echo BA('hotelbrand/update','','更新','list','remberBtn');?>
           </div>
        </div>

        <div class="tableBox">
            
                <table bordercolor="#dbdbdb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF; text-align:center;">
                    <tr bgcolor="#eee" height="48px;" style="color:#666; font-size:16px; line-height:48px;">
                        <td>品牌名称</td>
                        <td>该品牌下面拥有酒店数量</td>
                        <td>排序</td>
                        <td>操作</td>
                    </tr>

                    <?php if(is_array($list)): foreach($list as $key=>$var): ?><tr bgcolor="#fff" height="48px" style="font-size:14px; color:#545454; text-align:left; line-height:48px;">
                            <td style="padding-left:20px;"><?php echo ($var["title"]); ?>(<?php echo ($var["type"]); ?>)</td>
                            <?php $num = D('Hotel')->where(array('type'=>$var['type']))->count(); ?>
                            <td style="text-align:center"><?php echo ($num); ?> 家酒店</td>
                            <td style="padding-left:70px;"><input name="orderby[<?php echo ($var["type"]); ?>]" value="<?php echo ($var["orderby"]); ?>" type="text" class="remberinput w80" /></td>
                            <td style="text-align:center;"> 
                        <?php echo BA('hotelbrand/edit',array("type"=>$var["type"]),'编辑','load','remberBtn',300,280);?>
                        <?php echo BA('hotelbrand/delete',array("type"=>$var["type"]),'删除','act','remberBtn');?>
                        </td>
                        </tr><?php endforeach; endif; ?>     
                </table>
            
        </div>
    </div>
</div>
</form>

     
        
</div>
</body>
</html>