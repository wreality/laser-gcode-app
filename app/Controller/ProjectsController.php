<?php
App::uses('AppController', 'Controller');
/**
 * Projects Controller
 *
 * @property Project $Project
 */
class ProjectsController extends AppController {

/**
 * beforeFilter method
 *
 * Allow access to public actions.
 *
 * (non-PHPdoc)
 * @see Controller::beforeFilter()
 */
	public function beforeFilter() {
		$this->Auth->allow('index', 'add', 'view', 'edit', 'generate', 'delete', 'reset_project_defaults');
		parent::beforeFilter();
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Project->recursive = 1;
		$paginate = array(
			'order' => array('Project.modified' => 'DESC'),
			'conditions' => array(
				'public' => Project::PROJ_PUBLIC
			)
		);
		$paginate = array_merge_recursive($this->_processSearch(), $paginate);
		$this->paginate = $paginate;
		$this->set('projects', $this->paginate());
	}

/**
 * home method
 *
 * Display user's own projects
 */
	public function home() {
		$this->Project->recursive = 1;
		$paginate = array(
				'order' => array('Project.modified' => 'DESC'),
				'conditions' => array(
						'user_id' => $this->Auth->user('id'),
						'public' => array(Project::PROJ_PUBLIC, Project::PROJ_PRIVATE)
				)
		);
		$paginate = array_merge_recursive($this->_processSearch(), $paginate);
		$this->paginate = $paginate;
		$this->set('projects', $this->paginate());
	}

/**
 * claim method
 *
 * Display list of unclaimed projects
 */
	public function claim() {
		$this->Project->recursive = 1;
		$paginate = array(
				'order' => array('Project.modified' => 'DESC'),
				'conditions' => array(
						'User.id' => null,
						'Project.operation_count >' => 0
				)
		);
		$paginate = array_merge_recursive($this->_processSearch(), $paginate);
		$this->paginate = $paginate;

		$this->set('projects', $this->paginate());
	}

/**
 * make_claim method
 *
 * Process user's claim on a project
 *
 * @param project_id $id
 * @throws NotFoundException
 */
	public function make_claim($id) {
		$this->request->onlyAllow('post', 'put');
		$this->Project->id = $id;

		if (!$this->Project->exists()) {
			throw new NotFoundException();
		}
		$project = $this->Project->read();

		if (!empty($project['User']['id'])) {
			$this->Session->setFlash(__('This project is already claimed'), 'bs_error');
		} else {

			$project['Project']['user_id'] = $this->Auth->user('id');
			$project['Project']['public'] = Project::PROJ_PRIVATE;

			if ($this->Project->save($project, true, array('user_id', 'public'))) {
				$this->Session->setFlash(__('You have successfully claimed this project.'), 'bs_success');
				return $this->redirect(array('action' => 'edit', $id));
			} else {
				$this->Session->setFlash(__('There was an error while claiming your project.'), 'bs_error');
			}
		}
		return $this->redirect($this->referer());
	}

/**
 * edit method
 *
 * @param string $id
 * @throws NotFoundException
 * @throws ForbiddenException
 */
	public function edit($id = null) {
		$this->Project->id = $id;
		if (!$this->Project->exists($id)) {
			throw new NotFoundException(__('Invalid project'));
		}
		if (!$this->Project->isOwner($this->Auth->user('id'))) {
			throw new ForbiddenException(__('Not allowed to modify this project.'));
		}

		$this->Project->contain(array(
			'Operation' => array(
				'Path' => array(
					'Preset',
					'order' => array('order' => 'ASC'),
				)
			), 'User'
		));
		$project = $this->Project->read();

		if ($this->request->is('post') || $this->request->is('put')) {
			$fields = array('project_name', 'max_feedrate', 'home_before',
					'clear_after', 'gcode_postscript', 'gcode_preamble',
					'traversal_rate'
			);
			if (!$project['Project']['isAnonymous']) {
				$fields[] = 'public';
			}
			if ($this->Project->save($this->request->data, true, $fields)) {
				$this->Session->setFlash(__('Project settings saved.'), 'bs_success');
			} else {
				$this->Session->setFlash(__('There was an error trying to save your project settings.'), 'bs_error');
			}
		} else {
			$this->request->data = $project;
		}

		$this->set('public_options', Project::$statuses);

		$this->loadModel('Preset');
		$this->set('presets', $this->Preset->getList($this->Auth->user('id')));

		$this->set('project', $project);
	}

/**
 * generate method
 *
 * Generate gcode for a project
 *
 * @param unknown $id
 * @throws NotFoundException
 * @throws ForbiddenException
 */
	public function generate($id) {
		$this->request->onlyAllow('post', 'put');
		$this->Project->id = $id;
		if (!$this->Project->exists()) {
			throw new NotFoundException(__('Invalid project id.'));
		}
		if (!$this->Project->isOwner($this->Auth->user('id'))) {
			throw new ForbiddenException(__('Not authorized to modify this project.'));
		}

		$this->Project->contain(array('Operation'));
		$project = $this->Project->read();

		foreach ($project['Operation'] as $operation) {
			$this->Project->Operation->generateGcode($operation['id']);
		}
		$this->redirect(array('action' => 'edit', $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		$this->request->onlyAllow('post', 'put');
		$this->Project->create();
		$this->request->data['Project']['public'] = Project::PROJ_PRIVATE;
		if ($this->Auth->user('id')) {
			$this->request->data['Project']['user_id'] = $this->Auth->user('id');
		} else {
			$this->request->data['Project']['user_id'] = $this->request->clientIp();
		}
		$this->request->data['Project']['project_name'] = '';
		if ($this->Project->save($this->request->data)) {
			$this->redirect(array('action' => 'edit', $this->Project->id));
		} else {
			$this->Session->setFlash(__('The project could not be saved. Please, try again.'));
		}
	}

/**
 * view method
 *
 * @param string $id
 * @throws NotFoundException
 * @throws ForbiddenException
 */
	public function view($id = null) {
		$this->Project->id = $id;
		if (!$this->Project->exists()) {
			throw new NotFoundException(__('Invalid project'));
		}
		if (!$this->Project->isOwnerOrPublic($this->Auth->user('id'))) {
			throw new ForbiddenException(__('Not authorized to access this project.'));
		}
		$this->Project->contain(array(
			'Operation' => array(
				'Path' => array(
					'Preset',
					'order' => array('order' => 'ASC'),
				)
			), 'User'
		));
		$project = $this->Project->read();

		$this->set(compact('project'));
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
		$this->Project->id = $id;
		if (!$this->Project->exists()) {
			throw new NotFoundException(__('Invalid project'));
		}
		if (!$this->Project->isOwner($this->Auth->user('id'))) {
			throw new ForbiddenException(__('Not authorized to edit this project.'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->Project->delete()) {
			$this->Session->setFlash(__('Project deleted'));
			if (!$this->Auth->user('id')) {
				return $this->redirect(array('action' => 'index'));
			} else {
				return $this->redirect(array('action' => 'home'));
			}
		}
		$this->Session->setFlash(__('Project was not deleted'));
		$this->redirect($this->referer());
	}

/**
 * copy method
 * 
 * @param string $id
 * @throws NotFoundException
 * @throws ForbiddenException
 */
	public function copy($id = null) {
		$this->Project->id = $id;
		if (!$this->Project->exists()) {
			throw new NotFoundException();
		}
		if (!$this->Project->isOwner($this->Auth->user('id'), true)) {
			throw new ForbiddenException();
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Project->copyProject($this->request->data['Project']['project_name'])) {
				$this->Session->setFlash(__('Project successfully copied'), 'bs_success');
				$this->Project->updateOverviews();
				$this->redirect(array('action' => 'edit', $this->Project->id));
			} else {
				$this->Session->setFlash(__('Enable to copy projecgt.'), 'bs_error');
			}
		} else {
			$this->Project->contain(array());
			$this->request->data = $this->Project->read();
		}
	}

/**
 * admin_index method
 *
 * Display full list of system projects.
 */
	public function admin_index() {
		$paginate = $this->_processSearch();
		$paginate['contain'] = array(
			'Operation', 'User'
		);
		$paginate['conditions']['Project.public'] = array(Project::PROJ_PRIVATE, Project::PROJ_PUBLIC);
		$this->paginate = $paginate;
		$this->set('projects', $this->paginate());
	}

/**
 * defaults method
 *
 * Allow users to configure default settings for projects.
 *
 *
 */
	public function defaults() {
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Project->saveDefaults($this->Auth->user('id'), $this->request->data)) {
				$this->Session->setFlash(__('Project defaults saved.'), 'bs_success');
			} else {
				$this->Session->setFlash(__('Unable to save project defaults.'), 'bs_error');
			}
		} else {
			$this->request->data['Project'] = $this->Project->getDefaults($this->Auth->user('id'));
		}
	}

/**
 * reset_user_defaults method
 *
 * Reset user settings to system defaults.
 *
 */
	public function reset_user_defaults() {
		$this->request->onlyAllow('post', 'put');
		if ($this->Project->resetUserDefaults($this->Auth->user('id'))) {
			$this->Session->setFlash(__('User defaults reset.'), 'bs_success');
		} else {
			$this->Session->setFlash(__('Unable to reset user defaults.'), 'bs_error');
		}
		return $this->redirect($this->referer());
	}

/**
 * reset_project_defaults method
 *
 * Reset supplied project to system defaults.
 *
 * @param Project $id
 * @throws NotFoundException
 * @throws ForbiddenException
 */
	public function reset_project_defaults($id = null) {
		$this->request->onlyAllow('post', 'put');
		$this->Project->id = $id;
		if (!$this->Project->exists()) {
			throw new NotFoundException(__('Invalid project id.'));
		}
		if (!$this->Project->isOwner($this->Auth->user('id'))) {
			throw new ForbiddenException(__('Not authorized to modify this project.'));
		}

		if ($this->Project->resetProjectDefaults($this->Auth->user('id'))) {
			$this->Session->setFlash(__('Project reset to user/system defaults.'), 'bs_success');
		} else {
			$this->Session->setFlash(__('Error resetting project to defaults.'), 'bs_error');
		}
		return $this->redirect($this->referer());
	}
}
