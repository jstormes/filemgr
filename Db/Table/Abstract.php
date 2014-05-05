<?php

/***
 * Abstract data model class to manage mapping abstract file names
 * to database tables.
 * 
 * Definitions
 * 
 * fid - file id, a unique identifier for a single file.  Can be used
 *       to find a file in the database or download a file.  Every file
 *       has a unique id.
 * fgid - file group id, a unique identifier for a group of files. used
 *        to map multiple files to a single row. 
 * 
 * @author jstormes
 *
 */



class FileMgr_Db_Table_Abstract extends Zend_Db_Table_Abstract
{
    /**
     * Can this model handle multiple files per row.
     * 
     * @var  boolean
     */
    public $MultiFile = false;
    
    /**
     * Root path to where files are kept.
     * 
     * @var unknown
     */
    public $AbstractionDirectoryRoot = '../files/';
    
    /**
     * Get a file path and mime type for a given file id (FID) 
     *
     * By: jstormes Apr 29, 2014
     *
     */
    public function getFile($fid) 
    {
         $sql = "SELECT *
                FROM {$this->_name}
                WHERE task_card_file_id = $fid
                AND deleted = 0";

        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchAll($query);         
    }
    
    /**
     * Get a list of files for the given fgid.
     *
     * By: jstormes Apr 29, 2014
     *
     * @param unknown $fgid
     */
    public function getFileNames($fgid) 
    {


        $result =  array(
            array('name'=>'File Name from the abstract that is ridiculously long I mean seriously','file_id'=>'1',
            'size'=>'3',
            'note'=>'This is a saved note that is in the abstract'),

            array('name'=>'File Name 2',
            'file_id'=>'2',
            'size'=>'3',
            'note'=>'This is a saved note that is in the abstract'),

            array('name'=>'File Name 3',
            'file_id'=>'3',
            'size'=>'3',
            'note'=>'This is a saved note that is in the abstract')
        );
        return $result;

        //  $sql = "SELECT *
        //         FROM {$this->_name}
        //         WHERE deleted = 0";

        // $query = $this->getAdapter()->quoteInto($sql,'');
        // return $this->getAdapter()->fetchAll($query);       
    }
    
    /**
     * Add a file from the file system to a fgid.  File will
     * be moved from it's current directory into the file abstraction
     * directory tree.
     *
     * By: jstormes Apr 29, 2014
     *
     * @param unknown $fgid
     */
// addFile($file->fgid, $file->storage_path, $file->storage_name, $file->name, $file->size, $file->type)
    public function addFile($fgid, $storage_path, $storage_name, $name, $mime_type = null) 
    {

        $className   = "Application_Model_DbTable_" . $this->_model;
        $fileModel   = new $className();
        
        $className   = "Application_Model_DbTable_" . $this->_parent_model;
        $parentModel = new $className();

var_dump($modelName);exit;
            // $NewRow                     = $AttachmentTable->createRow();
            // $NewRow->project_id         = $this->_request->getParam('project_id',null);
            // $NewRow->$this->$_parent_id = $parent_id;
            // $NewRow->fgid               = $fgid;
            // $NewRow->uid                = $uid;
            // $NewRow->attachment_nm      = $attachmentname;
            // $NewRow->file_nm            = $filename;
            // $NewRow->file_size          = $size;
            // $NewRow->md5                = $md5;
            // $NewRow->file_storage_nm    = basename($uid_filepath);
            // $NewRow->notes_txt          = $notes_txt;
            // $NewRow->crea_dtm           = date('Y/m/d H:i:s',time());
            // $NewRow->crea_usr_id        = $this->user['user_id'];
            // $NewRow->updt_dtm           = date('Y/m/d H:i:s',time());
            // $NewRow->updt_usr_id        = $this->user['user_id'];
            // $fid                        = $NewRow->save();

            return $fid;
    }
    
    public function getFileId()
    {
        
    }

    public function get_storage_filepath($rootpath = null) 
    {
        if(is_null($rootpath)){
            $rootpath = $this->AbstractionDirectoryRoot;
        }
        $goodname = false;
        while (!$goodname) {
            $candidate = sprintf('%8.8s.%3.3s',strrev(uniqid()),uniqid());
            if (!is_dir($rootpath.$candidate[0])) {
                mkdir($rootpath.$candidate[0]);
            }
            if (!file_exists($rootpath.$candidate[0]."/".$candidate)) {
                $goodname = true;
                $newfilepath = $rootpath.$candidate[0]."/".$candidate;
            }
        }
        return $newfilepath;
    }

    public function getFgid()
    {
        // CREATE uid in uid table
        $UidTable = new Application_Model_DbTable_Uid();
        return $UidTable->createRow()->save();
    }

     public function downloadAction() 
     {
        // Disable menus and don't render any view.
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $attachment_id   = $this->_request->getParam('attachment_id',0);
        $lowRes          = $this->_request->getParam('lowres',1);
        $imgVersion      = '';

        $AttachmentTable = new Application_Model_DbTable_Attachment();
        $AttachmentRow   = $AttachmentTable->find($attachment_id)->current();

        $file_name = '../files/'.$AttachmentRow->file_storage_nm[0].'/'.$AttachmentRow->file_storage_nm;  

        if($lowRes){
          if(file_exists($file_name . '.thumb')){
            $imgVersion = '.thumb';
          }
        }

        // $file_name = '../files/'.$AttachmentRow->file_storage_nm[0].'/'.$AttachmentRow->file_storage_nm.$imgVersion;  
        
        // get the file mime type using the file extension
        switch(strtolower(substr(strrchr($AttachmentRow->file_nm, '.'), 1))) {
            case 'pdf': $mime  = 'application/pdf'; break;
            case 'zip': $mime  = 'application/zip'; break;
            case 'jpeg': $mime = 'image/jpeg'; break;
            case 'jpg': $mime  = 'image/jpeg'; break;
            case 'png': $mime  = 'image/png'; break;
            case 'gif': $mime  = 'image/gif'; break;
            default:    $mime  = 'application/force-download';
        }

        header('Pragma: public'); // required
        header('Expires: 0');
        // no cache
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        // header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($file_name)).' GMT');
        header('Cache-Control: private',false);
        header('Content-Type: '. $mime);
        header('Content-Disposition: inline; filename="'.$AttachmentRow->file_nm.'"');
        header('Content-Transfer-Encoding: binary');
        // provide file size
        // header('Content-Length: '.$AttachmentRow->file_size);
        // header('Connection: close');
        // push it out
        readfile($file_name.$imgVersion);
        exit();
    }
}