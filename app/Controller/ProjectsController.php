<?php
App::uses('AppController', 'Controller');
/**
 * Projects Controller
 *
 * @property Project $Project
 */
class ProjectsController extends AppController {

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Project->recursive = 1;
		$this->paginate = array(
			'order' => array('Project.created' => 'DESC'));
		$this->set('projects', $this->paginate());
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
		$this->Project->Behaviors->load('Containable');
		$this->Project->contain(array(
			'Operation' => array(
				'Path' => array(
					'Preset',
					'order' => array('order' => 'ASC'),
				)
			)
		));
		$options = array('conditions' => array('Project.' . $this->Project->primaryKey => $id));
		$project = $this->Project->find('first', $options);
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Project->save($this->request->data)) {
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
					$project['Project'] = $this->request->data['Project'];
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
		$this->Project->create();
		$this->request->data = array('Project' => array('project_name' => ''));
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
}
