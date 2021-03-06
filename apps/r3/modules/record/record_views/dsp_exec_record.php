<?php
/**
  	* o	Chương trình: Xây dựng phần mềm một cửa điện tử nguồn mở cho các quận huyện.
	* o	Thực hiện: Ban Quản lý các dự án công nghiệp công nghệ thông tin-Bộ Thông tin và Truyền thông.
	* o	Thuộc dự án: Hỗ trợ địa phương xây dựng, hoàn thiện một số sản phẩm phần mềm nguồn mở theo Quyết định 112/QĐ-TTg ngày 20/01/2012 của Thủ tướng Chính phủ.
	* o	Tác giả: Công ty Cổ phần Đầu tư và Phát triển Công nghệ Tâm Việt
	* o	Email: LTBinh@gmail.com
	* o	Điện thoại: 0936.114411
	* 
*/

if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

count($VIEW_DATA['arr_all_record']) > 0 OR DIE();

$arr_all_record         = $VIEW_DATA['arr_all_record'];
$arr_single_task_info   = $VIEW_DATA['arr_single_task_info'];
$v_record_id_list       = $VIEW_DATA['record_id_list'];

$v_record_type_code = $arr_single_task_info['C_RECORD_TYPE_CODE'];
$v_record_type_name = $arr_single_task_info['C_RECORD_TYPE_NAME'];
$v_group_name       = $arr_single_task_info['C_GROUP_NAME'];

//Step name ??
$v_task_code = $arr_all_record[0]['C_NEXT_TASK_CODE'];
$role = get_role($v_task_code);
if ($role == _CONST_YEU_CAU_THU_LY_LAI_ROLE) {
    $v_task_code = str_replace(_CONST_XML_RTT_DELIM._CONST_YEU_CAU_THU_LY_LAI_ROLE, _CONST_XML_RTT_DELIM._CONST_THU_LY_ROLE, $v_task_code);
}
$dom_workflow = simplexml_load_file($this->get_xml_config($v_record_type_code, 'workflow'));
$v_step_name = $dom_workflow->xpath("//step[task[@code='$v_task_code']]/@name");
$v_step_name = $v_step_name[0];

//display header
$this->template->title = 'Hoàn thành thụ lý hồ sơ';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');

