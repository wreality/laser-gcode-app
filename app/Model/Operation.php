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
			'order' => '',
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
		$operation = $this->find('first', array('conditions' => array('Operation.id' => $id)));
		
		//$files = Set::extract('/Path/file_hash', $operation);
		if (!extension_loaded('imagick')) {
			copy(PDF_PATH.DS.'no-preview.png', PDF_PATH.DS.$id.'.png');
			return false;
		}
		if (count($operation['Path']) == 0) {
			unlink(PDF_PATH.DS.$id.'.png');
			return true;
		} else { 
			$image = new Imagick(PDF_PATH.DS.$operation['Path'][0]['file_hash'].'.pdf');
			$image->setresolution(300, 300);
			$image->setImageFormat('png');
			if (count($operation['Path']) == 1) {
			} else {
				array_shift($operation['Path']);
				foreach ($operation['Path'] as $file) {
					$layer = new Imagick(PDF_PATH.DS.$file['file_hash'].'.pdf');
					$layer->setImageFormat('png');
					$layer->setResolution(300, 300);
					$image->compositeImage($layer, Imagick::COMPOSITE_DEFAULT, 0, 0);
					$layer->destroy();
				}
			}
			$image->writeImage(PDF_PATH.DS.$id.'.png');
		}
	}
}
