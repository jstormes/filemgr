FileMgr
=======
A ZendFramework 1.x library for managing abstract file uploads and downloads. It is designed to be used from a SlickGrid.

![file manager image](/image/filemanager.png)

This read me uses a table called `task_card_files` for a main table called `task`. This needs to be modified based on your requirements.



# File Uploads
Using jQuery File Upload http://blueimp.github.io/jQuery-File-Upload/
## Definitions
+ __fid__ - file id, a unique identifier for a single file.  Can be used to find a file in the database or download a file.  Every file has a unique id.

+ __fgid__ - file group id, a unique identifier for a group of files. used to map multiple files to a single row. 

+ __file_parent_id__ - this is the id of the 'thing' that owns the file. ie, the task card.

## Library

#### FileMgr > UploadHander.php
The php backend that jQuery File Upload uses to save and display files. 

+ The `handle_file_upload` method is modified to hook into the CAVOK file saving methods.
+ A `saveToDB` method is added to handle the interactions with the DB.

#### Library > Db > Table
Abstract.php - contains all the code to communicate with the file table in the db.

#### Library > View > Helper
Upload.php - Contains the HTML and JS for the File Upload button and file management element

Download.php -- TBD


## Application
### Models
#### Files
application > models > Files > XFiles.php (Where X is the name of that table that holds the file data)

Extends Abstract.php and contains properties specific to the table that is used to contain the file data.

    class Application_Model_Files_TaskcardFiles extends FileMgr_Db_Table_Abstract
    {
        protected $_name         = 'task_card_file';
        protected $_primary      = 'task_card_file_id';
        protected $_model        = 'TaskcardFile';
        
        // // the table that is the parent for all of these files
        // protected $_parent_table = 'task_card';
        // // the column in the $_parent_table that contains the parent id for the files
        // protected $_parent_id    = 'task_card_id';
        // // the model for the $_parent_table 
        // protected $_parent_model = 'Taskcard';
    }

## TaskcardController

Four methods that could stand to get moved perhaps to the `UploadHandler.php`

`filesAction()` that ultimately calls the `getFileNames($fgid)` method in Abstract.php. This is called by an $.ajax call on the initial load of the modal and returns the files associated with $fgid to list in the UI.

    /*
    |--------------------------------------------------------------------------
    | files
    |--------------------------------------------------------------------------
    |
    |    CVK File Manager
    |    This is the method that handles requests to get files
    |    from the ajax calls sent on initial load of the input modal
    |
    */
    public function filesAction()
    {
        // Disable menus and don't render any view.
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $fgid = $this->_request->getParam('fgid', null);

        if(! is_null($fgid)){
            $task_file_table = new Application_Model_Files_TaskcardFiles();
            $files = $task_file_table->getFileNames($fgid);
            echo json_encode($files);
            
        }else{
            echo 'No files';
        }

    }

`deletefileAction()` - deletes the file after the trash icon is clicked.

    /*
    |--------------------------------------------------------------------------
    | deletefile
    |--------------------------------------------------------------------------
    |
    |    CVK File Manager
    |    This is the method that handles file delete requests from the ajax 
    |    calls sent by the jquery file uploader
    |
    */
    public function deletefileAction()
    {
        // Disable menus and don't render any view.
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        
        $fid = $this->_request->getParam('fid', null);

        $task_file_table = new Application_Model_Files_TaskcardFiles();
        $files = $task_file_table->deleteFile($fid);

        echo 'ok';      
    }

`uploadAction()` - entry point for the file upload. Collected data is handed off to FileMgr_UploadHandler

    /*
    |--------------------------------------------------------------------------
    | upload
    |--------------------------------------------------------------------------
    |
    |    CVK File Manager
    |
    */
    public function uploadAction() 
    {
        // Disable menus and don't render any view.
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        
        // Zend_Registry::get('log')->debug(realpath(dirname(__FILE__)."/../../files"));

        $urlParams = $this->getRequest()->getParams();

        $options = array(
            'project_id'   => $this->project_id,
            'script_url'   => $this->view->url(array("action"=>"upload")),
            'upload_dir'   => realpath(dirname(__FILE__)."/../../files/"),
            //'upload_url' => $this->view->url(array('action'=>'upload'))
            'controller'   => $urlParams['controller'], // added for CAVOK file manager 
            'fileModel'    => new Application_Model_Files_TaskcardFiles() // added for CAVOK file manager
            );
        
        $upload_handler = new FileMgr_UploadHandler($options);
    }

`updatefilenoteAction()` - saves the file comment

    /*
    |--------------------------------------------------------------------------
    | updatefilenote
    |--------------------------------------------------------------------------
    |
    |    CVK File Manager
    |
    */
    public function updatefilenoteAction()
    {
        // Disable menus and don't render any view.
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $fid = $this->_request->getParam('fid', null);
        $note = $this->_request->getParam('note', null);

        $task_file_table = new Application_Model_Files_TaskcardFiles();
        $result = $task_file_table->updateFileNote($fid, $note);
        echo 'ok';
    }


