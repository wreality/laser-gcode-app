<?php
App::uses('AppModel', 'Model');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
/**
 * User Model
 *
 * @property Project $Project
 */
class User extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'email';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'id' => array(
			'uuid' => array(
				'rule' => array('uuid'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'email' => array(
			'account_exists' => array(
				'rule' => array('validateEmailDoesntExist'),
				'message' => 'This email address has already been registered.',
				'last' => true,
				'required' => true,
				'allowEmpty' => false,
			),
			'email' => array(
				'rule' => array('email'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'password' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'confirm_password' => array(
			'notempty' => array(
				'rule' => array('validatePasswordConfirm'),
				'message' => 'Passwords do not match.',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'admin' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'user_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

	public function beforeValidate($options = array()) {
		if (Configure::read('LaserApp.user_secret_enabled')  && (!$this->id)) {
			$this->validator()->add('user_secret', 'required', array(
					'rule' => array('validateSecret'),
					'message' => 'Secret entered was not correct.',
					'allowEmpty' => false,
					'on' => 'create'));
			
		}
		return true;
	}
	
	public function validateSecret ($check){
		if ($this->data[$this->alias]['user_secret'] == Configure::read('LaserApp.user_secret')) {
			return true;
		} else {
			return false;
		}
	}
	
	public function validatePasswordConfirm ($check) {
		if ($this->data[$this->alias]['password'] == $this->data[$this->alias]['confirm_password']) {
			return true;
		} else {
			$this->invalidate('password', null);
			return false;
		}
	}
	
	public function validateEmailDoesntExist ($check) {
		$user = $this->findByEmail($this->data[$this->alias]['email']);
		
		if (empty($user)) {
			return true;
		} else {
			return false;
		}
	}
	
	public function beforeSave($options = array()) {
		if (!$this->id) {
			$passwordHasher = new SimplePasswordHasher();
			$this->data[$this->alias]['password'] = $passwordHasher->hash(
				$this->data[$this->alias]['password']
			);
		}
		return true;
	}

}
