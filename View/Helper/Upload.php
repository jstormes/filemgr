<?php
class FileMgr_View_Helper_Upload extends Zend_View_Helper_Abstract
{
    
    public function Upload($name, $model = null, $attribs = null, $options=null)
    {
        //<link rel="stylesheet" href="css/jquery.fileupload.css">
        $this->view->headLink()->appendStylesheet('/filemgr/css/jquery.fileupload.css','screen');
        
        $upload_url = $this->view->url(array("action"=>"upload"));
        
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
            <input id='file_id' type='hidden' value='{$value}'>
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
    $('#fileupload').fileupload({
        url: url,
        dataType: 'json',
        done: function (e, data) {
            $.each(data.result.files, function (index, file) {
                console.log(file);
                $('#files').append(
                    '<span class=\"file-box-row\">'
                    +'<span class=\"file-id\" data-toggle=\"popover\" data-content=\"test\">'+file.file_id+'</span>'
                    +'<span class=\"file-name\" title=\"'+file.name+'\">'+file.name+'</span>'
                    +'<span class=\"file-note\" title=\"'+file.note+'\">'+file.note+'</span>'
                    +'<span class=\"file-delete\"><a href=\"\"><i class=\"icon icon-trash\"></i><i class=\"fa fa-trash\"></i></span>'
                    +'</span>'
                );
            });
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .progress-bar').css(
                'width',
                progress + '%'
            );
        }
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
});

</script>       
                ";
        
        return $HTML;
    }
}