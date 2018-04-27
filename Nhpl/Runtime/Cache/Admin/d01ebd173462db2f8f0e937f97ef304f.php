<?php if (!defined('THINK_PATH')) exit();?><form  target="baocms_frm" action="<?php echo U('Label/create',array('parent_id'=>$parent_id));?>" method="post">
    <div class="mainScAdd ">
        <div class="tableBox">
            <table bordercolor="#dbdbdb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;" >
                <tr>
                    <td class="lfTdBt">标签：</td>
                    <td class="rgTdBt"><input type="text" name="label_name" value="" class="manageInput" />
                    </td>
                </tr>

                <!--<tr>-->
                    <!--<td class="lfTdBt">排序：</td>-->
                    <!--<td class="rgTdBt"><input type="text" name="data[orderby]" value="<?php echo (($detail["orderby"])?($detail["orderby"]):''); ?>" class="manageInput" />-->
                        <!--<code>数字越小越高</code>-->
                    <!--</td>-->
                <!--</tr>-->
               
            </table>
        </div>
        <div class="smtQr"><input type="submit" value="确认添加" class="smtQrIpt" /></div>
    </div>  
</form>