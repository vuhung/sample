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

count($VIEW_DATA['arr_all_record']) > 0 OR DIE('ohhh');

$arr_all_record         = $VIEW_DATA['arr_all_record'];
$arr_single_task_info   = $VIEW_DATA['arr_single_task_info'];
$v_record_id_list       = $VIEW_DATA['record_id_list'];

$v_record_type_code = $arr_all_record[0]['C_RECORD_TYPE_CODE'];
$v_record_type_name = $arr_all_record[0]['C_RECORD_TYPE_NAME'];

// = $arr_single_task_info[''];
//$v_group_name       = $arr_single_task_info['C_GROUP_NAME'];
$arr_all_next_user  = $VIEW_DATA['arr_all_next_user'];

//display header
$this->template->title = 'Xét duyệt hồ sơ';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');

//Lựa chọn mặc định theo đề xuất của Cán bộ chuyên môn
$v_promote = '';
$v_reason = '';
$v_fee = '';
$v_fee_description = '';

//Tên phòng ban đang thực hiện xét duyệt hồ sơ
$v_approving_group_name = get_xml_value(simplexml_load_string($arr_all_record[0]['C_XML_PROCESSING']), '//next_task/@group_name');

if (count($arr_all_record) == 1)
{
    $v_xml_processing = $arr_all_record[0]['C_XML_PROCESSING'];
    $dom_processing = simplexml_load_string($v_xml_processing);

    $r = $dom_processing->xpath("//next_task/@promote");
    $v_promote = isset($r[0]) ? strtoupper($r[0]) : '';

    $r = $dom_processing->xpath("//next_task/@reason");
    $v_reason = isset($r[0]) ? $r[0] : '';

    if ($v_promote == '')
    {
        $r = $dom_processing->xpath("//next_task/@fee");
        if (!isset($r[0]))
        {
            //Tính phí, lệ phí theo mặc định
            $v_xml_workflow = $this->get_xml_config($v_record_type_code, 'workflow');
            $dom_flow = simplexml_load_file($v_xml_workflow);
            $r = $dom_flow->xpath("//process/@fee");
            $v_fee = isset($r[0]) ? $r[0] : 0;
            $v_fee_description = ($v_fee != 0) ? 'Theo quy định hiện hành' : 'Thủ tục không thu phí';
        }
        else
        {
            $v_fee = strtoupper($r[0]);
            $r = $dom_processing->xpath("//next_task/@fee_description");
            $v_fee_description = isset($r[0]) ? ($r[0]) : '';
        }
    }
}
if ($v_promote == '')
{
    $v_promote = _CONST_RECORD_APPROVAL_ACCEPT;
}

