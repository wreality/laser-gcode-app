<?php
App::uses('AppController', 'Controller');
/**
 * Operations Controller
 *
 * @property Operation $Operation
 */
class OperationsController extends AppController {

/**
 * beforeFilter method
 *
 * Allow access to operation functions for anonymous projects
 *
 * (non-PHPdoc)
 * @see Controller::beforeFilter()
 */
	public function beforeFilter() {
		$this->Auth->allow('add', 'preview', 'download', 'view', 'delete', 'copy');
		parent::beforeFilter();
	}

/**
 * add method
 *
 * @param string $projectId
 * @throws NotFoundException
 * @throws ForbiddenException
 */
	public function add($projectId = null) {
		$this->Operation->Project->id = $projectId;
		if (!$this->Operation->Project->exists()) {
			throw new NotFoundException(__('Invalid project id.'));
		}
		if (!$this->Operation->Project->isOwner($this->Auth->user('id'))) {
			throw new ForbiddenException(__('Not authorized to modify this project.'));
		}
		$this->request->onlyAllow('post', 'put');

		$this->Operation->create();
		$this->request->data = array('Operation' => array('project_id' => $projectId));
		if ($this->Operation->save($this->request->data)) {
			$this->Session->setFlash(__('The operation has been saved'), 'bs_success');
			$this->redirect(array('controller' => 'projects', 'action' => 'edit', $projectId));
		} else {
			$this->Session->setFlash(__('The operation could not be saved. Please, try again.'), 'bs_error');
		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @throws ForbiddenException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Operation->id = $id;
		if (!$this->Operation->exists()) {
			throw new NotFoundException(__('Invalid operation'));
		}
		if (!$this->Operation->isOwner($this->Auth->user('id'))) {
			throw new ForbiddenException(__('Not authorized to modify this project.'));
		}
		$this->Operation->recursive = -1;
		$op = $this->Operation->read();
		$this->request->onlyAllow('post', 'delete');
		if ($this->Operation->delete()) {
			$this->Session->setFlash(__('Operation deleted'), 'bs_success');
			return $this->redirect(array('controller' => 'projects', 'action' => 'view', $op['Operation']['project_id']));
		}
		$this->Session->setFlash(__('Operation was not deleted'), 'bs_error');
		return $this->redirect(array('controller' => 'projects', 'action' => 'view', $op['Operation']['project_id']));
	}

/**
 * preview method
 *
 * Load Gcode previewer.
 *
 * @param Operation $id
 * @throws NotFoundException
 * @throws ForbiddenException
 */
	public function preview($id = null) {
		$this->Operation->id = $id;
		if (!$this->Operation->exists()) {
			throw new NotFoundException();
		}
		if (!$this->Operation->isOwnerOrPublic($this->Auth->user('id'))) {
			throw new ForbiddenException(__('Not authorized to access this project'));
		}
		if (!file_exists(Configure::read('LaserApp.storage_path') . DS . $id . '.gcode')) {
			throw new NotFoundException();
		}

		$this->layout = 'gcode_pre';
		$this->set('operation_id', $id);
	}

/**
 * download method
 *
 * Start file download of gcode file.
 *
 * @param string $id
 * @throws NotFoundException
 * @throws ForbiddenException
 * @return CakeResponse
 */
	public function download($id = null) {
		$this->Operation->id = $id;

		if (!$this->Operation->exists()) {
			throw new NotFoundException();
		}
		if (!$this->Operation->isOwnerOrPublic($this->Auth->user('id'))) {
			throw new ForbiddenException();
		}
		if (!$this->Operation->gCodeExists()) {
			throw new NotFoundException();
		}

		$this->Operation->contain(array('Project'));
		$operation = $this->Operation->read();
		if (!empty($operation['Project']['project_name'])) {
			$name = str_replace(' ', '_', $operation['Project']['project_name']) . '_OP' . $operation['Operation']['order'] . '.gcode';
		} else {
			$name = 'OP' . $operation['Operation']['order'] . '.gcode';
		}

		$modified = new DateTime();
		$modified->setTimestamp(filemtime(Configure::read('LaserApp.storage_path') . DS . $id . '.gcode'));

		$this->response->modified($modified); // Allow for caching only when gocde hasn't been updated
		if ($this->response->checkNotModified($this->request)) {
			return $this->response;
		}

		//$this->response->file(Configure::read('LaserApp.storage_path') . DS . $id . '.gcode', array('download' => true, 'name' => $name));
		return $this->response;
	}

/**
 * copy method
 *
 * @param string $id
 * @throws NotFoundException
 * @throws ForbiddenException
 */
	public function copy($id = null) {
		$this->Operation->id = $id;
		if (!$this->Operation->exists()) {
			throw new NotFoundException();
		}
		if (!$this->Operation->isOwner($this->Auth->user('id'))) {
			throw new ForbiddenException();
		}
		$this->request->onlyAllow('post', 'put');
		if ($this->Operation->copyOperation()) {
			$this->Operation->updateOverview();
			$this->Session->setFlash(__('Sucessfully copied operation.'), 'bs_success');
		} else {
			$this->Session->setFlash(__('Unable to copy operation.'), 'bs_error');
		}
		$this->redirect($this->request->referer());
	}
}
