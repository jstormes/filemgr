<?php
class FileMgr_View_Helper_Upload extends Zend_View_Helper_Abstract
{
    
    public function Upload($name, $model = null, $filedata = null, $options=null)
    {
        //<link rel="stylesheet" href="css/jquery.fileupload.css">
        $this->view->headLink()->appendStylesheet('/filemgr/css/jquery.fileupload.css','screen');
        
        $upload_url = $this->view->url(array("action"=>"upload"));
        
        $controller = 'taskcard';  //*** TODO find a hook in to get this value dynamically ****///
        $model = 'TaskcardFiles'; //*** TODO find a hook in to get this value dynamically ****///

        $HTML = @"
    <!-- The fileinput-button span is used to style the file input field as button -->
        <!-- The container for the uploaded files -->
        <div class='file-list'>
            <div id='files' class='files'></div>
        </div>
        <div class='col-lg-4'>

        <span class='btn btn-primary btn-sm fileinput-button'>
            <i class='fa fa-plus-circle'></i><i class='icon icon-plus-sign'></i> 
            <span>Select files...</span>
            <!-- The file input field used as target for the file upload widget -->
            <input id='fileupload' type='file' name='files[]' multiple>
            <input id='fgid' type='hidden' value='{$value}'>
        </span>
        </div>
        <div class='col-lg-8'>
        <!-- The global progress bar -->
        <div id='progress' class='progress'>
            <div class='progress-bar progress-bar-success'></div>
        </div>
        </div>
                ";
        
        $HTML .= @"
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src='/filemgr/js/jquery.iframe-transport.js'></script>
<!-- The basic File Upload plugin -->
<script src='/filemgr/js/jquery.fileupload.js'></script>
                
<script>
/*jslint unparam: true */
/*global window, $ */

$(function () {
    'use strict';

    // Change this to the location of your server-side upload handler:
    var url = '{$upload_url}';
    var controller = '{$controller}';
    var model = '{$model}';

    $('#fileupload').fileupload({
        url: url,
        dataType: 'json',
        done: function (e, data) {
            $.each(data.result.files, function (index, file) {
                
                // after the file is uploaded then add it to the end of the file list in the UI
                $('#files').append(
                    '<span id=\"fid-'+file.task_card_file_id+'\" class=\"file-box-row\">'
                    +'<span class=\"file-id\" title=\"'+file.task_card_file_id+'\"><i class=\"icon icon-file\"></i><i class=\"fa fa-file\"></i></span>'
                    +'<span class=\"file-name\" title=\"'+file.file_nm+'\">'
                    +'<a href=\"/filemgr/download/fsn/'+file.file_storage_nm+ '/model/'+model+'\">'+file.file_nm+'</a>'
                    +'</span>'
                    +'<span class=\"file-note\" title=\"'+file.notes_txt+'\">'
                    +'<i data-fid=\"'+file.task_card_file_id+'\" class=\"edit-note icon icon-pencil\"></i><i data-fid=\"'+file.task_card_file_id+'\" class=\"edit-note fa fa-pencil\"></i>&nbsp;'
                    +'<span id=\"fid-note-'+file.task_card_file_id+'\">'+file.notes_txt+'</span>'
                    +'</span>'
                    +'<span class=\"file-delete\"><i data-fid=\"'+file.task_card_file_id+'\" data-controller=\"'+controller+'\"  class=\"icon icon-trash\"></i><i data-fid=\"'+file.file_id+'\" data-controller=\"'+controller+'\"  class=\"fa fa-trash\"></i></span>'
                    +'</span>'                );
                $('#fgid').val(file.fgid); // this is the id of the data row that was clicked on
            });
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .progress-bar').css(
                'width',
                progress + '%'
            );
        }
    })
    .prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled')
    .bind('fileuploadsubmit', function (e, data){
        // console.log('fgid at file upload ');console.log($('#fgid').val());
        data.formData = {fgid: $('#fgid').val()};
    })
    .bind('fileuploaddone -- bind', function (e, data){
        // console.log('fileuploaddone');console.log(data);
    });
});

</script>";
        
        return $HTML;
    }
}