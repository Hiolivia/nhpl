<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
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
        <li class="li2">数据库</li>
        <li class="li2 li3">数据库备份</li>
    </ul>
</div>
<div class="main-jsgl">
    <p class="attention"><span>注意：</span>推荐亲用多备份等云备份哦！</p>
    <div class="jsglNr">
        <div class="selectNr">
            <div class="left">
                <a id="export" class="btn" href="javascript:;" autocomplete="off">立即备份</a>
                <a id="optimize" class="btn" href="<?php echo U('optimize');?>">优化表</a>
                <a id="repair" class="btn" href="<?php echo U('repair');?>">修复表</a>
				<a id="repair" class="btn" href="<?php echo U('index?type=import');?>">数据库还原</a>
            </div>
            <div class="right">
                表总数：<?php echo count($list); ?>
            </div>
        </div>
        <form  id="export-form" target="baocms_frm" method="post" action="<?php echo U('export');?>">
            <div class="tableBox">
                <table bordercolor="#e1e6eb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;"  >
                    <tr>
                        <td width="48"><input class="check-all" checked="chedked" type="checkbox" value=""></td>
                        <td>表名</td>
                        <td>数据量</td>
                        <td>数据大小</td>
                        <td>创建时间</td>
                        <td>备份状态</td>
                        <td>操作</td>
                    </tr>
                    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$table): $mod = ($i % 2 );++$i;?><tr>
                            <td class="num">
                                <input class="ids" checked="chedked" type="checkbox" name="tables[]" value="<?php echo ($table["name"]); ?>">
                            </td>
                            <td><?php echo ($table["name"]); ?></td>
                            <td><?php echo ($table["rows"]); ?></td>
                            <td><?php echo (format_bytes($table["data_length"])); ?></td>
                            <td><?php echo ($table["create_time"]); ?></td>
                            <td class="info ">未备份</td>
                            <td class="action">
                                <a class="remberBtn" href="<?php echo U('optimize?tables='.$table['name']);?>">优化表</a>&nbsp;
                                <a class="remberBtn" href="<?php echo U('repair?tables='.$table['name']);?>">修复表</a>
                            </td>
                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>

                </table>
                <?php echo ($page); ?>
            </div>
        </form>

    </div>
</div>


<script type="text/javascript">
    (function($){
        var $form = $("#export-form"), $export = $("#export"), tables
            $optimize = $("#optimize"), $repair = $("#repair");

        $optimize.add($repair).click(function(){
            $.post(this.href, $form.serialize(), function(data){
                if(data.status){
                    alert(data.info);
                } else {
                    alert(data.info,'alert-error');
                }
            }, "json");
            return false;
        });

        $export.click(function(){
            $export.parent().children().addClass("disabled");
            $export.html("正在发送备份请求...");
            $.post(
                $form.attr("action"),
                $form.serialize(),
                function(data){
                    if(data.status){
                        tables = data.tables;
                        $export.html(data.info + "开始备份，请不要关闭本页面！");
                        backup(data.tab);
                        window.onbeforeunload = function(){ return "正在备份数据库，请不要关闭！" }
                    } else {
                        alert(data.info);
                        $export.parent().children().removeClass("disabled");
                        $export.html("立即备份");
                    }
                },
                "json"
            );
            return false;
        });

        function backup(tab, status){
            status && showmsg(tab.id, "开始备份...(0%)");
            $.get($form.attr("action"), tab, function(data){
                if(data.status){
                    showmsg(tab.id, data.info);

                    if(!$.isPlainObject(data.tab)){
                        $export.parent().children().removeClass("disabled");
                        $export.html("备份完成，点击重新备份");
                        window.onbeforeunload = function(){ return null }
                        return;
                    }
                    backup(data.tab, tab.id != data.tab.id);
                } else {
                    alert(data.info);
                    $export.parent().children().removeClass("disabled");
                    $export.html("立即备份");
                }
            }, "json");

        }

        function showmsg(id, msg){
            $form.find("input[value=" + tables[id] + "]").closest("tr").find(".info").html(msg);
        }
    })(jQuery);
    </script>

     
        
</div>
</body>
</html>