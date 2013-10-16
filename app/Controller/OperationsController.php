<?php
App::uses('AppController', 'Controller');
/**
 * Operations Controller
 *
 * @property Operation $Operation
 */
class OperationsController extends AppController {

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Operation->recursive = 0;
		$this->set('operations', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Operation->exists($id)) {
			throw new NotFoundException(__('Invalid operation'));
		}
		$options = array('conditions' => array('Operation.' . $this->Operation->primaryKey => $id));
		$this->set('operation', $this->Operation->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add($project_id = null) {
		//if ($this->Operation->Project->exists($project_id)) {
		//	throw new BadMethodCallException(__('Project not found..'));
		//}
		if ($this->request->is('post')) {
			$this->Operation->create();
			$this->request->data = array('Operation' => array('project_id' => $project_id));
			if ($this->Operation->save($this->request->data)) {
				$this->Session->setFlash(__('The operation has been saved'));
				$this->redirect(array('controller' => 'projects', 'action' => 'view', $project_id));
			} else {
				$this->Session->setFlash(__('The operation could not be saved. Please, try again.'));
			}
		} else {
			throw new MethodNotAllowedException();
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
		if (!$this->Operation->exists($id)) {
			throw new NotFoundException(__('Invalid operation'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Operation->save($this->request->data)) {
				$this->Session->setFlash(__('The operation has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The operation could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Operation.' . $this->Operation->primaryKey => $id));
			$this->request->data = $this->Operation->find('first', $options);
		}
		$projects = $this->Operation->Project->find('list');
		$this->set(compact('projects'));
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
		$this->Operation->id = $id;
		if (!$this->Operation->exists()) {
			throw new NotFoundException(__('Invalid operation'));
		}
		$this->Operation->recursive = -1;
		$op = $this->Operation->read();
		$this->request->onlyAllow('post', 'delete');
		if ($this->Operation->delete()) {
			$this->Session->setFlash(__('Operation deleted'));
			$this->redirect(array('controller' => 'projects', 'action' => 'view', $op['Operation']['project_id']));
		}
		$this->Session->setFlash(__('Operation was not deleted'));
		$this->redirect(array('controller' => 'projects', 'action' => 'view', $op['Operation']['project_id']));
	}

	public function preview($id = null) {
		$this->Operation->id = $id;
		if (!$this->Operation->exists()) {
			throw new NotFoundException();
		}
		if (!file_exists(PDF_PATH.DS.$id.'.gcode')) {
			throw new NotFoundException();
		}
	
		$this->layout = 'gcode_pre';
		$this->set('operation_id', $id);
	
	}
}
