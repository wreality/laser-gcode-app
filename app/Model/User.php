<?php
App::uses('AppModel', 'Model');
App::uses('Project', 'Model');
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');

/**
 * User Model
 *
 * @property Project $Project
 * @property Session $Session
 */
class User extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'username';

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
		'current_password' => array(
			'validatePassword' => array(
				'rule' => array('validateCurrentPassword'),
				'message' => 'Current password does not match.',
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
 * Default sorting order for model
 *
 * @var string
 */
	public $order = 'User.username';

/**
 * Constants for user active field.
 *
 * @var integer
 */
	const USER_ACTIVE = 1;
	const USER_INACTIVE = 0;
	const USER_BAN = 2;

/**
 * Enumeration of status constants
 *
 * @var array
 */
	public static $statuses = array(
		User::USER_ACTIVE => 'Active',
		User::USER_INACTIVE => 'Inactive',
		User::USER_BAN => 'Banned',
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
		), 'Session' => array(
			'className' => 'Session',
			'foreignKey' => 'user_id',
			'order' => array(
				'expires' => 'DESC',
			),
			'limit' => 1,
		),
	);

	public $hasOne = array(
		'ProjectDefault' => array(
			'className' => 'Project',
			'foreignKey' => 'user_id',
			'dependent' => true,
			'conditions' => array(
				'public' => Project::PROJ_DEFAULTS
			)
		),

	);

/**
 * beforeValidate method
 *
 * Enables validation for user_secret if this functionality is enabled in
 * application configuration.
 *
 * (non-PHPdoc)
 * @see Model::beforeValidate()
 */
	public function beforeValidate($options = array()) {
		if (Configure::read('LaserApp.user_secret_enabled') && (!$this->id)) {
			$this->validator()->add('user_secret', 'required', array(
					'rule' => array('validateSecret'),
					'message' => 'Secret entered was not correct.',
					'required' => true,
					'allowEmpty' => false,
					'on' => 'create'));

		}
		return true;
	}

/**
 * validateSecret method
 *
 * Custom validation function to check that secret, if entered, matches the
 * secret configured for the application.
 *
 * @param array $check
 * @return boolean
 */
	public function validateSecret($check) {
		if ($this->data[$this->alias]['user_secret'] == Configure::read('LaserApp.user_secret')) {
			return true;
		} else {
			return false;
		}
	}

/**
 * validatePasswordConfirm method
 *
 * Custom validation function to check that the password and confirm_password
 * match when submitted together.
 *
 * @param array $check
 * @return boolean
 */
	public function validatePasswordConfirm($check) {
		if ($this->data[$this->alias]['password'] == $this->data[$this->alias]['confirm_password']) {
			return true;
		} else {
			$this->invalidate('password', null);
			return false;
		}
	}

/**
 * createValidationKey method
 *
 * Creates a validation key of the requested type.
 *
 * @param char $type
 * @return string
 */
	public function createValidationKey($type) {
		return $type . ':' . sha1(mt_rand(10000, 99999) . time());
	}

/**
 * validateCurrentPassword method
 *
 * If current password is supplied.  Confirm that it is correct for the given
 * user.
 *
 * @param unknown $check
 * @return boolean
 */
	public function validateCurrentPassword($check) {
		$passwordHasher = new BlowfishPasswordHasher();
		return $passwordHasher->check($this->data[$this->alias]['current_password'], $this->field('password'));
	}

/**
 * requireCurrentPassword
 *
 * Update validation array to require current password for the current operation.
 */
	public function requireCurrentPassword() {
		$this->validate['current_password']['validatePassword']['required'] = true;
	}

/**
 * beforeSave method
 *
 * Hashes plaintext passwords if supplied and creates a verification validation
 * key if the save operation is creating a user record.
 *
 * (non-PHPdoc)
 * @see Model::beforeSave()
 */
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

/**
 * findByValidationKey method
 *
 * Overloaded magic function that finds and returns a user matching the
 * requested validation key type and key.
 *
 * @param char $type
 * @param string $key
 * @return string
 */
	public function findByValidateKey($type, $key) {
		$user = parent::findByValidateKey($type . ':' . $key);
		if (!empty($user)) {
			$user['User']['validate_key'] = substr($user['User']['validate_key'], 2, strlen($user['User']['validate_key'] - 2));
		}
		return $user;
	}

/**
 * findForEmail method
 *
 * Standard findById function extended to remove validation key type prefix
 * from validation key before returning resulting records.  Used by email
 * functions to present a clean validation key to email templates.
 *
 * @param user_id $id
 * @return mixed (array|boolean)
 */
	public function findForEmail($id) {
		$user = $this->findById($id);

		if (!empty($user)) {
			$user['User']['validate_key'] = substr($user['User']['validate_key'], 2, strlen($user['User']['validate_key']) - 2);
		}
		return $user;
	}

/**
 * enqueueEmail method
 *
 * Enqueue or immediately send the requested email function depending on if
 * CakeResque is available.
 *
 * @param string $email
 * @param user_id $userId
 * @throws InternalErrorException
 */
	public function enqueueEmail($email, $userId = null) {
		if (empty($userId)) {
			$userId = $this->id;
		}
		$email = ucfirst($email);
		if (method_exists('User', 'email' . $email)) {
			if (class_exists('CakeResque')) {
					CakeResque::enqueue('default', 'EmailSenderShell', array('send', 'User', $email, $userId));
			} else {
				$this->{'email' . $email}($userId);
			}
		} else {
			throw new InternalErrorException(__('Unknown email method.'));
		}
	}

/**
 * emailValidation method
 *
 * Send a validation email to the supplied user_id.
 *
 * @param user_id $userId
 * @throws NotFoundException
 */
	public function emailValidation($userId) {
		$this->recursive = -1;
		$user = $this->findForEmail($userId);
		if (!$user) {
			throw new NotFoundException(__('User not found.'));
		}

		$email = $this->_getMailer();

		$email->config('default')
			->template('verify_email')
			->emailFormat('html')
			->to($user['User']['email'])
			->subject(__('[%s] Verify Email Address', Configure::read('App.title')))
			->viewVars(array('user' => $user, 'title_for_layout' => __('Verify Email Address')))
			->send();
	}

/**
 * emailResetPassword method
 *
 * Send email prompting user to reset password by following validation link.
 *
 * @param user_id $userId
 * @throws NotFoundException
 */
	public function emailResetPassword($userId) {
		$this->recursive = -1;
		$user = $this->findForEmail($userId);
		if (!$user) {
			throw new NotFoundException(__('User not found.'));
		}

		$email = $this->_getMailer();

		$email->config('default')
			->template('reset_password')
			->emailFormat('html')
			->to($user['User']['email'])
			->subject(__('[%s] Reset Password', Configure::read('App.title')))
			->viewVars(array('user' => $user, 'title_for_layout' => __('Reset Password')))
			->send();
	}

/**
 * emailUpdateEmail method
 *
 * Send email requesting confirmation of an updated email address.
 *
 * @param user_id $userId
 * @throws NotFoundException
 */
	public function emailUpdateEmail($userId) {
		$this->recursive = -1;
		$user = $this->findForEmail($userId);
		if (!$user) {
			throw new NotFoundException(__('User not found'));
		}

		$email = $this->_getMailer();
		$email->config('default')
			->template('update_email')
			->emailFormat('html')
			->to($user['User']['validate_data'])
			->subject(__('[%s] Verify New Email', Configure::read('App.title')))
			->viewVars(array('user' => $user, 'title_for_layout' => ('Verify New Email')))
			->send();
	}

/**
 * emailInvalidatePassword method
 *
 * Send email notifying user that their password has been invalidated by an
 * admin user.
 *
 * @param unknown $userId
 * @throws NotFoundException
 */
	public function emailInvalidatePassword($userId) {
		$this->recursive = -1;
		$user = $this->findForEmail($userId);
		if (!$user) {
			throw new NotFoundException(__('User not found.'));
		}

		$email = $this->_getMailer();

		$email->config('default')
		->template('invalidate_password')
		->emailFormat('html')
		->to($user['User']['email'])
		->subject(__('[%s] Reset Password', Configure::read('App.title')))
		->viewVars(array('user' => $user, 'title_for_layout' => __('Reset Password')))
		->send();
	}

/**
 * newUser method
 *
 * Create a new user, optionally activating the new user account immediately.
 *
 * @param unknown $data
 * @param string $active
 * @return Ambigous <mixed, boolean, multitype:>
 */
	public function newUser($data, $active = false) {
		$data['User']['active'] = $active;
		$data['User']['project_count'] = 0;
		$data['User']['public_count'] = 0;
		$this->create();
		return $this->save($data, true, array(
			'username', 'email', 'password', 'active', 'validate_key',
			'project_count', 'public_count', 'confirm_password', 'user_secret'));
	}

/**
 * saveValidateData method
 *
 * Wrapper method for save which limits fields to only validation data.
 *
 * @param unknown $data
 * @return Ambigous <mixed, boolean, multitype:>
 */
	public function saveValidateData($data) {
		return $this->save($data, true, array('validate_data', 'validate_key'));
	}

/**
 * updateEmail method
 *
 * Wrapper method to save which limits fields to only those needed to update
 * email following validation.
 *
 * @param unknown $data
 * @return Ambigous <mixed, boolean, multitype:>
 */
	public function updateEmail($data) {
		return $this->save($data, true, array('email', 'validate_data', 'validate_key'));
	}

/**
 * updateUsername method
 *
 * Wrapper method to save which limits fields to only those needed to update
 * username.
 *
 * @param unknown $data
 * @return Ambigous <mixed, boolean, multitype:>
 */
	public function updateUsername($data) {
		return $this->save($data, true, array('username'));
	}

/**
 * updatePassword method
 *
 * Wrapper function for save which limits fields to only those needed to update
 * password.
 *
 * @param unknown $data
 * @param bool $requireCurrent If true, the current, correct password must be supplied in $data
 * @return Ambigous <mixed, boolean, multitype:>
 */
	public function updatePassword($data, $requireCurrent = true) {
		$fields = array('password', 'confirm_password');
		if ($requireCurrent) {
			$this->requireCurrentPassword();
			$fields[] = 'current_password';
		} else {
			$data['User']['validate_key'] = null;
			$data['User']['validate_data'] = null;
			$data['User']['active'] = User::USER_ACTIVE;
			$fields = array_merge($fields, array('active', 'validate_key', 'validate_data'));
		}
		return $this->save($data, true, $fields);
	}

/**
 * _getMailer method
 *
 * Implemenation for unit test mocking.  Returns CakeEmail object.
 *
 * @return CakeEmail
 */
	protected function _getMailer() {
		App::uses('CakeEmail', 'Network/Email');
		return new CakeEmail();
	}
}
