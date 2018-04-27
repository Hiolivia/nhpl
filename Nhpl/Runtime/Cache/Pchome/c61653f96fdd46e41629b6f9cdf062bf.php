<?php if (!defined('THINK_PATH')) exit();?> <ul>
 <?php if(is_array($cart_goods)): foreach($cart_goods as $key=>$item): ?><li class="topTwo_cart_list">
         <a class="jq_delete del" rel="<?php echo ($item["goods_id"]); ?>"  href="javascript:void(0);">删除</a>
        <div class="pub_img left"><a href="<?php echo U('mall/detail',array('goods_id'=>$item['goods_id']));?>"><img src="__ROOT__/attachs/<?php echo ($item["photo"]); ?>" width="80" height="60" /></a></div>
        <div class="pub_wz">
            <p class="overflow_clear"><a href="<?php echo U('mall/detail',array('goods_id'=>$item['goods_id']));?>"><?php echo ($item["format_title"]); ?> x<?php echo ($item["num"]); ?></a></p>
            <P class="price pointcl">¥<?php echo round($item['mall_price'] * $item['num']/100,2);?></P>
        </div>
        <div class="clear"></div>
    </li><?php endforeach; endif; ?> 
 </ul>
<div class="see_more"><a href="<?php echo U('mall/cart');?>">查看我的购物车</a></div>                 
 <script>       
        $(".jq_delete").click(function () {
            var goods_id = $(this).attr('rel');
                $.post("<?php echo U('mall/cartdel');?>", {goods_id: goods_id}, function (result) {
                    if (result.status == "success") {
                        layer.msg(result.msg);
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    } else {
                        layer.msg(result.msg);
                    }
                }, 'json');
        });
</script>