FileMgr
=======

A ZendFrameowrk 1.x library for managing abstract file uploads and downloads


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

Upload.php -- Contains the HTML and JS for the File Upload button and file managment element

## Application
### Models
#### Files
XXXXXXFiles.php - extends Abstract.php and contains properties specific to the table that is used to contain the

## TaskcardController

Three methods that could stand to get moved perhaps to the FileMgrController

`filesAction()` that ultimately calls the `getFileNames($fgid)` method in Abstract.php. This is called by an $.ajax call on the initial load of the modal and returns the files associated with $fgid to list in the UI.

`deletefileAction()` - deletes the file after the trash icon is clicked.

`uploadAction()` - entry point for the file upload. Collected data is handed off to FileMgr_UploadHandler

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

