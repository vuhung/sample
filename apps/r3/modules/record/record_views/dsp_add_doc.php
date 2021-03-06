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

if (!defined('SERVER_ROOT')) { exit('No direct script access allowed');}

//display header
$this->template->title = 'Thêm tài liệu';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');

$v_record_id = isset($_REQUEST['hdn_item_id']) ? $_REQUEST['hdn_item_id'] : 0;

($v_record_id > 0) OR DIE('Thao tác sai');

?>
<script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.iframe-post-form.js" type="text/javascript"></script>
<form name="frmMain" id="frmMain" method="POST" action="<?php echo $this->get_controller_url();?>do_add_doc" enctype="multipart/form-data">
    <?php echo $this->hidden('hdn_item_id', $v_record_id);?>
    <?php echo $this->hidden('hdn_update_method', 'do_add_doc');?>
    <?php echo $this->hidden('XmlData', '');?>
    <?php echo $this->hidden('controller', $this->get_controller_url());?>
    <table class="none-border-table" width="100%">
        <tr>
            <td>Số ký hiệu:<span class="required">(*)</span></td>
            <td>
                <input name="txt_record_doc_no" id="txt_record_doc_no" maxlength="50" style="width:200px"
                       type="text" value="" data-allownull="no" data-validate="text" data-name="Số ký hiệu:" data-xml="no" data-doc="no" />
            </td>
            <td>Nơi ban hành:</td>
            <td>
                <input  name="txt_issuer" id="txt_issuer"
                       style="width:200px" type="text" value="" data-allownull="yes"
                       data-validate="text" data-name="Nơi ban hành" data-xml="no" data-doc="no" />
            </td>
        </tr>
        <tr>
            <td>Tên tài liệu:<span class="required">(*)</span></td>
            <td colspan="3">
                <input  name="txt_description" id="txt_description"
                       style="width:580px" type="text" value="" data-allownull="no"
                       data-validate="text" data-name="Tên tài liệu" data-xml="no" data-doc="no" />
            </td>
        </tr>
        <tr>
            <td colspan="4">Nội dung:</td>
        </tr>
        <tr>
            <td colspan="4">
                <textarea style="width:89%;height:100px;" rows="2"
                    name="txt_content" id="txt_content" cols="20" maxlength="400"
                    ></textarea>
            </td>
        </tr>
    </table>
    <div class="Row">
        <label>
            Tài liệu đính kèm
        </label>
        <input type="file" style="border: solid #D5D5D5; color: #000000" class="multi accept-<?php echo _CONST_RECORD_FILE_ACCEPT;?>" name="uploader[]"
            id="File1" />
        <font style="font-weight: normal;">Hệ thống chỉ chấp nhận đuôi file dạng:</font><span
            class="fileUploaderMessage"> doc; docx; pdf;</span><br/>
    </div>
    <div class="clear">&nbsp;</div>
    <!-- Buttons -->
    <div class="button-area">
        <input type="button" name="btn_do_add_doc" class="button save" value="Cập nhật" onclick="btn_do_add_doc_onclick();" />
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
        <input type="button" name="cancel" class="button close" value="<?php echo __('close window'); ?>" onclick="<?php echo $v_back_action;?>"/>
    </div>
</form>
<script>
    var f=document.frmMain;
    function btn_do_add_doc_onclick()
    {
        var v_record_doc_no = trim(f.txt_record_doc_no.value);
        var v_description = trim(f.txt_description.value);
        var v_issuer = trim(f.txt_issuer.value);
        var v_content = trim(f.txt_content.value);

        if (v_record_doc_no == '')
        {
            alert('Bạn chưa số/ký hiệu!');
            f.txt_record_doc_no.focus();
            return false;
        }

        if (v_description == '')
        {
            alert('Bạn chưa nhập tên tài liệu!');
            f.txt_description.focus();
            return false;
        }

        f.submit();
        return;
    }
</script>
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');