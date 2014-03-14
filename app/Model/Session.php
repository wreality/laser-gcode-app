<?php
App::uses('AppModel', 'Model');

class Session extends AppModel {
	
	public $virtualFields = array(
		'isActive' => 'DATE_ADD(Session.modified, INTERVAL 10 MINUTE) > NOW()',
	);
	
	public function save($data = null, $validate = true, $fieldList = array()) {
		$data['user_id'] = CakeSession::read('Auth.User.id'); 
		
		return parent::save($data, $validate, $fieldList);
	}
	
	public function invalidateUserSession($user_id) {
		return $this->deleteAll(array('user_id' => $user_id));
	}
}