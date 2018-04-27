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
        <li class="li2">酒店管理</li>
        <li class="li2 li3">酒店列表</li>
    </ul>
</div>
<div class="main-jsgl main-sc">
    <p class="attention"><span>注意：</span>这里是等待审核的酒店列表，商家添加酒店后需要这里审核后才能显示！</p>
    <div class="jsglNr">
        <div class="selectNr" style="margin-top: 0px; border-top:none;">
            <div class="left">
                <?php echo BA('hotel/create','','添加酒店');?>
            </div>
            <div class="right">
                <form class="search_form" method="post" action="<?php echo U('hotel/noaudit');?>">
                    <div class="seleHidden" id="seleHidden">
                        <span>关键字(电话或商户名称)</span>
                        <input type="text" name="keyword" value="<?php echo ($keyword); ?>" class="inptText" /><input type="submit" value="   搜索"  class="inptButton" />
                    </div> 
                </form>
                <a href="javascript:void(0);" class="searchG">高级搜索</a>
                <div class="clear"></div>
            </div>
            <div class="clear"></div>
        </div>
        <form method="post" action="<?php echo U('hotel/noaudit');?>">
            <div class="selectNr selectNr2">
                <div class="left">
                    <div class="seleK">
                        <label>
                            <span>分类：</span>
                            <select id="cate_id" name="cate_id" class="select w100">
                                <option value="0">请选择...</option>
                                <?php if(is_array($cates)): foreach($cates as $key=>$var): if(($var["parent_id"]) == "0"): ?><option value="<?php echo ($var["cate_id"]); ?>"  <?php if(($var["cate_id"]) == $cate_id): ?>selected="selected"<?php endif; ?> ><?php echo ($var["cate_name"]); ?></option>                
                                    <?php if(is_array($cates)): foreach($cates as $key=>$var2): if(($var2["parent_id"]) == $var["cate_id"]): ?><option value="<?php echo ($var2["cate_id"]); ?>"  <?php if(($var2["cate_id"]) == $cate_id): ?>selected="selected"<?php endif; ?> >&nbsp;&nbsp;<?php echo ($var2["cate_name"]); ?></option><?php endif; endforeach; endif; endif; endforeach; endif; ?>
                            </select>
                        </label>
                        <label>
                            <span>区域：</span>
                             <select name="city_id" id="city_id"  class="select w100"></select>
                            <select name="area_id" id="area_id"  class="select w100"></select>
                     
                        </label>
                <script src="<?php echo U('app/datas/cityarea');?>"></script>
                <script>
                    var city_id = <?php echo (int)$city_id;?>;
                    var area_id = <?php echo (int)$area_id;?>;
                    function changeCity(cid){
                        var area_str = '<option value="0">请选择.....</option>';
                        for(a in cityareas.area){
                           if(cityareas.area[a].city_id ==cid){
                                if(area_id == cityareas.area[a].area_id){
                                    area_str += '<option selected="selected" value="'+cityareas.area[a].area_id+'">'+cityareas.area[a].area_name+'</option>';
                                }else{
                                     area_str += '<option value="'+cityareas.area[a].area_id+'">'+cityareas.area[a].area_name+'</option>';
                                }  
                            }
                        }
                        $("#area_id").html(area_str);
                    }
                    $(document).ready(function(){
                        var city_str = '<option value="0">请选择.....</option>';
                        for(a in cityareas.city){
                           if(city_id == cityareas.city[a].city_id){
                               city_str += '<option selected="selected" value="'+cityareas.city[a].city_id+'">'+cityareas.city[a].name+'</option>';
                           }else{
                                city_str += '<option value="'+cityareas.city[a].city_id+'">'+cityareas.city[a].name+'</option>';
                           }  
                        }
                        $("#city_id").html(city_str);
                        if(city_id){
                            changeCity(city_id);
                        }
                        $("#city_id").change(function(){
                            city_id = $(this).val();
                            changeCity($(this).val());
                        });
                    });
                </script>
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
                    <td class="w50"><input type="checkbox" class="checkAll" rel="hotel_id" /></td>
                    <td class="w50">ID</td>
                    <td>酒店名称</td>
                    <td>酒店级别</td>
                    <td>酒店品牌</td>
                    <td>酒店电话</td>
                    <td>酒店地址</td>
                    <td>酒店起价</td>
                    <td>酒店图片</td>	
                    <td>状态</td>	
                    <td>入住时间</td>
                    <td class="w360">操作</td>
                </tr>
                <?php if(is_array($list)): foreach($list as $key=>$var): ?><tr>
                        <td><input class="child_hotel_id" type="checkbox" name="hotel_id[]" value="<?php echo ($var["hotel_id"]); ?>" /></td>
                        <td><?php echo ($var["hotel_id"]); ?></td>
                        <td><?php echo ($var["hotel_name"]); ?>(商家ID:<?php echo ($var["shop_id"]); ?>)</td>
                        <td><?php echo ($cates[$var['cate_id']]); ?></td>
                        <td><?php echo ($hoteltypes[$var['type']]['title']); ?></td>
                        <td><?php echo ($var["tel"]); ?></td>
                        <td><?php echo ($var["addr"]); ?></td>
                        <td><?php echo ($var["price"]); ?></td>
                        <td><img src="<?php echo config_img($var['photo']);?>" class="w80" /></td>
                        <td><?php if($var["audit"] == 0): ?>未审核<?php elseif($var["audit"] == 2): ?><font style="color: red;">已拒绝</font><?php endif; ?></td>
                        <td><?php echo (date('Y-m-d H:i:s',$var["create_time"])); ?></td>
                    <td>
                    <?php echo BA('hotel/audit',array("hotel_id"=>$var["hotel_id"]),'审核','act','remberBtn');?>
                    <?php if($var["audit"] != 2): echo BA('hotel/refuse',array("hotel_id"=>$var["hotel_id"]),'拒绝','load','remberBtn',400,250); endif; ?>
                    <?php echo BA('hotel/edit',array("hotel_id"=>$var["hotel_id"]),'编辑','','remberBtn');?>
                    <?php echo BA('hotel/delete',array("hotel_id"=>$var["hotel_id"]),'删除','act','remberBtn');?>
                    </td>
                    </tr><?php endforeach; endif; ?>
            </table>
            <?php echo ($page); ?>
        </div>
        <div class="selectNr" style="margin-bottom: 0px; border-bottom: none;">
            <div class="left">
                <?php echo BA('hotel/delete','','批量删除','list',' a2');?>
                <?php echo BA('hotel/audit','','批量审核','list',' remberBtn');?>
            </div>
        </div>
    </form>
</div>
</div>

     
        
</div>
</body>
</html>