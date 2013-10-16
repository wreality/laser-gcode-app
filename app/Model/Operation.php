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
	
	public function generateGcode($id = null, $prepend = array(), $append = array()) {
		if (!empty($id)) {
			$this->id = $id;
		}
		
		$this->Behaviors->attach('Containable');
		$this->contain(array(
			'Project',
			'Path',
		));
		$operation = $this->read();
		$command = 'pstoedit -q -f "gcode: -speed %d -intensity %d -noheader -nofooter" %s';
		$gcode = array();
		
		foreach ($operation['Path'] as $path) {
			$speed = $operation['Project']['max_feedrate'] * ($path['speed']/100);
			$power = $path['power'];
			$gcode[] = '';
			$gcode[] = '; Start of path: '.$path['file_name'];
			$gcode[] = sprintf('; Speed: %d, Power: %d', $speed, $power);
			
			exec(sprintf($command,$path['speed'], $path['power'], PDF_PATH.DS.$path['file_hash'].'.pdf'), $gcode);
			
			$gcode[] = '; End of path: '.$path['file_name'];
		}
		
		$output = array_merge($prepend, $gcode, $append);
		return file_put_contents(PDF_PATH.DS.$this->id.'.gcode', implode("\n", $output));
		
	}
}
