 <?php
App::uses('AppModel', 'Model');
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');
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
		'username' => array(
			'username_exists' => array(
				'rule' => array('isUnique'),
				'message' => 'This username is already taken.',
				'allowEmpty' => false,
				'required' => true,
				'on' => 'create',
			),
			'username_exists2' => array(
				'rule' => array('isUnique'),
				'message' => 'This username is already taken.',
				'allowEmpty' => false,
				'required' => false,
				'on' => 'update'
			),
		),
		'email' => array(
			'account_exists' => array(
				'rule' => array('isUnique'),
				'message' => 'This email address has already been registered.',
				'last' => true,
				'required' => true,
				'allowEmpty' => false,
				'on' => 'create',
			),
			'account_exists2' => array(
				'rule' => array('isUnique'),
				'message' => 'This email address has already been registered.',
				'required' => false,
				'allowEmpty' => false,
				'on' => 'update'
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
	
	public function createValidationKey ($type) {
		return $type.':'.sha1(mt_rand(10000,99999).time());
	}
	
	public function beforeSave($options = array()) {
		if (isset($this->data[$this->alias]['confirm_password'])) {
			$passwordHasher = new BlowfishPasswordHasher();
			$this->data[$this->alias]['password'] = $passwordHasher->hash(
				$this->data[$this->alias]['password']
			);
		}
		if (!$this->id) {
			$this->data[$this->alias]['validate_key'] = $this->createValidationKey('v');
		}
		return true;
	}
	
	public function findByValidateKey($type, $key) {
		$user = parent::findByValidateKey($type.':'.$key);
		if (!empty($user)) {
			$user['User']['validate_key'] = substr($user['User']['validate_key'], 2, strlen($user['User']['validate_key'] - 2));
		}
		return $user;
	}
	
	public function findForEmail($id) {
		$user = $this->findById($id);
		
		if (!empty($user)) {
			$user['User']['validate_key'] = substr($user['User']['validate_key'], 2, strlen($user['User']['validate_key']) - 2);
		}
		return $user;
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
		$user = $this->findForEmail($user_id);
		if (!$user) {
			throw new NotFoundException(__('User not found.'));
		}
		
		$email = new CakeEmail();
		
		$email->config('default')
			->template('verify_email')
			->emailFormat('html')
			->to($user['User']['email'])
			->subject(__('[%s] Verify Email Address', Configure::read('App.title')))
			->viewVars(array('user' => $user, 'title_for_layout' => __('Verify Email Address')))
			->send();
	
	}
	
	public function emailResetPassword($user_id) {
		App::uses('CakeEmail', 'Network/Email');
	
		$this->recursive = -1;
		$user = $this->findForEmail($user_id);
		if (!$user) {
			throw new NotFoundException(__('User not found.'));
		}
	
		$email = new CakeEmail();
	
		$email->config('default')
			->template('reset_password')
			->emailFormat('html')
			->to($user['User']['email'])
			->subject(__('[%s] Reset Password', Configure::read('App.title')))
			->viewVars(array('user' => $user, 'title_for_layout' => __('Reset Password')))
			->send();
	
	}
	
	public function emailUpdateEmail($user_id) {
		App::uses('CakeEmail', 'Network/Email');
		
		$this->recursive = -1;
		$user = $this->findForEmail($user_id);
		if (!$user) {
			throw new NotFoundException(__('User not found'));
		}
		
		$email = new CakeEmail();
		$email->config('default')
			->template('update_email')
			->emailFormat('html')
			->to($user['User']['validate_data'])
			->subject(__('[%s] Verify New Email', Configure::read('App.title')))
			->viewVars(array('user' => $user, 'title_for_layout' => ('Verify New Email')))
			->send();
	}
	
	

}
