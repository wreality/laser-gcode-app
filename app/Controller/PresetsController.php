<?php
App::uses('AppController', 'Controller');
/**
 * Presets Controller
 *
 * @property Preset $Preset
 */
class PresetsController extends AppController {

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Preset->recursive = 0;
		$paginate = array(
			'conditions' => array(
				'Preset.user_id' => $this->Auth->user('id'),
			)
		);
		$paginate['conditions'] = array_merge($this->_processSearch(), $paginate['conditions']);
		$this->paginate = $paginate;
		$this->set('presets', $this->paginate());
	}
	
/**
 * admin_index method
 *
 * Display all presets.
 *
 */
	public function admin_index() {
		$this->Preset->recursive = 0;
		$paginate['conditions'] = $this->_processSearch();
		$this->paginate = $paginate;
		$this->set('presets', $this->paginate());
	}
	
/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Preset->create();
			$this->request->data['Preset']['user_id'] = $this->Auth->user('id');
			if ($this->Preset->save($this->request->data)) {
				$this->Session->setFlash(__('The preset has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The preset could not be saved. Please, try again.'));
			}
		}
	}
	
/**
 * admin_add method
 *
 * Allow admins to create a global preset.
 *
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->Preset->create();
			$this->request->data['Preset']['user_id'] = null;
			if ($this->Preset->save($this->request->data)) {
				$this->Session->setFlash(__('Preset saved.'), 'bs_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('Preset could not be saved.  Please, try again.'), 'bs_error');
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
		$this->Preset->id = $id;
		if (!$this->Preset->exists()) {
			throw new NotFoundException(__('Invalid preset'));
		}
		if (!$this->Preset->isOwner($this->Auth->user('id'))) {
			throw new ForbiddenException(__('Not authorized to modify this preset.'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Preset->save($this->request->data)) {
				$this->Session->setFlash(__('The preset has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The preset could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Preset.' . $this->Preset->primaryKey => $id));
			$this->request->data = $this->Preset->find('first', $options);
		}
	}

/**
 * admin_edit method
 *
 * Allow admins to edit any preset.
 * 
 * @param string $id
 * @throws NotFoundException
 */
	public function admin_edit($id = null) {
		$this->Preset->id = $id;
		if (!$this->Preset->exists()) {
			throw new NotFoundException(__('Invalid preset'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Preset->save($this->request->data)) {
				$this->Session->setFlash(__('The preset has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The preset could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Preset.' . $this->Preset->primaryKey => $id));
			$this->request->data = $this->Preset->find('first', $options);
		}
	}
	
/**
 * admin_promote method
 *
 * Promote a user's preset to be a global preset.
 * 
 * @param string $id
 * @throws NotFoundException
 */
	public function admin_promote($id = null) {
		$this->request->onlyAllow('post', 'put');
		$this->Preset->id = $id;
		if (!$this->Preset->exists()) {
			throw new NotFoundException(__('Invalid preset.'));
		}
		$preset = $this->Preset->read();
		
		if ($preset['Preset']['isGlobal']) {
			$this->Session->setFlash(__('Preset is already global'), 'bs_warning');
		} else if ($this->Preset->makeGlobal()) {
			$this->Session->setFlash(__('Preset promoted to global'), 'bs_success');
		} else {
			$this->Session->setFlash(__('Unable to promote preset.'), 'bs_error');
		}
		return $this->redirect($this->referer());
		
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
		$this->request->onlyAllow('post', 'delete');
		$this->Preset->id = $id;
		if (!$this->Preset->exists()) {
			throw new NotFoundException(__('Invalid preset'));
		}
		if (!$this->Preset->isOwner($this->Auth->user('id'))) {
			throw new ForbiddenException(__('Not authorized to modify this preset.'));
		}
		if ($this->Preset->delete()) {
			$this->Session->setFlash(__('Preset deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Preset was not deleted'));
		$this->redirect(array('action' => 'index'));
	}

/**
 * admin_delete method
 *
 * Allow admins to delete any preset.
 * 
 * @param string $id
 * @throws NotFoundException
 */
	public function admin_delete ($id = null) {
		$this->request->onlyAllow('post', 'delete');
		$this->Preset->id = $id;
		if (!$this->Preset->exists()) {
			throw new NotFoundException(__('Invalid preset'));
		}
		if ($this->Preset->delete()) {
			$this->Session->setFlash(__('Preset deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Preset was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
	
/**
 * import method
 *
 * Import settings used on a path and use them to create a preset.  
 * 
 * @param Path $path_id
 * @throws ForbiddedException
 * @throws InternalErrorException
 */
	public function import($path_id = null) {
		$this->loadModel('Path');
		$this->Path->id = $path_id;
		if (!$this->Path->exists()) {
			throw new InternalErrorException();
		}
		if (!empty($this->request->named['global'])) {
			if (!$this->Auth->user('admin')) {
				throw new ForbiddedException(__('Only admins can create global presets.'));
			} else {
				$user_id = null;
			}
		} else {
			$user_id = $this->Auth->user('id');
			if (!$this->Path->isOwner($user_id)) {
				throw new ForbiddenException(__('Unable to modify path settings.'));
			}
		}
		$this->Path->Behaviors->attach('Containable');
		$this->Path->contain(array(
			'Operation' => array(
				'Project'
			)
		));
		$path = $this->Path->read();
		
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->Preset->create();
			$this->request->data['Preset']['user_id'] = $user_id;
			if ($this->Preset->save($this->request->data)) {
				$this->Path->saveField('preset_id', $this->Preset->id);
				$this->Session->setFlash(__('Saved new preset.'));
				$this->redirect(array('controller' => 'projects', 'action' => 'edit', $path['Operation']['Project']['id']));
			} else {
				$this->Session->setFlash(__('Unable to save preset..'), 'bs_error');
			}
		} else {
			$this->request->data['Preset']['power'] = $path['Path']['power'];
			$this->request->data['Preset']['speed'] = $path['Path']['speed'];
		}
		$this->view = 'add';
	}
}
