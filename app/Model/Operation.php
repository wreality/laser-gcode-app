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
			'dependent' => false,
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
		
		$files = Set::extract('/Path/file_hash', $operation);
		
		if (count($files) == 0) {
			return true;
		} else if (count($files) == 1) {
			copy(PDF_PATH.DS.$files[0].'.pdf', PDF_PATH.DS.$id.'.pdf');
		} else {
			
			$start_file = array_shift($files);
			$other_files = '';
			foreach ($files as $file) {
				$other_files.= 'background '.PDF_PATH.DS.$file.'.pdf ';
			}
			$exec_string = 'pdftk '. PDF_PATH.DS.$start_file.'.pdf '.$other_files.'output '.PDF_PATH.DS.$id.'.pdf';
			exec($exec_string);
		}
	}
}
