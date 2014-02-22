<?php
App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 */
class UsersController extends AppController {
	
/**
 * beforeFilter method
 * 
 * Allow public actions.  Calls parent method.
 * 
 * (non-PHPdoc)
 * @see AppController::beforeFilter()
 */
	public function beforeFilter() {
		$this->Auth->allow('register', 'verify', 'lost_password', 'reset');
		parent::beforeFilter();
	
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
		$this->set('user', $this->User->find('first', $options));
	}

/**
 * register method
 * 
 * Processes user registrations. 
 *
 * @return void
 */
	public function register() {
		$this->_throttleAction();
		
		if ($this->request->is('post')) {
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('Account Created'), 'bs_success');
				$this->User->enqueueEmail('Validation');
				$this->render('register_confirm');
			} else {
				$this->Session->setFlash(__('Your account could not be created.  Please, try again.'), 'bs_error');
			}
		}
	}

/**
 * verify method
 * 
 * Verify email at registration.  
 * 
 * @param string $validate_key
 * @return CakeResponse
 */
	public function verify($validate_key) {
		$user = $this->User->findByValidateKey('v',$validate_key);
		
		if (!$user) {
			return $this->render('verify_error');
		} else {
			$user['User']['active'] = true;
			$user['User']['validate_key'] = null;
			if ($this->User->save($user)) {
				return $this->render('verify_success');	
			} else {
				var_dump($this->User->validationErrors);
				return $this->render('verify_error');
			}
		}
	}
	
/**
 * lost_password method
 * 
 * Allows users to request password reset.  Sets verification key and sends 
 * reset email to email address on record.
 * 
 * @throws InternalErrorException
 * @return CakeResponse
 */
	public function lost_password() {
		
		if ($this->request->is('post')) {
			$this->_throttleAction();
			$user = $this->User->findByEmail($this->request->data['User']['email']);
			if (!empty($user)) {
				$user['User']['validate_key'] = $this->User->createValidationKey('r');
				if (!$this->User->save($user, false, array('validate_key'))) {
					throw new InternalErrorException(__('Unable to save validation key.'));
				}
				$this->User->enqueueEmail('ResetPassword');
			}
			return $this->render('reset_sent');
		}
	}
	
/**
 * reset method
 * 
 * Processes return from password reset request email.
 * 
 * @param string $validate_key
 * @return CakeResponse
 */
	public function reset($validate_key) {
		$user = $this->User->findByValidateKey('r',$validate_key);
		if (empty($user)) {
			return $this->render('reset_invalid');
		}
		
		if ($this->request->is('post')) {
			$this->request->data['User']['id'] = $user['User']['id'];
			$this->request->data['User']['validate_key'] = null;
			$this->request->data['User']['active'] = true;
			if ($this->User->save($this->request->data, true, array('password', 'validate_key', 'confirm_password', 'active'))) {
				return $this->render('reset_success');
			} else {
				$this->Session->setFlash(__('Ubable to save password.  Check below for errors.'), 'bs_error');
			}
		}
		
	}
	
/**
 * account method
 * 
 * Allows users to edit user account details.
 * 
 */
	public function account() {
		$id = $this->Auth->user('id');
		$this->User->id = $id;
		$this->User->recursive = -1;
		$user = $this->User->read();
		if ($this->request->is('post') || $this->request->is('put')) {
			if (empty($this->request->data['User']['password'])) {
				unset($this->request->data['User']['confirm_password']);
				unset($this->request->data['User']['password']);
			}
			if ($this->User->save($this->request->data)) {
				$user = $this->User->read();
				$this->Session->setFlash(__('Account changes saved'), 'bs_success');
			} else {
				$this->Session->setFlash(__('There was a problem saving your account.'), 'bs_error');
			}
		} else {
			$this->request->data = $user;
		}
		$this->request->data['User']['password'] = '';
		$this->request->data['User']['confirm_password'] = '';
		$this->set(compact('user'));
	}
	
