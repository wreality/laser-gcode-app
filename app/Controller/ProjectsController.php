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
		$this->Auth->allow('index', 'add', 'view');	
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
		$paginate['conditons'] = array_merge($this->_processSearch(), $paginate['conditions']);
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
				)
		);
		$paginate['conditions'] = array_merge($this->_processSearch(), $paginate['conditions']);
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
						'User.id' => null
				)
		);
		$paginate['conditons'] = array_merge($this->_processSearch(), $paginate['conditions']);
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
				return $this->redirect(array('action' => 'view', $id));
			} else {
				$this->Session->setFlash(__('There was an error while claiming your project.'), 'bs_error');
			}
		}
		return $this->redirect($this->referer());
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->Project->id = $id;
		if (!$this->Project->exists($id)) {
			throw new NotFoundException(__('Invalid project'));
		}
		if (!$this->Project->isOwner($this->Auth->user('id'))) {
			throw new ForbiddenException(__('Not allowed to access this project.'));
		}
		
		$this->Project->Behaviors->load('Containable');
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
			$fields = array('project_name', 'max_traversal');
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
		$this->set('presets', $this->Preset->getList());
		
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
			throw new ForbiddenException(__('Not authorized to access this project.'));
		}
		
		$this->Project->Behaviors->load('Containable');
		$this->Project->contain(array(
			'Operation' => array(
				'Path' => array(
					'Preset',
					'order' => array('order' => 'ASC'),
				)
			), 'User'
		));
		$project = $this->Project->read();
		
		foreach($project['Operation'] as $oi => $operation) {
			$home = false;
			$disableSteppers = false;
			$preamble = array();
			$postscript = array();
			if ($oi == 0) {
				if ($project['Project']['home_before']) {
					$home = true;
				}
				if (!empty($project['Project']['gcode_preamble'])) {
					$preamble =  $project['Project']['gcode_preamble'];
				}
			}
			if ($oi == (count($project['Operation'])-1)) {
				if (!empty($project['Project']['gcode_postscript'])) {
					$append = $project['Project']['gcode_postscript'];
				}
				$disableSteppers = true;
			}
			$this->Project->Operation->generateGcode($operation['id'], $home, $disableSteppers, $preamble, $postscript);
		}
		$this->redirect(array('action' => 'edit', $id));;
	}
	
/**
 * add method
 *
 * @return void
 */
	public function add() {
		$this->request->onlyAllow('post', 'put');
		$this->Project->create();
		if ($this->Auth->user('id')) {
			$this->request->data['Project']['user_id'] = $this->Auth->user('id');
			$this->request->data['Project']['public'] = Project::PROJ_PRIVATE;
		} else {
			$this->request->data['Project']['user_id'] = $this->request->clientIp();
			$this->request->data['Project']['public'] = Project::PROJ_UNDEFINED;
		}
		$this->request->data['Project']['project_name'] = '';
		if ($this->Project->save($this->request->data)) {
			$this->redirect(array('action' => 'view', $this->Project->id));
		} else {
			$this->Session->setFlash(__('The project could not be saved. Please, try again.'));
		}
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Project->exists($id)) {
			throw new NotFoundException(__('Invalid project'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Project->save($this->request->data)) {
				$this->Session->setFlash(__('The project has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The project could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Project.' . $this->Project->primaryKey => $id));
			$this->request->data = $this->Project->find('first', $options);
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
		$this->Project->id = $id;
		if (!$this->Project->exists()) {
			throw new NotFoundException(__('Invalid project'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->Project->delete()) {
			$this->Session->setFlash(__('Project deleted'));
			$this->redirect(array('action' => 'add'));
		}
		$this->Session->setFlash(__('Project was not deleted'));
		$this->redirect($this->referer());
	}

/**
 * admin_index method
 *
 * Display full list of system projects.
 */
	public function admin_index() {
		
		$paginate['conditions'] = $this->_processSearch();
		$this->paginate = $paginate;
		$this->set('projects', $this->paginate());
	}

}