?>
<form name="frmMain" method="post" id="frmMain" action="<?php echo $this->get_controller_url();?>do_approval_record">
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
    echo $this->hidden('hdn_record_type_name', $v_record_type_name);

    //KQ thu ly
    echo $this->hidden('hdn_approval_value', $v_promote);
    echo $this->hidden('hdn_approving_group_name', $v_approving_group_name);
    ?>

    <div class="panel_color_form">Danh sách hồ xét duyệt</div>
    <div class="Row">
        <div class="left-Col">
            <label for="Loại hồ sơ: ">Loại hồ sơ: </label>
        </div>
        <div class="right-Col" id="record_type">
            <?php echo $v_record_type_code;?> - <?php echo $v_record_type_name;?>
        </div>
    </div>

    <!-- Record list -->
    <div id="record_list">
        <table cellpadding="4" cellspacing="0" width="100%" class="list">
            <tr>
                <th>STT</th>
                <th>Mã hồ sơ</th>
                <th>Người đăng ký</th>
                <th>Ngày tiếp nhận</th>
                <th>Ngày hẹn trả</th>
                <th>Cán bộ thụ lý</th>
            </tr>
            <?php for ($i=0; $i<count($arr_all_record); $i++): ?>
                <tr id="tr_<?php echo $i;?>" class="tr_data">
                    <td class="right"><?php echo ($i+1);?></td>
                    <td id="td_record_no_<?php echo $i;?>"><?php echo $arr_all_record[$i]['C_RECORD_NO'];?></td>
                    <td id="td_citizen_name_<?php echo $i;?>"><?php echo $arr_all_record[$i]['C_CITIZEN_NAME'];?></td>
                    <td id="td_receive_date_<?php echo $i;?>"><?php echo $this->break_date_string(jwDate::yyyymmdd_to_ddmmyyyy($arr_all_record[$i]['C_RECEIVE_DATE'], TRUE));?></td>
                    <td id="td_return_date_<?php echo $i;?>"><?php echo $this->break_date_string($this->return_date_by_text($arr_all_record[$i]['C_RETURN_DATE']));?></td>
                    <td id="td_user_exec_<?php echo $i;?>"><?php echo get_xml_value(simplexml_load_string($arr_all_record[$i]['C_XML_PROCESSING']), "//step[contains(@code,'::THU_LY')][last()]/user_name");?></td>
                </tr>
            <?php endfor;?>
        </table>
    </div>
    <!-- End: Record list -->
    <div class="panel_color_form">Xét duyệt hồ sơ</div>
    <div class="Row">
        <div class="left-Col">
            Kết quả:
        </div>
        <div class="right-Col">
            <input type="radio" name="rad_approval" id="rad_<?php echo _CONST_RECORD_APPROVAL_ACCEPT;?>"
                   value="<?php echo _CONST_RECORD_APPROVAL_ACCEPT;?>"
                   onclick="rad_approval_onclick(this.value)"
                   <?php echo ($v_promote == _CONST_RECORD_APPROVAL_ACCEPT) ? ' checked' : '';?>
            />
            <label for="rad_<?php echo _CONST_RECORD_APPROVAL_ACCEPT;?>">Phê duyệt hồ sơ</label>

            <input type="radio" name="rad_approval" id="rad_<?php echo _CONST_RECORD_APPROVAL_REEXEC;?>"
                   value="<?php echo _CONST_RECORD_APPROVAL_REEXEC;?>"
                   onclick="rad_approval_onclick(this.value)"
            />
            <label for="rad_<?php echo _CONST_RECORD_APPROVAL_REEXEC;?>" class="approval-reexec">Trả lại, yêu cầu thụ lý</label>

            <input type="radio" name="rad_approval" id="rad_<?php echo _CONST_RECORD_APPROVAL_SUPPLEMENT;?>"
                   value="<?php echo _CONST_RECORD_APPROVAL_SUPPLEMENT;?>"
                   onclick="rad_approval_onclick(this.value)"
                   <?php echo ($v_promote == _CONST_RECORD_APPROVAL_SUPPLEMENT) ? ' checked' : '';?>
            />
            <label for="rad_<?php echo _CONST_RECORD_APPROVAL_SUPPLEMENT;?>" class="approval-supplement">Yêu cầu bổ sung hồ sơ</label>

            <input type="radio" name="rad_approval" id="rad_<?php echo _CONST_RECORD_APPROVAL_REJECT;?>"
                   value="<?php echo _CONST_RECORD_APPROVAL_REJECT;?>"
                   onclick="rad_approval_onclick(this.value)"
                   <?php echo ($v_promote == _CONST_RECORD_APPROVAL_REJECT) ? ' checked' : '';?>
            />
            <label for="rad_<?php echo _CONST_RECORD_APPROVAL_REJECT;?>" class="approval-reject">Từ chối hồ sơ</label>
        </div>
    </div>

    <div id="divFee" class="Row">
        <?php if (intval($v_fee) > 0): ?>
            <div class="Row">
                <div class="left-Col">
                    Lệ phí:<span class="required">*</span>
                </div>
                <div class="right-Col">
                    <input type="text" name="txt_fee" id="txt_fee"
                        size="8" maxlength="10" class="text ui-widget-content ui-corner-all"
                        value="<?php echo $v_fee;?>"/> (đ)
                </div>
            </div>
            <div class="Row">
                <div class="left-Col">
                    <span id="spanLyDo">Diễn giải:</span> <span class="required">*</span>
                </div>
                <div class="right-Col">
                    <textarea style="width:100%;height:100px;" rows="2"
                        name="txt_fee_description" id="txt_fee_description" cols="20" maxlength="400"
                        class="text ui-widget-content ui-corner-all"><?php echo $v_fee_description;?></textarea>
                </div>
            </div>
        <?php endif; ?>
        <?php $v_next_task_code = $arr_all_next_user[0]['C_TASK_CODE'];?>
        <?php if(count($arr_all_next_user) > 1):?>
            <div id="divLead" class="Row" <?php echo ($v_promote != _CONST_RECORD_APPROVAL_ACCEPT) ? ' style="display: none;"' : '';?>>
                <div class="left-Col">
                    <?php if (preg_match('/' . _CONST_XML_RTT_DELIM . _CONST_THU_PHI_ROLE . '$/', $v_next_task_code)): ?>
                        <label>Cán bộ thu phí <span class="required">*</span></label>
                    <?php elseif (preg_match('/' . _CONST_XML_RTT_DELIM . _CONST_TRA_KET_QUA_ROLE . '$/', $v_next_task_code)): ?>
                        <label>Cán bộ trả kết quả:<span class="required">*</span></label>
                    <?php else: ?>
                        <label>Lãnh đạo ký duyệt:<span class="required">*</span></label>
                    <?php endif;?>
                </div>
                <div class="right-Col">
                    <ul id="signer">
                    <?php for ($i=0; $i<count($arr_all_next_user); $i++): ?>
                        <li>
                            <input type="radio" value="<?php echo $arr_all_next_user[$i]['C_USER_LOGIN_NAME'];?>"
                               id="rad_signer_<?php echo $i;?>" name="rad_signer"
                               <?php echo ($i==0) ? ' checked' : '';?> />
                            <label for="rad_signer_<?php echo $i;?>">
                                <?php echo $arr_all_next_user[$i]['C_NAME'];?> <i>(<?php echo $arr_all_next_user[$i]['C_JOB_TITLE'];?>)</i>
                            </label>
                        </li>
                    <?php endfor;?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <?php if (preg_match('/' . _CONST_XML_RTT_DELIM . _CONST_THU_PHI_ROLE . '$/', $v_next_task_code)
                        OR preg_match('/' . _CONST_XML_RTT_DELIM . _CONST_TRA_KET_QUA_ROLE . '$/', $v_next_task_code)): ?>
            <div class="Row">
                <div class="left-Col">&nbsp;</div>
                <div class="right-Col">
                    <input type="button" class="solid print" value="In phiếu bàn giao" name="btn_print_record_list_to_handover_back" onclick="btn_print_record_list_to_handover_back_onclick();" />
                </div>
            </div>
        <?php else: ?>
        <!--
            <div class="Row">
                <div class="left-Col">&nbsp;</div>
                <div class="right-Col">
                    <input type="button" class="solid print" value="In danh sách ký duyệt" name="btn_print_record_list_to_sign"onclick="btn_print_record_list_to_sign_onclick();" />
                </div>
            </div>
        -->
        <?php endif;?>
    </div>

    <div id="divNote" class="Row" <?php echo ($v_promote == _CONST_RECORD_APPROVAL_ACCEPT) ? ' style="display: none;"' : '';?>>
        <div class="left-Col">
            <span id="spanLyDo">Lý do:</span> <span class="required">*</span>
        </div>
        <div class="right-Col">
            <textarea style="width:100%;height:100px;" rows="2"
            	name="txt_reason" id="txt_reason" cols="20" maxlength="400"
            	class="text ui-widget-content ui-corner-all"><?php echo $v_reason;?></textarea>
        </div>
    </div>
    <div id="print_reject" class="Row" <?php echo ($v_promote == _CONST_RECORD_APPROVAL_REJECT) ? '' :  ' style="display: none;"';?>>
        <div class="left-Col">&nbsp;</div>
        <div class="right-Col">
            <input type="button" class="solid print" value="In phiếu từ chối" name="btn_print_supplement_request" onclick="btn_print_reject_onclick();" />
        </div>
    </div>

    <!-- Sau khi bo sung -->
    <div id="divAfterSupplement" class="Row" <?php echo ($v_promote != _CONST_RECORD_APPROVAL_SUPPLEMENT) ? ' style="display: none;"' : '';?>>
        <div class="left-Col">
            <span id="spanLyDo">Sau khi bổ sung:</span> <span class="required">*</span>
        </div>
        <div class="right-Col">
            <input type="radio" name="rad_after_supplement" id="rad_after_supplement_1" value="1" checked/>
            <label for="rad_after_supplement_1">Chuyển đến bước phê duyệt hồ sơ bổ sung</label>

            <input type="radio" name="rad_after_supplement" id="rad_after_supplement_0" value="0" />
            <label for="rad_after_supplement_0">Duyệt lại từ đầu</label>
        </div>

        <div class="left-Col">&nbsp;</div>
        <div class="right-Col">
            <input type="button" class="solid print" value="In phiếu yêu cầu bổ sung" name="btn_print_supplement_request" onclick="btn_print_supplement_request_onclick();" />
        </div>
    </div>

    <div class="clear">&nbsp;</div>
    <!-- Buttons -->
    <div class="button-area">
        <hr/>
        <input type="button" name="btn_do_approval" class="button save" value="<?echo __('update');?>" onclick="btn_do_approval_onclick();" />
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
        <input type="button" name="cancel" class="button close" value="<?php echo __('close window'); ?>" onclick="<?php echo $v_back_action;?>"/>
    </div>