/**
 * update_email method
 * 
 * Allows users to update email address on record.  Sends verification email.
 * 
 * @throws InternalErrorException
 * @return CakeResponse
 */
	public function update_email() {
		
		$id = $this->Auth->user('id');
		$this->User->id = $id;
		$this->User->recursive = -1;
		$user = $this->User->read();
		if ($this->request->is('post') || $this->request->is('put')) {
			unset($this->request->data['User']['id']);
			if ($this->request->data['User']['email'] == $user['User']['email']) {
				$this->User->invalidate('email', __('New email matches current email.'));
			} else {
				$this->User->set($this->request->data);
				if ($this->User->validates()) {
					$this->_throttleAction();
					$this->request->data['User']['validate_key'] = $this->User->createValidationKey('u');
					$this->request->data['User']['validate_data'] = $this->request->data['User']['email'];
					if ($this->User->save($this->request->data, false, array('validate_key', 'validate_data'))) {
						$this->User->enqueueEmail('UpdateEmail', $id);
						return $this->render('update_email_sent');
					} else {
						throw new InternalErrorException(__('Unable to update user record.'));
					}
				}
			}
			$this->Session->setFlash(__('Unable to update email.'), 'bs_error');
		}
	}
	
/**
 * update_email_verify method
 * 
 * Process return from update_email.  Sets email address after validation key is
 * successfully returned.
 * 
 * @param string $validate_key
 * @throws InternalErrorException
 * @return CakeResponse
 */
	public function update_email_verify($validate_key) {
		$user = $this->User->findByValidateKey('u', $validate_key);
		
		if (empty($user)) {
			return $this->render('reset_invalid');
		}
		
		$user['User']['email'] = $user['User']['validate_data'];
		$user['User']['validate_data'] = null;
		$user['User']['validate_key'] = null;
		
		if ($this->User->save($user, true, array('email', 'validate_data', 'validate_key'))) {
			return $this->render('verify_success');
		} else {
			throw new InternalErrorException(__('Unable to update email'));
		}
	}

/**
 * login method
 * 
 * Prcesses user logins
 */
	public function login() {
		$this->request->data['User']['password'] = '';
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				$this->User->id = $this->Auth->user('id');
				$this->User->saveField('last_login', date('Y-m-d H:i:s'));
				return $this->redirect(array('controller' => 'projects', 'action' => 'home'));
			} else {
				$this->Session->setFlash(__('Username or password is incorrect'),'bs_error');
			}
		}
	}	
	
/**
 * admin_index method
 * 
 * Main user index page. Admin only routing
 *
 * @return void
 */
	public function admin_index() {
		$this->User->recursive = 0;
		
		$paginate['conditons'] = $this->_processSearch();
		$this->paginate = $paginate;
		$this->set('users', $this->paginate());
	}

/**
 * admin_edit method
 *
 * Allows editing of user accounts by admins.
 *	
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->User->recursive = -1;
		$this->User->id = $id;
		$user = $this->User->read();
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->User->save($this->request->data, true, array('email', 'username', 'admin', 'active'))) {
				$this->Session->setFlash(__('The user has been saved'));
				$user = $this->User->read();
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $user;
		}
		$this->set(compact('user'));
	}

/**
 * admin_delete method
 * 
 * Allows admin to delete user account.  Requires POST.
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->User->delete()) {
			$this->Session->setFlash(__('User deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('User was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
	
/**
 * admin_invalidate_password
 * 
 * Allows admin to require password reset before a user is allowed to login.
 * 
 * @param string $id
 * @throws NotFoundException
 */
	public function admin_invalidate_password($id = null ) {
		$this->User->id = $id;
		$this->User->recursive = -1;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->request->onlyAllow('post', 'put');
		$user = $this->User->read();
		$user['User']['validate_key'] = $this->User->createValidationKey('r');
		$user['User']['active'] = false;
		if ($this->User->save($user, true, array('validate_key', 'active'))) {
			$this->Session->setFlash(__('Password Invalidated'), 'bs_success');
			$this->User->enqueueEmail('InvalidatePassword');
		} else {
			$this->Session->setFLash(__('Error invalidating password'));
		}
		return $this->redirect($this->referer());
	}

/**
 * admin_clear_validates method
 * 
 * Clear pending validation tokens. Requires POST|PUT
 * 
 * @param user_id $id
 * @throws NotFoundException
 */
	public function admin_clear_validates($id = null) {
		$this->request->onlyAllow('post', 'put');
		$this->User->id = $id;
		$this->User->recursive = -1;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$user = $this->User->read();
		$user['User']['validate_key'] = null;
		$user['User']['validate_data'] = null;
		if ($this->User->save($user, true, array('validate_key', 'validate_data'))) {
			$this->Session->setFlash(__('Cleared validation keys'), 'bs_success');
		} else {
			$this->Session->setFLash(__('Error clearing tokens.'));
		}
		return $this->redirect($this->referer());
		
	}
	
/**
 * logout method
 * 
 * Processes logout.
 */
	public function logout() {
		$this->Auth->logout();
		$this->redirect('/');
	}

}