//Tính phí, lệ phí theo mặc định
$v_xml_workflow = $this->get_xml_config($v_record_type_code, 'workflow');
$dom_flow = simplexml_load_file($v_xml_workflow);
$r = $dom_flow->xpath("//process/@fee");
$v_default_fee = isset($r[0]) ? $r[0] : 0;
if ($v_default_fee != 0)
{
    $v_default_fee_description = 'Theo quy định hiện hành';
}
else
{
    $v_default_fee_description = 'Thủ tục không thu phí';
}
?>
<form name="frmMain" method="post" id="frmMain" action="<?php echo $this->get_controller_url();?>do_exec_record">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_record');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_record');
    echo $this->hidden('hdn_update_method', 'update_record');
    echo $this->hidden('hdn_delete_method', 'delete_record');

    echo $this->hidden('pop_win', $v_pop_win);
    echo $this->hidden('hdn_item_id_list', $v_record_id_list);

    //Ma Loai HS
    echo $this->hidden('hdn_record_type_code', $v_record_type_code);

    //KQ thu ly
    echo $this->hidden('hdn_exec_value', _CONST_RECORD_APPROVAL_ACCEPT);

    ?>
    <div class="page-title"><?php echo $v_step_name;?></div>

    <div class="panel_color_form">Danh sách hồ sơ thụ lý</div>
    <div class="Row">
        <div class="left-Col">
            <label for="Loại hồ sơ: ">Loại hồ sơ: </label>
        </div>
        <div class="right-Col">
            <?php echo $v_record_type_code;?> - <?php echo $v_record_type_name;?>
        </div>
    </div>

    <!-- Record list -->
    <table cellpadding="4" cellspacing="0" width="100%" class="list">
        <tr>
            <th>STT</th>
            <th>Mã hồ sơ</th>
            <th>Người đăng ký</th>
            <th>Ngày nhận</th>
            <th>Hẹn trả công dân</th>
        </tr>
        <?php for ($i=0; $i<count($arr_all_record); $i++): ?>
            <tr>
                <td class="right"><?php echo ($i+1);?></td>
                <td><?php echo $arr_all_record[$i]['C_RECORD_NO'];?></td>
                <td><?php echo $arr_all_record[$i]['C_CITIZEN_NAME'];?></td>
                <td><?php echo jwDate::yyyymmdd_to_ddmmyyyy($arr_all_record[$i]['C_RECEIVE_DATE'], TRUE);?></td>
                <td><?php echo r3_View::return_date_by_text($arr_all_record[$i]['C_RETURN_DATE']);?></td>
            </tr>
        <?php endfor;?>
    </table>
    <!-- End: Record list -->
    
    <div class="panel_color_form">Thụ lý hồ sơ</div>
    <div class="Row">
        <div class="left-Col">
            Kết quả:
        </div>
        <div class="right-Col">
            <input type="radio" name="rad_exec" id="rad_<?php echo _CONST_RECORD_APPROVAL_ACCEPT;?>"
                   value="<?php echo _CONST_RECORD_APPROVAL_ACCEPT;?>"
                   onclick="rad_exec_onclick(this.value)" checked
            />
            <label for="rad_<?php echo _CONST_RECORD_APPROVAL_ACCEPT;?>">Đề nghị phê duyệt hồ sơ</label>

            <input type="radio" name="rad_exec" id="rad_<?php echo _CONST_RECORD_APPROVAL_SUPPLEMENT;?>"
                   value="<?php echo _CONST_RECORD_APPROVAL_SUPPLEMENT;?>"
                    onclick="rad_exec_onclick(this.value)"
            />
            <label for="rad_<?php echo _CONST_RECORD_APPROVAL_SUPPLEMENT;?>">Đề nghị bổ sung hồ sơ</label>

            <input type="radio" name="rad_exec" id="rad_<?php echo _CONST_RECORD_APPROVAL_REJECT;?>"
                   value="<?php echo _CONST_RECORD_APPROVAL_REJECT;?>"
                   onclick="rad_exec_onclick(this.value)"
            />
            <label for="rad_<?php echo _CONST_RECORD_APPROVAL_REJECT;?>">Đề nghị từ chối hồ sơ</label>
        </div>
    </div>
    <div id="divFee" class="Row">
        <div class="Row">
            <div class="left-Col">
                Lệ phí:<span class="required">*</span>
            </div>
            <div class="right-Col">
                <input type="text" name="txt_fee" id="txt_fee"
                       size="8" maxlength="10" class="text ui-widget-content ui-corner-all"
                       value="<?php echo $v_default_fee;?>"/> (đ)
            </div>
        </div>
        <div class="Row">
            <div class="left-Col">
                <span id="spanLyDo">Diễn giải:</span> <span class="required">*</span>
            </div>
            <div class="right-Col">
                <textarea style="width:100%;height:100px;" rows="2"
                    name="txt_fee_description" id="txt_fee_description" cols="20" maxlength="400"
                    class="text ui-widget-content ui-corner-all"><?php echo $v_default_fee_description;?></textarea>
            </div>
        </div>
    </div>
    <div id="divNote" class="Row" style="display: none;">
        <div class="left-Col">
            <span id="spanLyDo">Lý do:</span> <span class="required">*</span>
        </div>
        <div class="right-Col">
            <textarea style="width:100%;height:100px;" rows="2"
            	name="txt_reason" id="txt_reason" cols="20" maxlength="400"
            	class="text ui-widget-content ui-corner-all"></textarea>
        </div>
    </div>

    <div class="clear">&nbsp;</div>
    <!-- Buttons -->
    <div class="button-area">
        <hr/>
        <input type="button" name="btn_do_exec" class="button save" value="Cập nhật" onclick="btn_do_exec_onclick();" />
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
        <input type="button" name="cancel" class="button close" value="<?php echo __('close window'); ?>" onclick="<?php echo $v_back_action;?>"/>
    </div>
</form>
<script>
    function rad_exec_onclick(exec_value)
    {
        $("#hdn_exec_value").val(exec_value);
        if (exec_value == '<?php echo _CONST_RECORD_APPROVAL_ACCEPT;?>')
        {
            $("#divNote").css('display', 'none');
            $("#divFee").css('display', 'table');
        }
        else
        {
            $("#divNote").css('display', 'table');
            $("#divFee").css('display', 'none');
            document.frmMain.txt_reason.focus();
        }
    }

    function btn_do_exec_onclick()
    {
        var f = document.frmMain;
        var v_exec_value = $("#hdn_exec_value").val();
        var v_note = trim($("#txt_reason").val());

        if (v_exec_value != '<?php echo _CONST_RECORD_APPROVAL_ACCEPT;?>' && v_note == '')
        {
            alert('Lý do không được bỏ trống!');
            f.txt_reason.focus();
            return false;
        }

        f.submit();
    }
</script>
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');