<?php if (!defined('THINK_PATH')) exit();?><div class="listBox clfx">
    <div class="menuManage">
        <form  target="baocms_frm" action="<?php echo U('role/create');?>" method="post">
            <div class="mainScAdd">
                <div class="tableBox">
                    <table bordercolor="#e1e6eb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;" >
                        
                        <tr>
                    <td class="lfTdBt">角色名称：</td>
                    <td class="rgTdBt">
                        <input name="data[role_name]" type="text" class="manageInput" />
                    </td>
                </tr>
                    </table>
                </div>
                <div class="smtQr"><input type="submit" value="创建角色" class="smtQrIpt" /></div>
            </div>
        </form>
    </div>
</div>