</form>
<script>
    function rad_approval_onclick(approval_value)
    {
        $("#hdn_approval_value").val(approval_value);
        if (approval_value == '<?php echo _CONST_RECORD_APPROVAL_ACCEPT;?>')
        {
            $("#divNote").css('display', 'none');
            $("#divFee").css('display', 'table');
            $("#divLead").css('display', 'table');
        }
        else
        {
            $("#divNote").css('display', 'table');
            $("#divFee").css('display', 'none');
            $("#divLead").css('display', 'none');
            document.frmMain.txt_reason.focus();
        }

        if (approval_value == '<?php echo _CONST_RECORD_APPROVAL_SUPPLEMENT;?>')
        {
            $("#divAfterSupplement").css('display', 'table');
        }
        else
        {
            $("#divAfterSupplement").css('display', 'none');
        }
        if (approval_value == '<?php echo _CONST_RECORD_APPROVAL_REJECT;?>')
        {
            $("#print_reject").css('display', 'table');
        }
        else
        {
            $("#print_reject").css('display', 'none');
        }
    }

    function btn_do_approval_onclick()
    {
        var f = document.frmMain;
        var v_approval_value = $("#hdn_approval_value").val();
        var v_reason = trim($("#txt_reason").val());

        if (v_approval_value != '<?php echo _CONST_RECORD_APPROVAL_ACCEPT;?>' && v_reason == '')
        {
            alert('Lý do không được bỏ trống!');
            f.txt_reason.focus();
            return false;
        }

        f.submit();
    }

    function btn_print_supplement_request_onclick()
    {
        if (trim($("#txt_reason").val()) == "")
        {
            alert('Lý do không được bỏ trống!');
            $("#txt_reason").focus();
            return;
        }
        
        var url = '<?php echo $this->get_controller_url();?>dsp_print_supplement_request/<?php echo $v_record_id_list;?>'; 
        url += '/?record_type_code=' + $("#record_type_code").val();
        url += '&pop_win=1';
        showPopWin(url, 800, 450, null, true);
    }

    function btn_print_reject_onclick()
    {
    	if (trim($("#txt_reason").val()) == "")
        {
            alert('Lý do không được bỏ trống!');
            $("#txt_reason").focus();
            return;
        }
        
        var url = '<?php echo $this->get_controller_url();?>dsp_print_reject_record/<?php echo $v_record_id_list;?>'; 
        url += '/?record_type_code=' + $("#record_type_code").val();
        url += '&pop_win=1';
        showPopWin(url, 800, 450, null, true);
    }
    
    function btn_print_record_list_to_sign_onclick()
    {
        v_record_id_list    = $("#hdn_item_id_list").val();
        v_record_type_code  = $("#hdn_record_type_code").val();
        v_record_type_name  = $("#hdn_record_type_name").val();

        if (v_record_id_list != '')
        {
            var url = '<?php echo $this->get_controller_url();?>dsp_print_record_list_to_sign/';
            url += '&record_id_list=' + v_record_id_list;
            url += '&record_type_code=' + v_record_type_code;
            url += '&record_type_name=' + v_record_type_name;

            showPopWin(url, 800, 450, null, true);
        }
    }

    //In bien ban ban giao ve cho Mot Cua
    function btn_print_record_list_to_handover_back_onclick()
    {
        v_record_id_list    = $("#hdn_item_id_list").val();
        v_record_type_code  = $("#hdn_record_type_code").val();
        v_record_type_name  = $("#hdn_record_type_name").val();

        if (v_record_id_list != '')
        {
            var url = '<?php echo $this->get_controller_url();?>dsp_print_record_list_to_handover_back/';
            url += '&record_id_list=' + v_record_id_list;
            url += '&record_type_code=' + v_record_type_code;
            url += '&record_type_name=' + v_record_type_name;

            showPopWin(url, 800, 450, null, true);
        }
    }
</script>
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');