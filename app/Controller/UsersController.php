<?php
App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 */
class UsersController extends AppController {
	
	public function beforeFilter() {
		$this->Auth->allow('register', 'verify');
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
 * add method
 *
 * @return void
 */
	public function register() {
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

	public function verify($validate_key) {
		$user = $this->User->findByValidateKey($validate_key);
		
		if (!$user) {
			return $this->render('verify_error');
		} else {
			$user['User']['active'] = true;
			$user['User']['validate_key'] = null;
			if ($this->User->save($user)) {
				return $this->render('verify_succes');	
			} else {
				var_dump($this->User->validationErrors);
				return $this->render('verify_error');
			}
		}
	}
	
	public function lost_password() {
		if ($this->request->is('post')) {
			$user = $this->findByEmail($this->request->data['User']['email']);
			if (empty($user)) {
				$this->User->enqueueEmail('ResetNotFound');
			} else {
				$user['User']['validate_key'] = $this->User->createValidationKey();
				if (!$this->User->save($user)) {
					throw new InternalErrorException(__('Unable to save validation key.'));
				}
				$this->User->enqueueEmail('ResetPassword');
			}
			return $this->render('reset_sent');
		}
	}
	
	public function reset($validate_key) {
		$user = $this->findByValidateKey($validate_key);
		if (empty($user)) {
			return $this->render('reset_invalid');
		}
		
		if ($this->request->is('post')) {
			$this->request->data['User']['validate_key'] = null;
			if ($this->request->save($this->request->data)) {
				return $this->render('reset_success');
			} else {
				$this->Session->setFlash(__('Ubable to save password.  Check below for errors.'), 'bs_error');
			}
		}
		
	}
	
	
/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'), 'bs_error');
			}
		} else {
			$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
			$this->request->data = $this->User->find('first', $options);
		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
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

	
	public function login() {
		if ($this->request->is('POST')) {
			$this->Auth->login($this->request->data);
		}
	}
/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
		$this->set('user', $this->User->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		}
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
			$this->request->data = $this->User->find('first', $options);
		}
	}

/**
 * admin_delete method
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
	
	public function logout() {
		$this->redirect($this->Auth->logout());
	}
}
