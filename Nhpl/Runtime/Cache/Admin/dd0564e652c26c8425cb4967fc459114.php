<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">

    <head>

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <!-- title><?php echo ($CONFIG["site"]["title"]); ?>管理后台</title> -->
        <title>你好漂亮管理后台</title>
        <meta name="description" content="你好漂亮管理后台" />

        <meta name="keywords" content="你好漂亮管理后台" />

    
        <link href="__TMPL__statics/css/new_index.css" rel="stylesheet" type="text/css" />
        <script> var BAO_PUBLIC = '__PUBLIC__';

            var BAO_ROOT = '__ROOT__';</script>

        <script src="__PUBLIC__/js/jquery.js"></script>

        <script src="__PUBLIC__/js/my97/WdatePicker.js"></script>

        <script src="__PUBLIC__/js/admin.js"></script>



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





    <style>

	/*body{background-color: #eee;}*/

	</style>

    <body>
    <iframe id="baocms_frm" name="baocms_frm" style="display:none;"></iframe>
    <form action="<?php echo U('login/loging');?>" method="post" target="baocms_frm">
    <div class="login">
        <div class="login_box">
            <h2><img src="__PUBLIC__/images/login.png" alt="E-SHOP"></h2>
            <div class="usname">
                <span><img src="__PUBLIC__/images/ico_01.png" align="usname"></span>
                <input type="text" placeholder="请输入用户名"  name="username" class="loginInput1"/>
            </div>
            <div class="usname">
                <span><img src="__PUBLIC__/images/ico_02.png" alt="pwd"></span>
                <input type="password" placeholder="请输入密码"  name="password" class="loginPass"/>
            </div>
            <div class="yz">
                <input type="text" required="required" name="yzm" class="yzm" placeholder="请输入验证码"/>
                <span class="yzm_code" style="cursor:pointer;"><img style="width:100px;height:50px;" src="__ROOT__/index.php?g=app&m=verify&a=index&mt=<?php echo time();?>" /></span></td>
            </div>
            <div class="logins">
                <input type="submit" class="loginBtn" value="确认登录" />
            </div>
        </div>
        <div class="bg_color">
            <ul>
                <li><img src="__PUBLIC__/images/login_01.jpg" alt="背景图1"></li>
                <li><img src="__PUBLIC__/images/login_02.jpg" alt="背景图2"></li>
                <li><img src="__PUBLIC__/images/login_03.jpg" alt="背景图3"></li>
            </ul>
        </div>
    </div>
    </form>  	   	
<script>
    $(function(){
        var i = 0
        var size = $('.bg_color li').size();
        function fadeIn(){
            i++;
            if(i==size)
            {
                i=0
            }
            $('.bg_color li').eq(i).fadeIn(1000).siblings().fadeOut(1000);
        }
        $('.bg_color li').eq(0).show().siblings().hide();
        
        setInterval(fadeIn,5000)
    })
</script>
</body>
</html>