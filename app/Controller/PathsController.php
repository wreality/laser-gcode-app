<?php
App::uses('AppController', 'Controller');
/**
 * Paths Controller
 *
 * @property Path $Path
 */
class PathsController extends AppController {

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Path->recursive = 0;
		$this->set('paths', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Path->exists($id)) {
			throw new NotFoundException(__('Invalid path'));
		}
		$options = array('conditions' => array('Path.' . $this->Path->primaryKey => $id));
		$this->set('path', $this->Path->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add($operation_id) {
		if ($this->request->is('post')) {
			if (!$this->Path->Operation->exists($operation_id)) {
				throw new BadRequestException('Operation does not exist');
			}
			
			$this->Path->create();
			$this->request->data['Path']['operation_id'] = $operation_id;
			
			if ($this->Path->save($this->request->data)) {
				
				$this->Path->Operation->updateOverview($operation_id);
				$this->Session->setFlash(__('The path has been saved'));
				$this->redirect($this->referer());
			} else {
				$this->Session->setFlash(__('The path could not be saved. Please, try again.'));
			//	$this->redirect($this->referer());
			}
		} else {
			throw new BadMethodCallException();
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
		if (!$this->Path->exists($id)) {
			throw new NotFoundException(__('Invalid path'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Path->save($this->request->data)) {
				$this->Session->setFlash(__('The path has been saved'));
				$this->Path->Operation->id = $this->request->data['Path']['operation_id'];
				
				$this->redirect(array('controller' => 'projects', 'action' => 'view',$this->Path->Operation->field('project_id')));
			} else {
				$this->Session->setFlash(__('The path could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Path.' . $this->Path->primaryKey => $id));
			$this->request->data = $this->Path->find('first', $options);
		}
		
	}

	public function move_down($id = null) {
		$this->Path->id = $id;
		if (!$this->Path->exists()) {
			throw new NotFoundException();
		}
		if (!($this->request->is('post') || $this->request->is('put'))) {
			throw new MethodNotAllowedException();
		}
		$path = $this->Path->read();
		$exchange_path = $this->Path->find('first', array(
			'conditions' => array(
				'order' => $path['Path']['order']+1,
				'operation_id' => $path['Path']['operation_id'],
			)
		));
		if (!$exchange_path) {
			$this->Session->setFlash(__('Can\'t move path.'));
			$this->redirect($this->referer());
		} else {
			$exchange_path['Path']['order']--;
			$path['Path']['order']++;
			if ($this->Path->save($exchange_path) && $this->Path->save($path)) {
				$this->Session->setFlash(__('Path order updated.'));
			} else {
				$this->Session->setFlash(__('Problem updating path order'));
			}
			$this->redirect($this->referer());
		}
	}
	
	public function move_up($id = null) {
		$this->Path->id = $id;
		if (!$this->Path->exists()) {
			throw new NotFoundException();
		}
		if (!($this->request->is('post') || $this->request->is('put'))) {
			throw new MethodNotAllowedException();
		}
		$path = $this->Path->read();
		$exchange_path = $this->Path->find('first', array(
			'conditions' => array(
				'order' => $path['Path']['order']-1,
				'operation_id' => $path['Path']['operation_id'],
			)
		));
		if (!$exchange_path) {
			$this->Session->setFlash(__('Can\'t move path.'));
			$this->redirect($this->referer());
		} else {
			$exchange_path['Path']['order']++;
			$path['Path']['order']--;
			if ($this->Path->save($exchange_path) && $this->Path->save($path)) {
				$this->Session->setFlash(__('Path order updated.'));
			} else {
				$this->Session->setFlash(__('Problem updating path order'));
			}
			$this->redirect($this->referer());
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
		$this->Path->id = $id;
		if (!$this->Path->exists()) {
			throw new NotFoundException(__('Invalid path'));
		}
		$this->request->onlyAllow('post', 'delete');
		$operation_id = $this->Path->field('operation_id');	
		if ($this->Path->delete()) {
			$this->Path->Operation->updateOverview($operation_id);
			$this->Session->setFlash(__('Path deleted'));
			$this->redirect($this->referer());
		}
		$this->Session->setFlash(__('Path was not deleted'));
		$this->redirect($this->referer());
	}
}
