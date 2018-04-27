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

        <li class="li1">设置</li>

        <li class="li2">基本设置</li>

        <li class="li2 li3">附件设置</li>

    </ul>

</div>

<p class="attention"><span>注意：</span>这里是控制全局缩略图大小等设置的&#28304;&#12288;&#30721;&#12288;&#30001;&#12288;&#25240;&#12288;&#32764;&#12288;&#22825;&#12288;&#20351;&#12288;&#36164;&#12288;&#28304;&#12288;&#31038;&#12288;&#21306;&#12288;&#25552;&#12288;&#20379;<a href="http://www.zheyitianshi.com">http://www.zheyitianshi.com</a></p>

<form  target="baocms_frm" action="<?php echo U('setting/attachs');?>" method="post">

    <div class="mainScAdd">

        <div class="tableBox">

            <table  bordercolor="#dbdbdb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;" >

                <tr>

                    <td class="lfTdBt">水印图片：</td>

                    <td class="rgTdBt"><div style="width: 300px; height: 100px; float: left;">

                            <input type="hidden" name="data[water]" value="<?php echo ($CONFIG["attachs"]["water"]); ?>" id="data_photo" />

                            <input id="photo_file" name="photo_file" type="file" multiple="true" value="" />

                        </div>

                        <div style="width: 300px; height: 100px; float: left;">

                            <img id="photo_img" width="100" height="80"  src="__ROOT__/attachs/<?php echo (($CONFIG["attachs"]["water"])?($CONFIG["attachs"]["water"]):'default.jpg'); ?>" />

                        </div>

                <script type="text/javascript" src="__PUBLIC__/js/uploadify/jquery.uploadify.min.js"></script>

                <link rel="stylesheet" href="__PUBLIC__/js/uploadify/uploadify.css">

                <script>

                    $("#photo_file").uploadify({

                        'swf': '__PUBLIC__/js/uploadify/uploadify.swf?t=<?php echo ($nowtime); ?>',

                        'uploader': '<?php echo U("app/upload/uploadify",array("model"=>"setting"));?>',

                        'cancelImg': '__PUBLIC__/js/uploadify/uploadify-cancel.png',

                        'buttonText': '设置水印图片',

                        'fileTypeExts': '*.gif;*.jpg;*.png',

                        'queueSizeLimit': 1,

                        'onUploadSuccess': function (file, data, response) {

                            $("#data_photo").val(data);

                            $("#photo_img").attr('src', '__ROOT__/attachs/' + data).show();

                        }

                    });

                </script>

                </td>

                </tr>

                <tr>

                    <td class="lfTdBt">店铺LOGO：</td>

                    <td class="rgTdBt"><input type="text" name="data[shoplogo][thumb]" value="<?php echo ($CONFIG["attachs"]["shoplogo"]["thumb"]); ?>" class="scAddTextName" /> </td>

                </tr>

                <tr>

                    <td class="lfTdBt">商场LOGO：</td>

                    <td class="rgTdBt"><input type="text" name="data[marketlogo][thumb]" value="<?php echo ($CONFIG["attachs"]["marketlogo"]["thumb"]); ?>" class="scAddTextName" /> </td>

                </tr>

                <tr>

                    <td class="lfTdBt">商场缩略图：</td>

                    <td class="rgTdBt"><input type="text" name="data[market][thumb]" value="<?php echo ($CONFIG["attachs"]["market"]["thumb"]); ?>" class="scAddTextName" /> </td>

                </tr>

                <tr>

                    <td class="lfTdBt">商场环境图：</td>

                    <td class="rgTdBt"><input type="text" name="data[marketpic][thumb]" value="<?php echo ($CONFIG["attachs"]["marketpic"]["thumb"]); ?>" class="scAddTextName" /> </td>

                </tr>

                <tr>

                    <td class="lfTdBt">网站LOGO：</td>

                    <td class="rgTdBt"><input type="text" name="data[sitelogo][thumb]" value="<?php echo ($CONFIG["attachs"]["sitelogo"]["thumb"]); ?>" class="scAddTextName" /> </td>

                </tr>

                <tr>

                    <td class="lfTdBt">商家新闻：</td>

                    <td class="rgTdBt"><input type="text" name="data[shopnews][thumb]" value="<?php echo ($CONFIG["attachs"]["shopnews"]["thumb"]); ?>" class="scAddTextName" /> </td>

                </tr>

                <tr>

                    <td class="lfTdBt">家政缩略图、组图：</td>

                    <td class="rgTdBt"><input type="text" name="data[lifeservice][thumb]" value="<?php echo ($CONFIG["attachs"]["lifeservice"]["thumb"]); ?>" class="scAddTextName" /> 

                    <code>上门家政的图，350*285</code>

                    </td>

                </tr>

                <tr>

                    <td class="lfTdBt">活动缩略图：</td>

                    <td class="rgTdBt">

                        <input type="text" name="data[activity][thumb]" value="<?php echo ($CONFIG["attachs"]["activity"]["thumb"]); ?>" class="scAddTextName" /> 

                    </td>

                </tr>

                <tr>

                    <td class="lfTdBt">优惠券图片：</td>

                    <td class="rgTdBt"><input type="text" name="data[coupon][thumb]" value="<?php echo ($CONFIG["attachs"]["coupon"]["thumb"]); ?>" class="scAddTextName" /> </td>

                </tr>

                <tr>

                    <td class="lfTdBt">云购商品图片：</td>

                    <td class="rgTdBt"><input type="text" name="data[cloud][thumb]" value="<?php echo ($CONFIG["attachs"]["cloud"]["thumb"]); ?>" class="scAddTextName" /> </td>

                </tr>

                <tr>

                    <td class="lfTdBt" style="padding-right: 0px;">约会缩略图：</td>

                    <td class="rgTdBt"><input type="text" name="data[huodong][thumb]" value="<?php echo ($CONFIG["attachs"]["huodong"]["thumb"]); ?>" class="scAddTextName" /> </td>

                </tr>

                <tr>

                    <td class="lfTdBt">榜单缩略图：</td>

                    <td class="rgTdBt"><input type="text" name="data[billboard][thumb]" value="<?php echo ($CONFIG["attachs"]["billboard"]["thumb"]); ?>" class="scAddTextName"/></td>

                </tr>

                <tr>

                    <td class="lfTdBt">榜单分类图标：</td>

                    <td class="rgTdBt"><input type="text" name="data[billcate][thumb]" value="<?php echo ($CONFIG["attachs"]["billcate"]["thumb"]); ?>" class="scAddTextName"/></td>

                </tr>

                <tr>

                    <td class="lfTdBt">文章缩略图：</td>

                    <td class="rgTdBt">

                        <input type="text" name="data[article][thumb]" value="<?php echo ($CONFIG["attachs"]["article"]["thumb"]); ?>" class="scAddTextName" /> 

                    </td>

                </tr>

                <tr>

                    <td class="lfTdBt">积分商品：</td>

                    <td class="rgTdBt"><input type="text" name="data[integralgoods][thumb]" value="<?php echo ($CONFIG["attachs"]["integralgoods"]["thumb"]); ?>" class="scAddTextName"/></td>

                </tr>

                <tr>

                    <td class="lfTdBt">点餐菜品：</td>

                    <td class="rgTdBt"><input type="text" name="data[dian][thumb]" value="<?php echo ($CONFIG["attachs"]["dian"]["thumb"]); ?>" class="scAddTextName" /> </td>

                </tr>

                <tr>

                    <td class="lfTdBt">订座包厢：</td>

                    <td class="rgTdBt"><input type="text" name="data[dingroom][thumb]" value="<?php echo ($CONFIG["attachs"]["dingroom"]["thumb"]); ?>" class="scAddTextName" /> </td>

                </tr>

                

                <tr>
                    <td class="lfTdBt">酒店：</td>
                    <td class="rgTdBt">
                        <input type="text" name="data[hotel][thumb]" value="<?php echo ($CONFIG["attachs"]["hotel"]["thumb"]); ?>" class="scAddTextName" /> 
                    </td>
                </tr>
                <tr>
                    <td class="lfTdBt">部落：</td>
                    <td class="rgTdBt">
                        <input type="text" name="data[tribe][thumb]" value="<?php echo ($CONFIG["attachs"]["tribe"]["thumb"]); ?>" class="scAddTextName" /> 
                    </td>
                </tr>
                <tr>
                    <td class="lfTdBt">部落banner：</td>
                    <td class="rgTdBt">
                        <input type="text" name="data[tribebanner][thumb]" value="<?php echo ($CONFIG["attachs"]["tribebanner"]["thumb"]); ?>" class="scAddTextName" /> 
                    </td>
                </tr>
                <tr>
                    <td class="lfTdBt">部落详情：</td>
                    <td class="rgTdBt">
                        <input type="text" name="data[tribepost][thumb]" value="<?php echo ($CONFIG["attachs"]["tribepost"]["thumb"]); ?>" class="scAddTextName" /> 
                    </td>
                </tr>
                <tr>
                    <td class="lfTdBt">部落话题回复：</td>
                    <td class="rgTdBt">
                        <input type="text" name="data[tribecomments][thumb]" value="<?php echo ($CONFIG["attachs"]["tribecomments"]["thumb"]); ?>" class="scAddTextName" /> 
                    </td>
                </tr>

                <tr>

                    <td class="lfTdBt">商家点评：</td>

                    <td class="rgTdBt">

                        <input type="text" name="data[shopdianping][thumb]" value="<?php echo ($CONFIG["attachs"]["shopdianping"]["thumb"]); ?>" class="scAddTextName" /> 

                    </td>

                </tr>

                <tr>

                    <td class="lfTdBt">手机发现：</td>

                    <td class="rgTdBt"><input type="text" name="data[found][thumb]" value="<?php echo ($CONFIG["attachs"]["found"]["thumb"]); ?>" class="scAddTextName" /> </td>

                </tr>

                <tr>

                    <td class="lfTdBt">用户头像：</td>

                    <td class="rgTdBt">

                        <code>大图</code><input type="text" name="data[user][thumb][thumb]" value="<?php echo ($CONFIG["attachs"]["user"]["thumb"]["thumb"]); ?>" class="scAddTextName w150" /> 

                        <code>中图</code><input type="text" name="data[user][thumb][middle]" value="<?php echo ($CONFIG["attachs"]["user"]["thumb"]["middle"]); ?>" class="scAddTextName w150" /> 

                        <code>小图</code><input type="text" name="data[user][thumb][small]" value="<?php echo ($CONFIG["attachs"]["user"]["thumb"]["small"]); ?>" class="scAddTextName w150" /> 

                    </td>

                </tr>

                <tr>

                    <td class="lfTdBt">店铺环境图：</td>

                    <td class="rgTdBt">

                        <input type="text" name="data[shopphoto][thumb]" value="<?php echo ($CONFIG["attachs"]["shopphoto"]["thumb"]); ?>" class="scAddTextName" /> 

                    </td>

                </tr>

                <tr>

                    <td class="lfTdBt">推荐内容：</td>

                    <td class="rgTdBt"><input type="text" name="data[recommend][thumb]" value="<?php echo ($CONFIG["attachs"]["recommend"]["thumb"]); ?>" class="scAddTextName" /></td>

                </tr>

                <tr>

                    <td class="lfTdBt">优惠券：</td>

                    <td class="rgTdBt">

                        <input type="text" name="data[coupon][thumb]" value="<?php echo ($CONFIG["attachs"]["coupon"]["thumb"]); ?>" class="scAddTextName" />

                    </td>

                </tr>

                <tr>

                    <td class="lfTdBt">抢购：</td>

                    <td class="rgTdBt">

                        <input type="text" name="data[tuan][thumb]" value="<?php echo ($CONFIG["attachs"]["tuan"]["thumb"]); ?>" class="scAddTextName" /> 

                    </td>

                </tr>

                <tr>

                    <td class="lfTdBt" style="padding-right: 0px;">生活服务图标：</td>

                    <td class="rgTdBt">

                        <input type="text" name="data[lifeservice][icon]" value="<?php echo ($CONFIG["attachs"]["lifeservice"]["icon"]); ?>" class="scAddTextName" />

                    </td>

                </tr>

                <tr>

                    <td class="lfTdBt" style="padding-right: 0px;">生活信息列表图：</td>

                    <td class="rgTdBt">

                        <input type="text" name="data[life][thumb]" value="<?php echo ($CONFIG["attachs"]["life"]["thumb"]); ?>" class="scAddTextName" />

                    </td>

                </tr>

                <tr>

                    <td class="lfTdBt" style="padding-right: 0px;">生活信息详情图：</td>

                    <td class="rgTdBt">

                        <input type="text" name="data[lifepic][thumb]" value="<?php echo ($CONFIG["attachs"]["lifepic"]["thumb"]); ?>" class="scAddTextName" /> 

                    </td>

                </tr>

                <tr>

                    <td class="lfTdBt">投票选项图：</td>

                    <td class="rgTdBt">

                        <input type="text" name="data[votepic][thumb]" value="<?php echo ($CONFIG["attachs"]["votepic"]["thumb"]); ?>" class="scAddTextName" /> 

                    </td>

                </tr>

                <tr>

                    <td class="lfTdBt">微信回复：</td>

                    <td class="rgTdBt">

                        <input type="text" name="data[weixin][thumb]" value="<?php echo ($CONFIG["attachs"]["weixin"]["thumb"]); ?>" class="scAddTextName" /> 

                    </td>

                </tr>

                <tr>

                    <td class="lfTdBt">投票banner：</td>

                    <td class="rgTdBt">

                        <input type="text" name="data[votebanner][thumb]" value="<?php echo ($CONFIG["attachs"]["votebanner"]["thumb"]); ?>" class="scAddTextName" /> 

                    </td>

                </tr>





                <tr>

                    <td class="lfTdBt">商品：</td>

                    <td class="rgTdBt">

                        <input type="text" name="data[goods][thumb]" value="<?php echo ($CONFIG["attachs"]["goods"]["thumb"]); ?>" class="scAddTextName" />  

                        <code>用于商品详情页以及wap商品详情页，列表页</code>

                    </td>

                </tr>

                <tr>

                    <td class="lfTdBt" style="padding-right: 0px;">手机店铺轮播图：</td>

                    <td class="rgTdBt">

                        <input type="text" name="data[shopbanner][thumb]" value="<?php echo ($CONFIG["attachs"]["shopbanner"]["thumb"]); ?>" class="scAddTextName" />

                    </td>

                </tr>

                <tr>

                    <td class="lfTdBt">PC店铺BANNER：</td>

                    <td class="rgTdBt">

                        <input type="text" name="data[shopbanner1][thumb]" value="<?php echo ($CONFIG["attachs"]["shopbanner1"]["thumb"]); ?>" class="scAddTextName" />

                    </td>

                </tr><tr>

                    <td class="lfTdBt">菜单缩略图（外卖）：</td>

                    <td class="rgTdBt">

                        <input type="text" name="data[eleproduct][thumb]" value="<?php echo ($CONFIG["attachs"]["eleproduct"]["thumb"]); ?>" class="scAddTextName" /> 

                    </td>

                </tr><tr>

                    <td class="lfTdBt">菜品缩略图（订座）：</td>

                    <td class="rgTdBt">

                        <input type="text" name="data[dingmenu][thumb]" value="<?php echo ($CONFIG["attachs"]["dingmenu"]["thumb"]); ?>" class="scAddTextName" /> 

                    </td>

                </tr>

                <tr>

                    <td class="lfTdBt" style="padding-right: 0px;">手机商城首页广告图：</td>

                    <td class="rgTdBt">

                        <input type="text" name="data[mall][thumb]" value="<?php echo ($CONFIG["attachs"]["mall"]["thumb"]); ?>" class="scAddTextName" /> 

                    </td>

                </tr>

                

                 <tr>

                    <td class="lfTdBt" style="padding-right: 0px;">wap微店缩略图：</td>

                    <td class="rgTdBt">

                        <input type="text" name="data[weidianpic][thumb]" value="<?php echo ($CONFIG["attachs"]["weidianpic"]["thumb"]); ?>" class="scAddTextName" /> 

                        <code>wap微店！</code>

                    </td>

                </tr>

                

                <tr>

                    <td class="lfTdBt" style="padding-right: 0px;">商家认证：</td>

                    <td class="rgTdBt">

                        <input type="text" name="data[shop_audit][thumb]" value="<?php echo ($CONFIG["attachs"]["shop_audit"]["thumb"]); ?>" class="scAddTextName" /> 

                        <code>商户中心认证营业执照上传的图片，建议1000*800</code>

                    </td>

                </tr>

                

                 <tr>

                    <td class="lfTdBt" style="padding-right: 0px;">社区村镇logo：</td>

                    <td class="rgTdBt">

                        <input type="text" name="data[village][thumb]" value="<?php echo ($CONFIG["attachs"]["village"]["thumb"]); ?>" class="scAddTextName" /> 

                        <code>用于手机版上展示，建议：640*320</code>

                    </td>

                </tr>

                

                <tr>

                    <td class="lfTdBt" style="padding-right: 0px;">社区村镇工作人员头像：</td>

                    <td class="rgTdBt">

                        <input type="text" name="data[village_worker][thumb]" value="<?php echo ($CONFIG["attachs"]["village_worker"]["thumb"]); ?>" class="scAddTextName" /> 

                        <code>用于手机版上展示，建议：200*200</code>

                    </td>

                </tr>

                

                

                  <tr>

                    <td class="lfTdBt" style="padding-right: 0px;">小区缩略图：</td>

                    <td class="rgTdBt">

                        <input type="text" name="data[xiaoqu][thumb]" value="<?php echo ($CONFIG["attachs"]["xiaoqu"]["thumb"]); ?>" class="scAddTextName" /> 

                        <code>小区缩略图，建议：640*320</code>

                    </td>

                </tr>

                

                <tr>

                    <td class="lfTdBt">编辑器大图：</td>

                    <td class="rgTdBt">

                        <input type="text" name="data[editor][thumb]" value="<?php echo ($CONFIG["attachs"]["editor"]["thumb"]); ?>" class="scAddTextName" /> 

                        <label>

                            <input type="checkbox" name="data[editor][water]" value="1"  <?php if(($CONFIG["attachs"]["editor"]["water"]) == "1"): ?>checked="checked"<?php endif; ?> />启用水印

                        </label>

                        <code>水印只能作用于大于某个条件的图，一般水印只会加在最大的图上面</code> 

                    </td>

                </tr>

            </table>

        </div>

        <div class="smtQr"><input type="submit" value="确认保存" class="smtQrIpt" /></div>

    </div>

</form>


     
        
</div>
</body>
</html>