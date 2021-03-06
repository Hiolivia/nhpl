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
        <li class="li2 li3">短信设置</li>
    </ul>
</div>
<style>
.profit {text-align: center;color: #333;font-weight: bold; background: #F5F5FB;}
.sogn{ width:120px; margin:5px 0;height: 30px;}
</style>
<p class="attention"><span>注意：</span>万能短信接口，不知道怎么使用可以询问技术售后！&#28304;&#12288;&#30721;&#12288;&#30001;&#12288;&#25240;&#12288;&#32764;&#12288;&#22825;&#12288;&#20351;&#12288;&#36164;&#12288;&#28304;&#12288;&#31038;&#12288;&#21306;&#12288;&#25552;&#12288;&#20379;</p>
<form  target="baocms_frm" action="<?php echo U('setting/sms');?>" method="post">
    <div class="mainScAdd">
        <div class="tableBox">
            <table  bordercolor="#dbdbdb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;" >
                <tr>
                    <td class="lfTdBt">短信接口选择：</td>
                    <td class="rgTdBt">
                        <select name="data[dxapi]"  class="scAddTextName sogn">
                            <option value="dy" <?php if($CONFIG['sms']['dxapi'] == dy): ?>selected='selected'<?php endif; ?>>大于短信</option>
                            <option value="bo" <?php if($CONFIG['sms']['dxapi'] == bo): ?>selected='selected'<?php endif; ?>>万能短信接口</option>
                        </select>
                        <code>接口选择，选择后保存，清理缓存方可生效</code></td>
                </tr>
              
                <tr class="jq_type_bo">
                    <td class="lfTdBt">万能短信接口URL：</td>
                    <td class="rgTdBt">
                        <input type="text" name="data[url]" value="<?php echo ($CONFIG["sms"]["url"]); ?>" style="width: 700px;" class="scAddTextName " />
                        <code>填写短信服务商的HTTP请求接口，需要将发送给的人的参数替换成{mobile}，内容替换成{content}，记住前面不能留空格！</code>
                    </td>
                </tr>

                <tr class="jq_type_bo">
                    <td class="lfTdBt">内容编码：</td>
                    <td class="rgTdBt">
                        <label><input type="radio" name="data[charset]" <?php if(($CONFIG["sms"]["charset"]) == "1"): ?>checked="checked"<?php endif; ?> value="1"  />GBK</label>
                        <label><input type="radio" name="data[charset]"  <?php if(($CONFIG["sms"]["charset"]) == "0"): ?>checked="checked"<?php endif; ?>  value="0"  />UTF-8</label>
                        <code>如果短信那边编码是GBK 就需要选择GBK！</code>
                    </td>
                </tr>
                <tr class="jq_type_dy">
                 <td class="lfTdBt">短信宝信息：</td>
                 <td class="rgTdBt">短信宝账户： <input type="text" name="data[sms_bao_account]" value="<?php echo ($CONFIG["sms"]["sms_bao_account"]); ?>" class="scAddTextName sogn" />
                 &nbsp;&nbsp;短信宝密码：  <input type="text" name="data[sms_bao_password]" value="<?php echo ($CONFIG["sms"]["sms_bao_password"]); ?>" class="scAddTextName sogn" />
                 <code>这里写短信宝的账户密码，记得不要有空格哦！填写这里的原因是可以查询短信宝账户的余额，非短信宝用户不要填写，记住这里跟上面的万能短信接口没有关联！</code></td>
                </tr>
                <tr class="jq_type_bo">
                    <td class="lfTdBt">成功状态值：</td>
                    <td class="rgTdBt">
                        <input type="text" name="data[code]" value="<?php echo ($CONFIG["sms"]["code"]); ?>"  class="scAddTextName sogn" />
                        <code>填写对方HTTP接口请求的正确返回值，<a style="color:#F00; font-weight:bold; font-size:18px;">当前短信宝剩余短信数量：<?php echo ($number); ?></a></code>
                    </td>
                </tr>
                <tr>
                	<td class="rgTdBt profit" colspan="2"> 下面是大于短信配置，注意，签名要跟站点名称一致</td>
                </tr>
                <tr class="jq_type_bo">
                    <td class="lfTdBt">大于短信签名：</td>
                    <td class="rgTdBt">
                        <input type="text" name="data[sign]" value="<?php echo ($CONFIG["sms"]["sign"]); ?>"  class="scAddTextName " />
                        <code>填写您在大鱼官方申请的短信签名，建议跟站点名称一致</code>
                    </td>
                </tr>
                <tr class="jq_type_dy">
                    <td class="lfTdBt">大于短信：</td>
                    <td class="rgTdBt">Key: <input type="text" name="data[dykey]" value="<?php echo ($CONFIG["sms"]["dykey"]); ?>" class="scAddTextName " />
                    &nbsp;&nbsp;Secret: <input type="text" name="data[dysecret]" value="<?php echo ($CONFIG["sms"]["dysecret"]); ?>" class="scAddTextName " />
                    <code>大于短信配置</code></td>
                </tr>
            </table>
        </div>
        <div class="smtQr"><input type="submit" value="确认保存" class="smtQrIpt" /></div>
    </div>
</form>

     
        
</div>
</body>
</html>