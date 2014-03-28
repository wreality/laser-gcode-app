<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. 
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');

//Class is included for the benefit of the user constants used in authentication
App::uses('User', 'Model');

/**
 * Application Controller
 *
 * @package       app.Controller
 */
class AppController extends Controller {

	public $components = array(
		'Auth' => array(
			'loginAction' => array(
				'controller' => 'users',
				'action' => 'login',
				'admin' => false,
			),
			'authorize' => 'Controller',
			'authenticate' => array(
				'Form' => array(
					'passwordHasher' => 'Blowfish',
					'fields' => array('username' => 'email', 'password' => 'password'),
					'scope' => array('User.active' => User::USER_ACTIVE),
				),
			),
		),
		'Cakestrap.Cakestrap', 'Session');

	public $helpers = array('Html', 'Form', 'Session', 'Paginator', 'Time', 'Projects');

	public $uses = array('Setting');

/**
 * beforeFilter method
 * 
 * Load DB stored settings
 * 
 * (non-PHPdoc)
 * @see Controller::beforeFilter()
 */
	public function beforeFilter() {
		$this->Setting->getSettings();
		parent::beforeFilter();
	}

/**
 * _throttleAction method
 * 
 * Uses caching to error out multiple requests to email sending actions to 
 * prevent abuse.
 * 
 * @throws BadRequestException
 */
	protected function _throttleAction() {
		$cacheKey = 'email_sending_' . $this->request->clientIp();

		$count = Cache::read($cacheKey);

		if ($count === false) {
			$count = 1;
		} else {
			$count++;
		}
		Cache::write($cacheKey, $count);
		if ($count >= 25) {
			throw new BadRequestException(__('To prevent abuse this request has been throttled.  Try again later.'));
		}
	}

/**
 * _processSearch method
 * 
 * Process request data and return a conditions array ready for find/paginate.
 * 
 * @return Ambigous <mixed, boolean, NULL, unknown, multitype:>|multitype:
 */
	protected function _processSearch() {
		$sessionKey = $this->name . '.' . $this->action . '.';
		if ($this->request->is('post')) {
			$paginate = array(
				'conditions' => array()
			);
			foreach ($this->request->data as $model => $fields) {
				foreach ($fields as $field => $value) {
					if (!empty($value)) {
						if (stristr($value, '*')) {
							$paginate['conditions'][$model . '.' . $field . ' LIKE'] = str_replace('*', '%', $value);
						} elseif (preg_match('/"([^"]+)"/', $value, $matches)) {
							$paginate['conditions'][$model . '.' . $field] = $matches[1];
						} elseif (preg_match("/([^']+)'/", $value, $matches)) {
							$paginate['conditions'][$model . '.' . $field] = $matches[1];
						} else {
							$paginate['conditions'][$model . '.' . $field . ' LIKE'] = '%' . $value . '%';
							$paginate['conditions'][$model . '.' . $field . ' !='] = null;
						}
					}
				}
			}
			$paginate['page'] = 1;
			$this->Session->write($sessionKey . 'conditions', $paginate['conditions']);
			$this->Session->write($sessionKey . 'data', $this->request->data);
		} else {
			$paginate['conditions'] = $this->Session->read($sessionKey . 'conditions');
			$this->request->data = $this->Session->read($sessionKey . 'data');
		}
		if (!empty($paginate)) {
			return $paginate;
		} else {
			$this->request->data = array();
			return array();
		}
	}

/**
 * isAuthorized method
 * 
 * Check admin routes and deny access to non-admins
 * 
 * @param array $user
 * @return boolean
 */
	public function isAuthorized($user) {
		//Check for admin routes.
		if (!empty($this->request->params['admin'])) {
			if ($user['admin']) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}

/**
 * _onlyGuest method
 * 
 * Redirect to the force_logout action if a request is coming from a logged in
 * user.
 *
 */
	protected function _requireGuest() {
		if ($this->Auth->loggedIn()) {
			$this->Session->write('force_logout_url', array('controller' => strtolower($this->name), 'action' => $this->action));
			return $this->redirect(array('controller' => 'users', 'action' => 'force_logout'));
		} else {
			return false;
		}
	}
}