## HTML
### Upload section
This code generates the file UI that is shown in the figure below.

    <!-- Files -->
    <div class="form-group col-md-4">
        <label class="control-label" for="fileRelatedFiles" id="labelRelatedFiles">Related Files:</label>
        <div class="controls">
            <div class="file-box">
                <?php echo $this->Upload('taskcard', $this->fileModel, 'filedata'); ?>
            </div>
      </div>
    <!-- End of Files -->

![file manager image](/image/filemanager.png)

## JS
### JS in Views and PHP 

+ `Upload.php` -- this contains the fileupload init code. After upload the jQuery returns name and size that can be used to append to the file list which is the `div#files`.
+ `input_modal.phtml` -- this is very specific to a SlickGrid implementation

        // ****************** added for the CVK File Manager ******************
        $('#fgid').val(input_row.fgid); // this is the id of the data row that was clicked on
        $('#files').html('');

        if(input_row.fgid == null || input_row.fgid.length === 0){
            $('#files').append('No files attached');
        }else{
            getFileList("<?=  $this->getFileUrl;?>/fgid/"+input_row.fgid, "taskcard", "TaskcardFiles"); // located in the getfilelist.js file
        }
        // added for the CVK File Manager

### JS Files

The jQuery Fileupload code (JS and CSS files) is in `public/filemgr/`

The three files below are located in `public/js/filemgr/` to add functionality to the jQuery Fileupload code.

`getfilelist.js` - on initial load of the modal, gets the files attached to the grid row. This is called from the input_modal.phtml file `.on('show.bs.modal')`

`delete.js` - handles file deletion when the trash icon is clicked. TODO - needs better abstraction.

`updatefilecomment.js` - handles the editing of the file comment

## CSS Files

These styles are added `public/filemgr/css/jquery.fileupload.css`

    /***********************************
    *
    * Custom CAVOK styling
    * 
    ************************************/
    .file-box{
        width: 100%;
    }
    .file-list{
        max-height: 160px;
        overflow: auto;
        margin-bottom: 15px;
    }
    .file-box-row{
        height: 20px;
        display: table-row;
    }
    .file-id, .file-name, .file-note, .file-delete{
        display: table-cell;
        padding-left: 4px;
        width: 20px;
        border-bottom: 1px solid silver;
    }
    .file-name{
        width: 200px;
    }
    .file-note{
        width: 200px;
    }
    .file-delete, .edit-note{
        cursor: pointer;
    }
    .file-delete:hover{
      color: red;
    }
    .edit-note:hover{
      color: #428BCA;
    }

## Program flow

Click upload button -> ajax to taskcard/upload

uploadAction -> builds options array and creates new FileMgr_UploadHandler($options)

###FileMgr_UploadHandler.php
This file is part of the jQuery Fileupload code base. `handle_file_upload()` is added for the CAVOK file storage scheme.

1. __construct()

2. Initialize() -> switch based on request_method ie., GET, POST etc

3. post() 



##FileMgrController.php

`downloadAction()` allows download of files from an anchor.

`<a href="/filemgr/download/fsn/df8eb95e.536/model/TaskcardFiles">8_17_1.jpg</a>`

* Pass the file_storage_nm as 'fsn'
* Pass the model that stores the file data as 'model'


##Database Tables

###UID table

A simple table that creates the unique id

    CREATE TABLE 'uid' (
        'uid' bigint(20) NOT NULL AUTO_INCREMENT,
        PRIMARY KEY ('uid')
    ) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

###File table

Change 'task_card_file' to the name of the table for your requirements

    CREATE TABLE 'task_card_file' (
        'task_card_file_id' bigint(20) NOT NULL AUTO_INCREMENT,
        'project_id' bigint(20) NOT NULL,
        'fgid' varchar(20) NOT NULL DEFAULT '',
        'uid' bigint(20) NOT NULL,
        'attachment_nm' varchar(255) NOT NULL,
        'file_nm' varchar(255) DEFAULT NULL,
        'file_size' varchar(25) DEFAULT NULL,
        'mime_type' varchar(50) DEFAULT NULL,
        'md5' varchar(50) DEFAULT NULL,
        'file_storage_nm' varchar(256) DEFAULT NULL,
        'notes_txt' varchar(2000) DEFAULT NULL,
        'crea_usr_id' varchar(64) DEFAULT NULL,
        'crea_dtm' timestamp NULL DEFAULT NULL,
        'updt_usr_id' varchar(64) DEFAULT NULL,
        'updt_dtm' timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        'deleted' tinyint(1) DEFAULT '0',
        'source_id' bigint(20) DEFAULT NULL,
        PRIMARY KEY ('task_card_file_id')
    ) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

###Main Table
 
 Needs a new column to hold the `fgid` value. In our example this would be the `task` table.

    'fgid' varchar(20) NOT NULL DEFAULT ''


##File Storage

Need a folder called `files` on the root level of the application. In use, additional folder with HEX values 0-F will be generated.

>This directory is the storage directory for files uploaded to the system by
users.  This directory should be ignored by source control and should be
backed up as part of your database backup routines as the files in this
directory are referenced by the database.