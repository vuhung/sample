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

//View data
$arr_all_record_type    = $VIEW_DATA['arr_all_record_type'];
$v_record_type_code     = $VIEW_DATA['record_type_code'];
$arr_all_record         = $VIEW_DATA['arr_all_record'];

//header
$this->template->title = 'Thu phi';
$this->template->display('dsp_header.php');

?>
<form name="frmMain" id="frmMain" action="" method="POST">
    <?php
    echo $this->hidden('controller',$this->get_controller_url());
    echo $this->hidden('hdn_item_id','0');
    echo $this->hidden('hdn_item_id_list','');

    echo $this->hidden('hdn_dsp_single_method','dsp_single_record');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_record');
    echo $this->hidden('hdn_update_method','update_record');
    echo $this->hidden('hdn_delete_method','delete_record');
    echo $this->hidden('hdn_handover_method','do_handover_record');

    echo $this->hidden('record_type_code', $v_record_type_code);

    ?>
    <?php echo $this->dsp_div_notice($VIEW_DATA['active_role_text'] );?>
    <!-- filter -->
    <div id="div_filter">
        (1)&nbsp;<label>Mã loại hồ sơ</label>
        <input type="text" name="txt_record_type_code" id="txt_record_type_code"
               value="<?php echo $v_record_type_code; ?>"
               class="inputbox upper_text" size="10" maxlength="10"
               onkeypress="txt_record_type_code_onkeypress(event);"
               autofocus="autofocus"
               accesskey="1"
               />&nbsp;
        <select name="sel_record_type" id="sel_record_type" style="width:75%; color:#000000;"
                onchange="sel_record_type_onchange(this)">
            <option value="">-- Chọn loại hồ sơ --</option>
            <?php echo $this->generate_select_option($arr_all_record_type, $v_record_type_code); ?>
        </select>
        <input type="text" name="noname" style="visibility: hidden"/>
    </div>
    <div class="clear"></div>

    <div id="procedure">
        <?php
        if ($this->load_abs_xml($this->get_xml_config($v_record_type_code, 'list')))
        {
             echo $this->render_form_display_all_record($arr_all_record, FALSE);
        }
        ?>
    </div>
    <div><?php echo $this->paging2($arr_all_record);?></div>
    <!-- Context menu -->
    <ul id="myMenu" class="contextMenu">
        <li class="charging">
            <a href="#charging">Thu phí</a>
        </li>
        <li class="statistics">
            <a href="#statistics">Xem tiến độ</a>
        </li>
    </ul>
	<ul id="myMenu_2" class="contextMenu">
        <li class="charging">
            <a href="#charging">Thu phí</a>
        </li>
        <li class="statistics">
            <a href="#statistics">Xem tiến độ</a>
        </li>
    </ul>
</form>
<script>
    $(document).ready( function() {
        //Show context on each row
        $(".adminlist tr[role='presentation']").contextMenu({
            menu: 'myMenu'
        }, function(action, el, pos) {
            v_record_id = $(el).attr('data-item_id');
            switch (action){
                case 'charging':
                    dsp_charging_record(v_record_id);
                    break;

                case 'statistics':
                    dsp_single_record_statistics(v_record_id);
                    break;
            }
        });

        //Quick action
        <?php if (strtoupper($this->active_role) == _CONST_THU_PHI_ROLE): ?>
            $('.adminlist tr[role="presentation"] td[role="action"] .quick_action').each(function(index) {
                v_item_id =   $(this).attr('data-item_id');

                html = '';

                //Thu phi
                html = '<a href="javascript:void(0)" onclick="dsp_charging_record(\'' + v_item_id + '\')" class="quick_action" >';
                html += '<img src="' + SITE_ROOT + 'public/images/money-16x16.png" title="Thu phí" /></a>';

                //Thong tin tien do
                html += '<a href="javascript:void(0)" onclick="dsp_single_record_statistics(\'' + v_item_id + '\')" class="quick_action" >';
                html += '<img src="' + SITE_ROOT + 'public/images/statistics-16x16.png" title="Xem tiến đọ" /></a>';

                $(this).html(html);
            });

        <?php endif;?>
    });

    function dsp_charging_record(record_id)
    {
        var url = '<?php echo $this->get_controller_url();?>dsp_charging/' + record_id
                + '/&record_type_code=' + $("#record_type_code").val()
                + '&pop_win=1';
            showPopWin(url, 800, 400, null, true);
    }

</script>
<?php $this->template->display('dsp_footer.php');