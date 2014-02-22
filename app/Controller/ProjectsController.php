<?php
App::uses('AppController', 'Controller');
/**
 * Projects Controller
 *
 * @property Project $Project
 */
class ProjectsController extends AppController {

	public function beforeFilter() {
		$this->Auth->allow('index', 'add', 'view');
		
		parent::beforeFilter();
	}
	
	public function isAuthorized($user = null) {
		parent::isAuthorized($user);
		if (in_array($this->request->params['action'], array('view', 'edit', 'delete'))) {
			var_dump($this->request->params);
		}
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
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Project->id = $id;
		if (!$this->Project->exists($id)) {
			throw new NotFoundException(__('Invalid project'));
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
		
		if (!empty($project['User']['id'])) {
			if (!$project['Project']['public']) {
				if ($this->Auth->user('id') != $project['Project']['user_id']) {
					throw new ForbiddenException(__('Project is not public.'));
				}
			}
		}
		$options = array('conditions' => array('Project.' . $this->Project->primaryKey => $id));
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Project->save($this->request->data)) {
				$project = $this->Project->find('first', $options);
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
				
			} else {
				$this->Session->setFlash(__('There was an error saving this project.'), 'bs_error');
			}
		} else {
			$this->request->data = $project;
		}
		$this->loadModel('Preset');
		$this->set('presets', $this->Preset->getList());
		$this->set('project', $project);
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
		} else {
			$this->request->data['Project']['user_id'] = $this->request->clientIp();
		}
		$this->request->data['Project']['project_name'] = '';
		$this->request->data['Project']['public'] = Project::PROJ_PRIVATE;
		if ($this->Project->save($this->request->data)) {
			$this->redirect(array('action' => 'view', $this->Project->id));
		} else {
			$this->Session->setFlash(__('The project could not be saved. Please, try again.'));
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

	public function admin_index() {
		
		$paginate['conditions'] = $this->_processSearch();
		$this->paginate = $paginate;
		$this->set('projects', $this->paginate());
	}

}
