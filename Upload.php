<?php
class FileMgr_Upload
{

	public function uploadAction() {
		// Disable menus and don't render any view.
		$this->_helper->layout()->disableLayout(true);
		$this->_helper->viewRenderer->setNoRender(true);
		$project_id=$this->_request->getParam('project_id',null);
		$accd_id=$this->_request->getParam('accd_id',null);
		$attachment_id = 0;
		// foreach uploaded file
		// var_dump($_POST);exit;
		// var_dump($_FILES);exit;
	
		// check for upload errors
		if ($_FILES["user_file"]["error"] == UPLOAD_ERR_OK) {
			$uid_filepath = $this->get_uid_filepath('../files/');
			$filepath = '../files/'.basename($_FILES["user_file"]['name']);
			$md5 = md5_file($_FILES["user_file"]['tmp_name']);
			$size = $_FILES["user_file"]['size'];
			$filename = basename($_FILES["user_file"]['name']);
	
			move_uploaded_file($_FILES["user_file"]['tmp_name'], $uid_filepath);
	
			$uid = 0;
	
			$accdTable = new Application_Model_DbTable_Accd();
			$accdRow = $accdTable->find($accd_id)->current();
			// var_dump($accdRow);exit;
			if (!is_null($accdRow->files)) {
				$uid            = $accdRow->files;
			} else {
				$UidTable       = new Application_Model_DbTable_Uid();
				$uid            = $UidTable->createRow()->save();
				$accdRow->files = $uid;
	
			}
			// If nothing changed Zend wont update row time, so force it.
			$accdRow->updt_dtm = new Zend_Db_Expr('NOW()');
			$accdRow->save();
	
			// Save to db table
			$AttachmentTable = new Application_Model_DbTable_Attachment();
			$NewRow                  = $AttachmentTable->createRow();
			$NewRow->project_id      = $this->_request->getParam('project_id',null);
			// $NewRow->grid_nm      = $this->_request->getParam('grid_nm',null);
			// $NewRow->column_nm    = $this->_request->getParam('column_nm',null);
			// $NewRow->row_id       = $this->_request->getParam('id',null);
			$NewRow->file_nm         = $filename;
			$NewRow->file_size       = $size;
			$NewRow->md5             = $md5;
			$NewRow->uid             = $uid;
			$NewRow->file_storage_nm = basename($uid_filepath);
			$NewRow->updt_dtm        = date('Y/m/d H:i:s',time());
			$NewRow->updt_usr_id     = $this->user['user_id'];
			$attachment_id           =  $NewRow->save();
		}
	
		// make a thumbnail for online viewing
		$thumbnail = $this->_helper->ResizeImageHelper($uid_filepath, $uid_filepath . '.thumb');
	
		$this->_redirect('/checklist/view/project_id/'.$this->project_id);
	}
	
	/**
	 * Given a path in the file system (rootpath) find a unique directory/file name
	 * path so that no file will ever be overwritten.  Return  
	 *
	 * By: jstormes Apr 22, 2014
	 *
	 * @param string $rootpath
	 * @return string
	 */
	private function get_uid_filepath($rootpath) {
		$goodname=false;
		while (!$goodname) {
			$candidate=sprintf('%8.8s.%3.3s',strrev(uniqid()),uniqid());
			if (!is_dir($rootpath.$candidate[0])) {
				mkdir($rootpath.$candidate[0]);
			}
			if (!file_exists($rootpath.$candidate[0]."/".$candidate)) {
				$goodname=true;
				$newfilepath=$rootpath.$candidate[0]."/".$candidate;
			}
		}
		return $newfilepath;
	}
	
}