<?php if (!defined('THINK_PATH')) exit();?><title><?php echo ($CONFIG["site"]["web_close_title"]); ?></title>



<style>
html{ margin:0px; padding:0px;}
*{word-wrap:break-word;}body,input,button,select,textarea{font:12px/1.5 Microsoft YaHei,Helvetica,'SimSun',sans-serif;color:#444;margin: 0px;background:#FCFCFC;}
	.alert-box{padding:60px 0;margin-bottom:-30px;}
	.alert-box .alert {margin:40px;padding:30px;border:thin solid #DDD;}
	.alert-box .alert strong{font-size:14px;margin-bottom:10px;display:block;}
	.alert-box .alert p{margin:20px 0;}
</style>
<div class="layout alert-box">
	<div class="blank-10"></div>
	<div class="container">
		<div class="alert alert-red radius">
			<strong>友情提示：</strong>
			<p><?php echo ($CONFIG["site"]["web_close_title"]); ?></p>
		</div>
	</div>
	<div class="blank-10"></div>
</div>