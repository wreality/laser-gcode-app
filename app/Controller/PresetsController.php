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
		$this->set('presets', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Preset->exists($id)) {
			throw new NotFoundException(__('Invalid preset'));
		}
		$options = array('conditions' => array('Preset.' . $this->Preset->primaryKey => $id));
		$this->set('preset', $this->Preset->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Preset->create();
			if ($this->Preset->save($this->request->data)) {
				$this->Session->setFlash(__('The preset has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The preset could not be saved. Please, try again.'));
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
		if (!$this->Preset->exists($id)) {
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
 * delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Preset->id = $id;
		if (!$this->Preset->exists()) {
			throw new NotFoundException(__('Invalid preset'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->Preset->delete()) {
			$this->Session->setFlash(__('Preset deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Preset was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
