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
        <li class="li2">部落管理</li>
        <li class="li2 li3">部落列表</li>
    </ul>
</div>
<div class="main-jsgl main-sc">
    <p class="attention"><span>注意：</span>&#28304;&#12288;&#30721;&#12288;&#30001;&#12288;&#25240;&#12288;&#32764;&#12288;&#22825;&#12288;&#20351;&#12288;&#36164;&#12288;&#28304;&#12288;&#31038;&#12288;&#21306;&#12288;&#25552;&#12288;&#20379;</p>
    <div class="jsglNr">
        <div class="selectNr" style="margin-top: 0px; border-top:none;">
            <div class="left">
                <?php echo BA('tribe/create','','添加部落');?>
            </div>
            <div class="right">
                <form class="search_form" method="post" action="<?php echo U('tribe/index');?>">
                    <div class="seleHidden" id="seleHidden">
                        <span>关键字(部落名称)</span>
                        <input type="text" name="keyword" value="<?php echo ($keyword); ?>" class="inptText" /><input type="submit" value="   搜索"  class="inptButton" />
                    </div> 
                </form>
                <a href="javascript:void(0);" class="searchG">高级搜索</a>
                <div class="clear"></div>
            </div>
            <div class="clear"></div>
        </div>
        <form method="post" action="<?php echo U('tribe/index');?>">
            <div class="selectNr selectNr2">
                <div class="left">
                    <div class="seleK">
                        <label>
                            <span>分类：</span>
                            <select id="cate_id" name="cate_id" class="select w100">
                                <option value="0">请选择...</option>
                                <?php if(is_array($cates)): foreach($cates as $key=>$var): ?><option value="<?php echo ($var["cate_id"]); ?>"  <?php if(($var["cate_id"]) == $cate_id): ?>selected="selected"<?php endif; ?> ><?php echo ($var["cate_name"]); ?></option><?php endforeach; endif; ?>
                            </select>
                        </label>
                        <label>
                            <span>关键字:</span>
                            <input type="text" name="keyword" value="<?php echo ($keyword); ?>" class="inptText" />
                        </label>
                    </div>
                </div>
                <div class="right">
                    <input type="submit" value="   搜索"  class="inptButton" />
                </div>
        </form>
        <div class="clear"></div>
    </div>
    <form  target="baocms_frm" method="post">
        <div class="tableBox">
            <table bordercolor="#dbdbdb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;"  >
                <tr>
                    <td class="w50"><input type="checkbox" class="checkAll" rel="tribe_id" /></td>
                    <td class="w50">ID</td>
                    <td>部落名称</td>
                    <td>部落分类</td>
                    <td>部落图片</td>
                    <td class="w300">部落banner</td>
                    <td>部落帖子数</td>
                    <td>部落关注数</td>
                    <td>是否热门</td>
                    <td>创建时间</td>
                    <td class="w360">操作</td>
                </tr>
                <?php if(is_array($list)): foreach($list as $key=>$var): ?><tr>
                        <td><input class="child_tribe_id" type="checkbox" name="tribe_id[]" value="<?php echo ($var["tribe_id"]); ?>" /></td>
                        <td><?php echo ($var["tribe_id"]); ?></td>
                        <td><?php echo ($var["tribe_name"]); ?></td>
                        <td><?php echo ($cates[$var['cate_id']]['cate_name']); ?></td>
                        <td><img src="__ROOT__/attachs/<?php echo ($var["photo"]); ?>" class="w80" /></td>
                        <td><img src="__ROOT__/attachs/<?php echo ($var["banner"]); ?>" class="w300" /></td>
                        <td><?php echo ($var["posts"]); ?></td>
                        <td><?php echo ($var["fans"]); ?></td>
                        <td><?php if(($var["is_hot"]) == "1"): ?>是<?php else: ?>否<?php endif; ?></td>
                        <td><?php echo (date('Y-m-d H:i:s',$var["create_time"])); ?></td>
                    <td>
                    <?php echo BA('tribe/edit',array("tribe_id"=>$var["tribe_id"]),'编辑','','remberBtn');?>
                    <?php echo BA('tribepost/create',array("tribe_id"=>$var["tribe_id"]),'发帖','','remberBtn');?>
                    <?php echo BA('tribe/delete',array("tribe_id"=>$var["tribe_id"]),'删除','act','remberBtn');?>
                    </td>
                    </tr><?php endforeach; endif; ?>
            </table>
            <?php echo ($page); ?>
        </div>
        <div class="selectNr" style="margin-bottom: 0px; border-bottom: none;">
            <div class="left">
                <?php echo BA('tribe/delete','','批量删除','list',' a2');?>
            </div>
        </div>
    </form>
</div>
</div>

     
        
</div>
</body>
</html>