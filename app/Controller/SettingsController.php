<?php
App::uses('AppController', 'Controller');
/**
 * Settings Controller
 *
 * @property Setting $Setting
 */
class SettingsController extends AppController {
	
/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->Setting->recursive = 0;
		$settings = Cache::read('settings');
		if ($settings === false) {
			$settings = $this->Setting->find('all');
			Cache::write('settings', $settings);
		}
		$this->set('settings', $settings);
		$this->request->data = array();
	}

/**
 * admin_view method
 *
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->Setting->id = $id;
		if (!$this->Setting->exists()) {
			throw new NotFoundException(__('Invalid setting'));
		}
		$this->set('setting', $this->Setting->read(null, $id));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->Setting->create();
			if ($this->Setting->save($this->request->data)) {
				$this->Session->setFlash(__('The setting has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The setting could not be saved. Please, try again.'));
			}
		}
	}

/**
 * admin_edit method
 *
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		$this->Setting->id = $id;
		
		if (!$this->Setting->exists()) {
			throw new NotFoundException(__('Invalid setting'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if (!empty($this->request->data['Setting']['enum_safe'])) {
				$options_lines = explode(';', $this->request->data['Setting']['enum_safe']);
				foreach($options_lines as $option_line) {
					$temp = explode(',', $option_line);
					$options[$temp[0]] = $temp[1];
				}
				$this->request->data['Setting']['enum_data'] = serialize($options);
					
			}
			if (!empty($this->request->data['Setting']['validate'])) {
				
				$this->Setting->validate = array(
					'value' => array(
						'rule' => $this->request->data['Setting']['validate'],
						'allowEmpty' => true,
						'required' => false,
						'message' => __('Invalid setting value.'),
					),
				);
			}
			if (Configure::read('debug') != 3) {
				$fields = array('value', 'key', 'enum_data', 'validate', 'help_text', 'title');
			} else {
				$field = null;
			}
			if ($this->Setting->save($this->request->data, $fields)) {
				$this->Session->setFlash(__('The setting has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The setting could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Setting->read(null, $id);
			if (empty($this->request->data['Setting']['value'])) {
				$this->request->data['Setting']['value'] = Configure::read($this->request->data['Setting']['key']);
			}
			if (!empty($this->request->data['Setting']['enum_data'])) {
				$options = unserialize($this->request->data['Setting']['enum_data']);
				foreach($options as $key => $option) {
					$comma_sep[] = $key.','.$option;
				}
				$this->request->data['Setting']['enum_safe'] = implode(';', $comma_sep);
			}
		}
	}

/**
 * admin_update method
 * 
 * Update settings by extracting from model.
 */	
	public function admin_update() {
		$this->Setting->updateSettings();
		$this->Session->setFlash(__('Settings updated'), 'bs_success');
		$this->redirect(array('action' => 'index'));
	}
	
/**
 * admin_import method
 * 
 * Import settings from currently configured values.
 * 
 * @throws MethodNotAllowedException
 */	
	public function admin_import() {
		if (!($this->request->is('post') || ($this->request->is('put')))) {
			throw new MethodNotAllowedException('POST or PUT required for this method.');
		}
		
		$key = $this->Setting->findByKey($this->request->data['Setting']['key']);
		if (!$key) {
			$this->Setting->create();
			$this->request->data['Setting']['value'] = Configure::read($this->request->data['Setting']['key']);
			if ($this->Setting->save($this->request->data)) {
				$this->Session->setFlash(__('New key imported.'), 'bs_success');
				$this->redirect(array('action' => 'edit', $this->request->data['Setting']['key']));
			} else {
				$this->Session->setFlash(__('Unable to create new key'), 'bs_error');
				$this->redirect(array('action' => 'index'));
			}
		} else {
			$this->Session->setFlash(__('Key already exists.'), 'bs_default');
			$this->redirect(array('action' => 'edit', $key['Setting']['key']));
			
		}
	}
	
/**
 * admin_delete method
 *
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Setting->id = $id;
		if (!$this->Setting->exists()) {
			throw new NotFoundException(__('Invalid setting'));
		}
		if ($this->Setting->delete()) {
			$this->Session->setFlash(__('Setting deleted'));
			Cache::delete('settings');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Setting was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
