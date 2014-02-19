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
			),
		),
		'email' => array(
			'account_exists' => array(
				'rule' => array('isUnique'),
				'message' => 'This email address has already been registered.',
				'last' => true,
				'required' => true,
				'allowEmpty' => false,
			),
			'email' => array(
				'rule' => array('email'),
			),
		),
		'password' => array(
			'notempty' => array(
				'rule' => array('notempty'),
			),
		),
		'confirm_password' => array(
			'notempty' => array(
				'rule' => array('validatePasswordConfirm'),
				'message' => 'Passwords do not match.',
				'required' => false,
				'allowEmpty' => false,
			),
		),
		'admin' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			),
		),
		
	);


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
	
	public function createValidationKey () {
		return sha1(mt_rand(10000,99999).time());
	}
	
	public function beforeSave($options = array()) {
		if (!$this->id) {
			$passwordHasher = new SimplePasswordHasher();
			$this->data[$this->alias]['password'] = $passwordHasher->hash(
				$this->data[$this->alias]['password']
			);
			$this->data[$this->alias]['validate_key'] = $this->createValidationKey();
		}
		return true;
	}

	public function enqueueEmail($email, $user_id = null) {
		if (empty($user_id)) {
			$user_id = $this->id;
		}
		$email =  ucfirst($email);
		if (method_exists('User', 'email'.$email)) {
			if (class_exists('CakeResque')) {
					CakeResque::enqueue('default', 'EmailSenderShell', array('send', 'User', $email, $user_id));
			} else {
				$this->{'email'.$email}($user_id);
			}
		} else {
			throw new InternalErrorException(__('Unknown email method.'));
		}
	}
	
	public function emailValidation($user_id) {
		App::uses('CakeEmail', 'Network/Email');

		$this->recursive = -1;
		$user = $this->findById($user_id);
		if (!$user) {
			throw new NotFoundException(__('User not found.'));
		}
		
		$email = new CakeEmail();
		
		$email->config('default')
			->template('verify_email')
			->emailFormat('html')
			->to($user['User']['email'])
			->viewVars(array('user' => $user))
			->send();
	
	}
	
	

}
