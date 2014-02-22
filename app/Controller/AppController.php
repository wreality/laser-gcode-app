<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	
	public $components = array(
		'Auth' => array(
			'authenticate' => array(
				'Form' => array(
					'passwordHasher' => 'Blowfish',
					'fields' => array('username' => 'email', 'password' => 'password'),
				),
			),
		),
		'Cakestrap.Cakestrap','Session');
	public $helpers = array('Html', 'Form', 'Session', 'Paginator', 'Time', 'Projects');
	public $uses = array('Setting');
	
	public function beforeFilter() {
		
		$this->Setting->getSettings();
		
		$this->request->addDetector('internalIp', array(
			'env' => 'REMOTE_ADDR',
			'pattern' => '/^(192\.168\.0\.|192\.168\.1\.|127\.0\.0\.1|::1)/',
		));
		parent::beforeFilter();
	}

	protected function _throttleAction() {
		$cache_key = 'email_sending_'.$this->request->clientIp();
	
		$count = Cache::read($cache_key);
	
		if ($count === false) {
			$count = 1;
		} else {
			$count++;
		}
		Cache::write($cache_key, $count);
		if ($count >= 140) {
			throw new BadRequestException(__('To prevent abuse this request has been throttled.  Try again later.'));
		}
	}

	protected function _processSearch() {
		$session_key = $this->name.'.'.$this->action.'.';
		if ($this->request->is('post')) {
			$conditions = array();
			foreach($this->request->data as $model => $fields) {
				foreach ($fields as $field => $value) {
					if (!empty($value)) {
						$conditions[$model.'.'.$field.' LIKE'] = '%'.$value.'%';
						$conditions[$model.'.'.$field.' !='] = null;
					}
				}
			}
			$this->Session->write($session_key.'conditions', $conditions);
			$this->Session->write($session_key.'data', $this->request->data);
		} else {
			$conditions = $this->Session->read($session_key.'conditions');
			$this->request->data = $this->Session->read($session_key.'data');
		}
		if (!empty($conditions)) {
			return $conditions;
		} else {
			$this->request->data = array();
			return array();
		}
	}
}
