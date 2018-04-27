<?php if (!defined('THINK_PATH')) exit();?><div class="mainScAdd ">
    <div class="tableBox">
        <form target="baocms_frm" action="<?php echo U('label/edit',array('cate_id'=>$detail['cate_id']));?>" method="post">
            <table bordercolor="#dbdbdb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;" >
                <tr>
                    <td class="lfTdBt">分类：</td>
                    <td class="rgTdBt"><input type="text" name="data[label_name]" value="<?php echo (($detail["label_name"])?($detail["label_name"]):''); ?>" class="manageInput" />
                        <input type="hidden" name="cate_id" value="<?php echo ($detail["id"]); ?>" >
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="smtQr"><input type="submit" value="确认保存" class="smtQrIpt" /></div>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>