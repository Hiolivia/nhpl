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
        <li class="li2 li3">站点设置</li>
    </ul>
</div>
<p class="attention"><span>注意：</span>这个设置和全局有关系，请不要乱填写</p>

<form  target="baocms_frm" action="<?php echo U('setting/site');?>" method="post">

    <div class="mainScAdd">
        <div class="tableBox">
            <table  bordercolor="#dbdbdb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;" >
                <tr>
                    <td class="lfTdBt">站点名称：</td>
                    <td class="rgTdBt"><input type="text" name="data[sitename]" value="<?php echo ($CONFIG["site"]["sitename"]); ?>" class="scAddTextName " />
                        <code>注意这个不是网站的Title，一般建议是网站的品牌名，如果您使用大于的短信，这里的站点名称必须跟大于的签名一致，否则无法接受短信！</code>
                    </td>
                </tr>

                <tr>
                    <td class="lfTdBt">站点网址：</td>
                    <td class="rgTdBt"><input type="text" name="data[host]" value="<?php echo ($CONFIG["site"]["host"]); ?>" class="scAddTextName " />
                        <code>例如：http://www.baocms.cn 如果你在二级目录下面就需要带上二级目录</code></td>
                </tr>
                <tr>
                    <td class="lfTdBt">站点根域名：</td>
                    <td class="rgTdBt"><input type="text" name="data[hostdo]" value="<?php echo ($CONFIG["site"]["hostdo"]); ?>" class="scAddTextName " />
                        <code>例如：baocms.cn 用于分站二级域名，如需要分站支持二级域名联系KOUKOU：1,2,0,5,8,5,0,2,2</code></td>
                </tr>
                
                <tr>
                    <td class="lfTdBt">android下载地址：</td>
                    <td class="rgTdBt"><input type="text" name="data[android]" value="<?php echo ($CONFIG["site"]["android"]); ?>" class="scAddTextName w360 " />
                        <code>android下载地址</code></td>
                </tr>
                <tr>
                    <td class="lfTdBt">IOS下载地址：</td>
                    <td class="rgTdBt"><input type="text" name="data[ios]" value="<?php echo ($CONFIG["site"]["ios"]); ?>" class="scAddTextName  w360" />
                        <code>IOS下载地址</code></td>
                </tr>

              <tr>
                    <td class="lfTdBt">LOGO：</td>
                    <td class="rgTdBt"><div style="width: 300px; height: 100px; float: left;">
                            <input type="hidden" name="data[logo]" value="<?php echo ($CONFIG["site"]["logo"]); ?>" id="data_photo" />
                            <input id="photo_file" name="photo_file" type="file" multiple="true" value="" />
                        </div>
                        <div style="width: 300px; height: 100px; float: left;">
                            <img id="photo_img" width="200" height="80"  src="__ROOT__/attachs/<?php echo (($CONFIG["site"]["logo"])?($CONFIG["site"]["logo"]):'default.jpg'); ?>" />
                        </div>
                        <script type="text/javascript" src="__PUBLIC__/js/uploadify/jquery.uploadify.min.js?t=<?php echo ($nowtime); ?>"></script>
                        <link rel="stylesheet" href="__PUBLIC__/js/uploadify/uploadify.css">
                        <script>
                            $("#photo_file").uploadify({
                                'swf': '__PUBLIC__/js/uploadify/uploadify.swf?t=<?php echo ($nowtime); ?>',
                                'uploader': '<?php echo U("app/upload/uploadify",array("model"=>"setting"));?>',
                                'cancelImg': '__PUBLIC__/js/uploadify/uploadify-cancel.png',
                                'buttonText': '上传网站LOGO',
                                'fileTypeExts': '*.gif;*.jpg;*.png',
                                'queueSizeLimit': 1,
                                'onUploadSuccess': function (file, data, response) {
                                    $("#data_photo").val(data);
                                    $("#photo_img").attr('src', '__ROOT__/attachs/' + data).show();
                                }
                            });
                        </script></td>
                </tr>
                
                <tr>
                    <td class="lfTdBt">微信二维码：</td>
                    <td class="rgTdBt">
					<div style="width: 300px; height: 100px; float: left;">
                            <input type="hidden" name="data[wxcode]" value="<?php echo ($CONFIG["site"]["wxcode"]); ?>" id="data_wxcode" />
                            <input id="wxcode_file" name="wxcode_file" type="file" multiple="true" value="" />
                        </div>
                        <div style="width: 300px; height: 100px; float: left;">
                            <img id="wxcode_img" width="100" height="100"  src="__ROOT__/attachs/<?php echo (($CONFIG["site"]["wxcode"])?($CONFIG["site"]["wxcode"]):'default.jpg'); ?>" />
                        </div>
																		        
                        <script>
                            $("#wxcode_file").uploadify({
                                'swf': '__PUBLIC__/js/uploadify/uploadify.swf?t=<?php echo ($nowtime); ?>',
                                'uploader': '<?php echo U("app/upload/uploadify",array("model"=>"setting"));?>',
                                'cancelImg': '__PUBLIC__/js/uploadify/uploadify-cancel.png',
                                'buttonText': '上传微信二维码',
                                'fileTypeExts': '*.gif;*.jpg;*.png',
                                'queueSizeLimit': 1,
                                'onUploadSuccess': function (file, data, response) {
                                    $("#data_wxcode").val(data);
                                    $("#wxcode_img").attr('src', '__ROOT__/attachs/' + data).show();
                                }
                            });

                        </script></td>
                </tr>
                
                <tr>
                    <td class="lfTdBt">客服QQ：</td>
                    <td class="rgTdBt">
                    <input type="text" name="data[qq]" value="<?php echo ($CONFIG["site"]["qq"]); ?>" class="scAddTextName " />
                    <code>模板中QQ客服</code>
                    <input type="text" name="data[upgrade_url]" value="<?php echo ($CONFIG["site"]["upgrade_url"]); ?>" class="scAddTextName w360" />
                    <code>模板顶部升级URL</code>
                    </td>
                </tr>
                <tr>
                    <td class="lfTdBt">电话：</td>
                    <td class="rgTdBt"><input type="text" name="data[tel]" value="<?php echo ($CONFIG["site"]["tel"]); ?>" class="scAddTextName " />
                    <code>这里前台显示用</code>
                    </td>
                </tr>
                <tr>
                    <td class="lfTdBt">邮件：</td>
                    <td class="rgTdBt"><input type="text" name="data[email]" value="<?php echo ($CONFIG["site"]["email"]); ?>" class="scAddTextName " />
                    <code>填写管理员的邮箱，前台模板显示调用！</code>
                    </td>
                </tr>

                <tr>
                    <td class="lfTdBt">ICP备案：</td>
                    <td class="rgTdBt"><input type="text" name="data[icp]" value="<?php echo ($CONFIG["site"]["icp"]); ?>" class="scAddTextName " />
                    <code>前台模板显示调用！</code>
                    </td>
                </tr>
                <tr>
                    <td class="lfTdBt">站点标题：</td>
                    <td class="rgTdBt"><input type="text" name="data[title]" value="<?php echo ($CONFIG["site"]["title"]); ?>" class="scAddTextName " />
                    <code>seo设置中调用</code>
                    </td>
                </tr>
                <tr>
                    <td class="lfTdBt">站点关键字：</td>
                    <td class="rgTdBt"><textarea name="data[keyword]" cols="80" rows="2"><?php echo ($CONFIG["site"]["keyword"]); ?></textarea>
                    <code>seo设置中调用，建议认真填写！</code>
                    </td>
                </tr>
                <tr>
                    <td class="lfTdBt">站点描述：</td>
                    <td class="rgTdBt"><textarea name="data[description]" cols="80" rows="2"><?php echo ($CONFIG["site"]["description"]); ?></textarea>
                    <code>seo设置中调用</code>
                    </td>

                </tr>

               
                <tr>
                    <td class="lfTdBt">统计代码：</td>
                    <td class="rgTdBt"><textarea name="data[tongji]" cols="80" rows="2"><?php echo ($CONFIG["site"]["tongji"]); ?></textarea>
                    <code>模板中调用，有统计代码的填写在这里。</code>
                    </td>
                </tr>

                <tr>
                    <td class="lfTdBt">默认城市：</td>
                    <td class="rgTdBt">
                        <select name="data[city_id]" class="selectOption">
                            <?php  foreach($citys as $val){?>
                            <option <?php if($val['city_id'] == $CONFIG['site']['city_id']) echo 'selected="selected"' ;?> value="<?php echo ($val["city_id"]); ?>"><?php echo ($val["name"]); ?></option>
                            <?php }?>
                        </select>
                        <code>请填写您的默认城市，如果自动定位失败后就显示当前选择的默认城市。</code>
                    </td>
                </tr>

                <tr>
                    <td class="lfTdBt" style="padding-right: 0px;">城市中心地图坐标：</td>
                    <td class="rgTdBt">
                        经度  <input type="text" name="data[lng]" value="<?php echo ($CONFIG["site"]["lng"]); ?>" class="scAddTextName " />
                        纬度 <input type="text" name="data[lat]" value="<?php echo ($CONFIG["site"]["lat"]); ?>" class="scAddTextName " />
                        <code>关系到全局默认地图位置，请认真填写，建议跟城市列表》》》编辑》》》坐标》》》填写一致</code>
                        </td>
                </tr>
                <tr>
                    <td class="lfTdBt">自动收货时间：</td>
                    <td class="rgTdBt">
                        商城<input type="text" name="data[goods]" value='<?php echo ($CONFIG["site"]["goods"]); ?>' style="width: 50px;"  class="scAddTextName " />（天）
                        外卖<input type="text" name="data[ele]" value='<?php echo ($CONFIG["site"]["ele"]); ?>' style="width: 50px;"  class="scAddTextName " />（小时）
                    </td>
                </tr>

                <tr>
                    <td class="lfTdBt">贴吧发帖免审核：</td>
                    <td class="rgTdBt">
                        <label><input type="radio" name="data[postaudit]" <?php if(($CONFIG["site"]["postaudit"]) == "1"): ?>checked="checked"<?php endif; ?> value="1"  />开启</label>
                        <label><input type="radio" name="data[postaudit]"  <?php if(($CONFIG["site"]["postaudit"]) == "0"): ?>checked="checked"<?php endif; ?>  value="0"  />不开启</label>

                        <code>开启之后帖子发布免审核！</code>
                    </td>

                </tr>

                 <tr>

                    <td class="lfTdBt">贴吧回帖免审核：</td>
                    <td class="rgTdBt">
                        <label><input type="radio" name="data[replyaudit]" <?php if(($CONFIG["site"]["replyaudit"]) == "1"): ?>checked="checked"<?php endif; ?> value="1"  />开启</label>
                        <label><input type="radio" name="data[replyaudit]"  <?php if(($CONFIG["site"]["replyaudit"]) == "0"): ?>checked="checked"<?php endif; ?>  value="0"  />不开启</label>
                        <code>开启之后帖子发布免审核！</code>
                    </td>
                </tr>

                

                 <tr>
                    <td class="lfTdBt">小区发帖免审核：</td>
                    <td class="rgTdBt">
                        <label><input type="radio" name="data[xiaoqu_post_audit]" <?php if(($CONFIG["site"]["xiaoqu_post_audit"]) == "1"): ?>checked="checked"<?php endif; ?> value="1"  />开启</label>

                        <label><input type="radio" name="data[xiaoqu_post_audit]"  <?php if(($CONFIG["site"]["xiaoqu_post_audit"]) == "0"): ?>checked="checked"<?php endif; ?>  value="0"  />不开启</label>

                        <code>开启之后小区帖子发布免审核！</code>
                    </td>
                </tr>

                

                 <tr>
                    <td class="lfTdBt">小区回帖免审核：</td>
                    <td class="rgTdBt">
                        <label><input type="radio" name="data[xiaoqu_reply_audit]" <?php if(($CONFIG["site"]["xiaoqu_reply_audit"]) == "1"): ?>checked="checked"<?php endif; ?> value="1"  />开启</label>

                        <label><input type="radio" name="data[xiaoqu_reply_audit]"  <?php if(($CONFIG["site"]["xiaoqu_reply_audit"]) == "0"): ?>checked="checked"<?php endif; ?>  value="0"  />不开启</label>
                        <code>开启之后小区回帖发布免审核！</code>
                    </td>
                </tr>


                <tr>
                    <td class="lfTdBt">新闻评论免审核：</td>
                    <td class="rgTdBt">
                        <label><input type="radio" name="data[article_reply_audit]" <?php if(($CONFIG["site"]["article_reply_audit"]) == "1"): ?>checked="checked"<?php endif; ?> value="1"  />开启</label>
                        <label><input type="radio" name="data[article_reply_audit]"  <?php if(($CONFIG["site"]["article_reply_audit"]) == "0"): ?>checked="checked"<?php endif; ?>  value="0"  />不开启</label>
                        <code>开启后新闻评论免审核！小心使用！</code>
                    </td>
                </tr>
                
                
                 <tr>
                    <td class="lfTdBt">物业发送通知免审核：</td>
                    <td class="rgTdBt">
                        <label><input type="radio" name="data[xiaoqu_news_audit]" <?php if(($CONFIG["site"]["xiaoqu_news_audit"]) == "1"): ?>checked="checked"<?php endif; ?> value="1"  />开启</label>
                        <label><input type="radio" name="data[xiaoqu_news_audit]"  <?php if(($CONFIG["site"]["xiaoqu_news_audit"]) == "0"): ?>checked="checked"<?php endif; ?>  value="0"  />不开启</label>
                        <code>开启之后物业发送通知免审核，自己看吧，怕就不要开启自动！</code>
                    </td>
                </tr>


          <tr>
            <td class="lfTdBt">部落免审核：</td>
              <td class="rgTdBt">
              <label><input type="radio" name="data[tribeaudit]" <?php if(($CONFIG["site"]["tribeaudit"]) == "1"): ?>checked="checked"<?php endif; ?> value="1" />开启</label>
              <label><input type="radio" name="data[tribeaudit]"  <?php if(($CONFIG["site"]["tribeaudit"]) == "0"): ?>checked="checked"<?php endif; ?> value="0" />不开启</label>
              <code>开启后部落无需审核！小心使用！</code>
             </td>
        </tr>

         <tr>
           <td class="lfTdBt">农家攻略部落ID：</td>
           <td class="rgTdBt">
           <input type="text" name="data[tribe_id]" value='<?php echo ($CONFIG["site"]["tribe_id"]); ?>' class="scAddTextName " />
          </td>
       </tr>
                <tr>
                    <td class="lfTdBt">微信自动绑定：</td>
                    <td class="rgTdBt">
                        <label><input type="radio" name="data[weixin]" <?php if(($CONFIG["site"]["weixin"]) == "1"): ?>checked="checked"<?php endif; ?> value="1"  />开启</label>
                        <label><input type="radio" name="data[weixin]"  <?php if(($CONFIG["site"]["weixin"]) == "0"): ?>checked="checked"<?php endif; ?>  value="0"  />不开启</label>
                        <code>开启后微信访问手机端自动登录，不开启就不自动登录了！</code>
                    </td>
                </tr>
             
                <tr>
                    <td class="lfTdBt">全局通知手机号码</td>
                    <td class="rgTdBt">
                 <input type="text" name="data[config_mobile]" value="<?php echo ($CONFIG["site"]["config_mobile"]); ?>" class="scAddTextName " />
                        <code>填写有有的场景需要通知给管理员的手机号！</code>
                    </td>
                </tr>

                <tr>
                    <td class="lfTdBt">全局通知邮箱</td>
                    <td class="rgTdBt">
                 <input type="text" name="data[config_email]" value="<?php echo ($CONFIG["site"]["config_email"]); ?>" class="scAddTextName " />
                        <code>这里是在必要情况下，给站长发邮箱的时候的接受信箱。</code>
                    </td>
                </tr>

                 <tr>
                    <td class="lfTdBt">是否关闭网站：</td>
                    <td class="rgTdBt">
                 <label><input type="radio" name="data[web_close]" <?php if(($CONFIG["site"]["web_close"]) == "1"): ?>checked="checked"<?php endif; ?> value="1"  />开启</label>
                 <label><input type="radio" name="data[web_close]"  <?php if(($CONFIG["site"]["web_close"]) == "0"): ?>checked="checked"<?php endif; ?>  value="0"  />关闭</label>
                        <code style="color:#F00">关闭之后前台打不开哦，突发情况以及备案的时候可以关闭，其他时候不要去动！关闭后不影响后台跟商家后台！</code>
                    </td>
                </tr>

                 <tr>
                    <td class="lfTdBt">关闭网站原因：</td>
                    <td class="rgTdBt"><textarea name="data[web_close_title]" cols="80" rows="2"><?php echo ($CONFIG["site"]["web_close_title"]); ?></textarea>
                    <code>这里填写关站原因，将会显示到前台首页！</code>
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