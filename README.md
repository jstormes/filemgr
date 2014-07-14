FileMgr
=======

A ZendFramework 1.x library for managing abstract file uploads and downloads


# File Uploads
Using jQuery File Upload http://blueimp.github.io/jQuery-File-Upload/
## Definitions
+ __fid__ - file id, a unique identifier for a single file.  Can be used to find a file in the database or download a file.  Every file has a unique id.

+ __fgid__ - file group id, a unique identifier for a group of files. used to map multiple files to a single row. 

+ __file_parent_id__ - this is the id of the 'thing' that owns the file. ie, the task card.

## Library
### FileMgr
#### UploadHander.php
The php backend that jQuery File Upload uses to save and display files. 

+ The `handle_file_upload` method is modified to hook into the CAVOK file saving methods.
+ A `saveToDB` method is added to handle the interactions with the DB.

#### Db > Table
Abstract.php - contains all the code to communicate with the file table in the db.

#### View > Helper
Download.php -- TBD

Upload.php - Contains the HTML and JS for the File Upload button and file managment element

## Application
### Models
#### Files
XXXXXXFiles.php - extends Abstract.php and contains properties specific to the table that is used to contain the file data.

## TaskcardController

Three methods that could stand to get moved perhaps to the FileMgrController

`filesAction()` that ultimately calls the `getFileNames($fgid)` method in Abstract.php. This is called by an $.ajax call on the initial load of the modal and returns the files associated with $fgid to list in the UI.

`deletefileAction()` - deletes the file after the trash icon is clicked.

`uploadAction()` - entry point for the file upload. Collected data is handed off to FileMgr_UploadHandler

## HTML
### Upload section

    <!-- Files -->
    <div class="form-group col-md-4">
        <label class="control-label" for="fileRelatedFiles" id="labelRelatedFiles">Related Files:</label>
        <div class="controls">
            <div class="file-box">
                <?php echo $this->Upload('taskcard', $this->fileModel, 'filedata'); ?>
            </div>
      </div>
    <!-- End of Files -->


## JS
### JS in Views and PHP 

+ Upload.php -- this contains the fileupload init code. After upload the jQuery returns name and size that can be used to append to the file list which is the `div#files`.
+ input_modal.phtml

### JS Files

getfilelist.js - on initial load of the modal, gets the files attached to the grid row. This is called from the input_modal.phtml file `.on('show.bs.modal')`

delete.js - handles file deletion when the trash icon is clicked. TODO - needs better abstraction.

## Program flow

Click upload button -> ajax to taskcard/upload

uploadAction -> builds options array and creates new FileMgr_UploadHandler($options)

###FileMgr_UploadHandler
1. __construct()

2. Initialize() -> switch based on request_method ie., GET, POST etc

3. post() 



##FileMgrController

Download function that allows download of files from an anchor.

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
