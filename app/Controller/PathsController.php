<?php
App::uses('AppController', 'Controller');
/**
 * Paths Controller
 *
 * @property Path $Path
 */
class PathsController extends AppController {

	public function beforeFilter() {
		$this->Auth->allow('add', 'delete', 'edit', 'move_up', 'move_down');
		parent::beforeFilter();
	}

/**
 * add method
 *
 * @return void
 */
	public function add($operation_id) {
		$this->Path->Operation->id = $operation_id;
		if (!$this->Path->Operation->exists()) {
			throw new NotFoundException(__('Invalid operation id.'));
		}
		if (!$this->Path->Operation->isOwner($this->Auth->user('id'))) {
			throw new ForbiddenException(__('Not authroized to modify this project.'));
		}
		
		if ($this->request->is('post')) {
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
		} 
		$this->set('presets', $this->Path->Preset->getList());
		$this->view = 'edit';
	}
 
/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->Path->id = $id;
		if (!$this->Path->exists()) {
			throw new NotFoundException(__('Invalid path'));
		}
		if (!$this->Path->isOwner($this->Auth->user('id'))) {
			throw new ForbiddenException(__('Not authorized to edit this project.'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Path->save($this->request->data)) {
				$this->Session->setFlash(__('The path has been saved'));
				$this->Path->Operation->id = $this->request->data['Path']['operation_id'];
				$this->Path->Operation->updateOverview();
				$this->redirect(array('controller' => 'projects', 'action' => 'edit',$this->Path->Operation->field('project_id')));
			} else {
				$this->Session->setFlash(__('The path could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Path.' . $this->Path->primaryKey => $id));
			$this->request->data = $this->Path->find('first', $options);
		}
		$this->set('presets', $this->Path->Preset->getList());
		
	}

/**
 * move_down method
 *
 * Move the supplied path down in the operation's path order.
 * 
 * @param Path $id
 * @throws NotFoundException
 * @throws ForbiddenException
 */
	public function move_down($id = null) {
		$this->request->onlyAllow('post', 'put');
		$this->Path->id = $id;
		if (!$this->Path->exists()) {
			throw new NotFoundException();
		}
		if (!$this->Path->isOwner($this->Auth->user('id'))) {
			throw new ForbiddenException(__('Not allowed to modify this project.'));
		}
		if ($this->Path->movePathDown($id)) {
			$this->Session->setFlash(__('Path order updated.'));
		} else {
			$this->Session->setFlash(__('Problem updating path order'));
		}
		$this->redirect($this->referer());
	
	}

/**
 * move_up method
 *
 * Move the supplied path up in the operation's order.
 * 
 * @param Path $id
 * @throws NotFoundException
 * @throws ForbiddenException
 */
	public function move_up($id = null) {
		$this->request->onlyAllow('post', 'put');
		$this->Path->id = $id;
		if (!$this->Path->exists()) {
			throw new NotFoundException();
		}
		if (!$this->Path->isOwner($this->Auth->user('id'))) {
			throw new ForbiddenException(__('Not authorized to modify this project.'));
		}
		if ($this->Path->movePathUp($id)) {
			$this->Session->setFlash(__('Path order updated.'), 'bs_success');
		} else {
			$this->Session->setFlash(__('Problem updating path order'), 'bs_error');
		}
		$this->redirect($this->referer());
		
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
		$this->Path->id = $id;
		if (!$this->Path->exists()) {
			throw new NotFoundException(__('Invalid path'));
		}
		if (!$this->Path->isOwner($this->Auth->user('id'))) {
			throw new ForbiddenException(__('Not authorized to modify this project.'));
		}
		$operation_id = $this->Path->field('operation_id');	
		if ($this->Path->delete()) {
			$this->Path->Operation->updateOverview($operation_id);
			$this->Session->setFlash(__('Path deleted'), 'bs_success');
			$this->redirect($this->referer());
		}
		$this->Session->setFlash(__('Path was not deleted'));
		$this->redirect($this->referer());
	}
}
