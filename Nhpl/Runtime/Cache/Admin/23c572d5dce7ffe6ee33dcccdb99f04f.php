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
        <li class="li1">部落</li>
        <li class="li2">部落分类</li>
        <li class="li2 li3">分类列表</li>
    </ul>
</div>
<div class="main-cate">
    <p class="attention"><span>注意：</span></p>
    <div class="jsglNr">
        <div class="selectNr" style="border-top: 1px solid #dbdbdb;">
            <div class="left">
                <?php echo BA('tribecate/create','','添加分类','load','',500,280);?>
            </div>
        </div>

        <div class="tableBox">
            <form  target="baocms_frm" method="post">
                <table bordercolor="#dbdbdb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF; text-align:center;">
                    <tr bgcolor="#ffaa7b" height="48px;" style="color:#FFF; font-size:16px; line-height:48px;">
                        <td>分类名称</td>
                        <td>排序</td>
                        <td>操作</td>
                    </tr>

                    <?php if(is_array($list)): foreach($list as $key=>$var): ?><tr bgcolor="#f1f1f1" height="48px" style="font-size:14px; color:#545454; text-align:left; line-height:48px;">
                            <td style="padding-left:20px;"><?php echo ($var["cate_name"]); ?>(<?php echo ($var["cate_id"]); ?>)</td>
                            <td style="padding-left:70px;"><input name="orderby[<?php echo ($var["cate_id"]); ?>]" value="<?php echo ($var["orderby"]); ?>" type="text" class="remberinput w80" /></td>
                            <td style="text-align:center;"> 
                        <?php echo BA('tribecate/edit',array("cate_id"=>$var["cate_id"]),'编辑','load','remberBtn',300,280);?>
                        <?php echo BA('tribecate/delete',array("cate_id"=>$var["cate_id"]),'删除','act','remberBtn');?>
                        </td>
                        </tr><?php endforeach; endif; ?>     
                </table>
                <div class="selectNr">
                    <div class="left">
                        <?php echo BA('tribecate/update','','更新','list','remberBtn');?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

     
        
</div>
</body>
</html>