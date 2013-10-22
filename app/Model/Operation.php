<?php
App::uses('AppModel', 'Model');
/**
 * Operation Model
 *
 * @property Project $Project
 * @property Path $Path
 */
class Operation extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'project_id' => array(
			'uuid' => array(
				'rule' => array('uuid'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'project_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Path' => array(
			'className' => 'Path',
			'foreignKey' => 'operation_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => array('order' => 'ASC'),
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);


	public function updateOverview($id = null) {
		if (empty($id)) {
			$id = $this->id;
		}
		$this->id = $id;
		
		$operation = $this->find('first', array('conditions' => array('Operation.id' => $id)));
		
		//$files = Set::extract('/Path/file_hash', $operation);
		if (!extension_loaded('imagick')) {
			copy(PDF_PATH.DS.'no-preview.png', PDF_PATH.DS.$id.'.png');
			return false;
		}
		if (count($operation['Path']) == 0) {
			$this->saveField('size_warning', false);
			unlink(PDF_PATH.DS.$id.'.png');
			return true;
		} else { 
			$image = new Imagick(PDF_PATH.DS.$operation['Path'][0]['file_hash'].'.pdf');
			$warn_size = false;
			$size['width'] = $operation['Path'][0]['width'];
			$size['height'] = $operation['Path'][0]['height'];
			
			$image->setresolution(300, 300);
			$image->setImageFormat('png');
			if (count($operation['Path']) > 1) {
				$colors = Configure::read('App.colors');
				array_shift($operation['Path']);
				foreach ($operation['Path'] as $fi => $file) {
					$layer = new Imagick(PDF_PATH.DS.$file['file_hash'].'.pdf');
					if (($file['width'] != $size['width']) || ($file['height'] != $size['height'])){
						$warn_size = true;
					}
					$layer->paintOpaqueImage('#000000', $colors[$fi+1], 50000);
					$layer->setImageFormat('png');
					$layer->setResolution(300, 300);
					$image->compositeImage($layer, Imagick::COMPOSITE_DEFAULT, 0, 0);
					$layer->destroy();
				}
			}
			$this->saveField('size_warning', $warn_size);
			$image->writeImage(PDF_PATH.DS.$id.'.png');
		}
	}
	
	public function generateGcode($id = null, $home = false, $disableSteppers = true, $preamble = array(), $postscript = array()) {
		if (!empty($id)) {
			$this->id = $id;
		}
		
		$this->Behaviors->attach('Containable');
		$this->contain(array(
			'Project',
			'Path',
		));
		$operation = $this->read();
		
		$gcode = array();
		App::import('Model', 'GCode');
		
		$GCode = new GCode();
		$GCode->insertGCode($preamble);
		$GCode->startOpCode($home);
		
		foreach ($operation['Path'] as $path) {
			$speed = $operation['Project']['max_feedrate'] * ($path['speed']/100);
			$power = ($path['power']/100) * Configure::read('App.power_scale');
			$GCode->insertComment('Start of path: '.$path['file_name']);
			$GCode->insertComment(sprintf('; Speed: %d, Power: %d', $speed, $power));
			$GCode->newLine();
			$GCode->pstoedit($speed, $power, $operation['Project']['traversal_rate'], PDF_PATH.DS.$path['file_hash'].'.pdf');
			$GCode->laserOff();
			$GCode->moveTo(0,0,false, 6000);
			$GCode->insertComment('End of path: '.$path['file_name']);
		}
		
		$GCode->endOpCode($disableSteppers);
		$GCode->insertGCode($postscript);
		
		return $GCode->writeFile(PDF_PATH.DS.$this->id.'.gcode');
		
		
		
	}

	public function beforeDelete($cascade = true) {
		$project_id = $this->field('project_id');
		$this->recursive = -1;
		$operations = $this->find('all', array(
			'conditions' => array(
				'Operation.project_id' => $project_id,
				'Operation.id <>' => $this->id,
			),
		));
		foreach ($operations as $oi => $operation) {
			$this->id = $operation['Operation']['id'];
			$this->saveField('order', $oi+1);
		}
	}
	
	public function beforeSave($options = array()) {
		if (empty($this->id) && empty($this->data[$this->alias]['id'])) {
			$order = $this->field('order', array('project_id' => $this->data[$this->alias]['project_id']), array('order' => 'DESC'))+1;
			$this->data[$this->alias]['order'] = $order;
		}
		return true;
	}
}
