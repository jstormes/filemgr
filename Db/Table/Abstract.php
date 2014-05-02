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
    public $AbstractionDirectoryRoot;
    
    /**
     * Get a file path and mime type for a given file id (FID) 
     *
     * By: jstormes Apr 29, 2014
     *
     */
    public function getFile($fid) {
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
    public function getFileNames($fgid) {


        $result =  array(
            array('name'=>'File Name from the abstract','id'=>'1',
            'size'=>'3',
            'md5'=>'4'),

            array('name'=>'File Name',
            'id'=>'2',
            'size'=>'3',
            'md5'=>'4'),

            array('name'=>'File Name',
            'id'=>'3',
            'size'=>'3',
            'md5'=>'4')
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
    public function addFile($fgid, $path, $name, $mime_type=null) {
        
            $NewRow                     = $AttachmentTable->createRow();
            $NewRow->project_id         = $this->_request->getParam('project_id',null);
            $NewRow->$this->$_parent_id = $parent_id;
            $NewRow->fgid               = $fgid;
            $NewRow->uid                = $uid;
            $NewRow->attachment_nm      = $attachmentname;
            $NewRow->file_nm            = $filename;
            $NewRow->file_size          = $size;
            $NewRow->md5                = $md5;
            $NewRow->file_storage_nm    = basename($uid_filepath);
            $NewRow->notes_txt          = $notes_txt;
            $NewRow->crea_dtm           = date('Y/m/d H:i:s',time());
            $NewRow->crea_usr_id        = $this->user['user_id'];
            $NewRow->updt_dtm           = date('Y/m/d H:i:s',time());
            $NewRow->updt_usr_id        = $this->user['user_id'];
            $attachment_id              = $NewRow->save();

    }
    
}