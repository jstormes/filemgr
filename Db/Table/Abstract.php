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
		
	}
	
	/**
	 * Get a list of files (name and fid) for the given id.
	 *
	 * By: jstormes Apr 29, 2014
	 *
	 * @param unknown $fgid
	 */
	public function getFileNames($fgid) {
		
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
		
	}
	
}