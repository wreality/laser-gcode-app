<?php
App::uses('AppModel', 'Model');

/**
 * Project Model
 *
 * @property Operation $Operation
 */
class Project extends AppModel {
	
/**
 * Project access mode constants
 * @var unknown
 */
	const PROJ_PUBLIC = 1;
	const PROJ_PRIVATE = 0;
	const PROJ_UNDEFINED = 2;
	
/**
 * Enum for access modes.
 * 
 * @var unknown
 */
	static $statuses = array(
		Project::PROJ_PUBLIC => 'Public',
		Project::PROJ_PRIVATE => 'Private',
		Project::PROJ_UNDEFINED => 'Undefined',
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Operation' => array(
			'className' => 'Operation',
			'foreignKey' => 'project_id',
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

/**
 * belongsTo associations
 * 
 * @var unknown
 */	
	public $belongsTo = array('User');

/**
 * __construct method
 *
 * Construct virtualFields array with isAnonymous
 * 
 * @param string $id
 * @param string $table
 * @param string $ds
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->virtualFields['isAnonymous'] = 'Project.public = "'.Project::PROJ_UNDEFINED.'"';
	}
	
/**
 * beforeSave method
 * 
 * Set default values for project settings
 * 
 * (non-PHPdoc)
 * @see Model::beforeSave()
 */
	public function beforeSave($options = array()) {
		if (empty($this->id) && empty($this->data[$this->alias]['id'])) {
			$this->data[$this->alias]['max_feedrate'] = Configure::read('LaserApp.default_max_cut_feedrate');
			$this->data[$this->alias]['traversal_rate'] = Configure::read('LaserApp.default_traversal_feedrate');
			$this->data[$this->alias]['home_before'] = true;
			$this->data[$this->alias]['clear_after'] = true;
			$this->data[$this->alias]['gcode_preamble'] = Configure::read('App.default_gcode_preamble');
			$this->data[$this->alias]['gcode_postscript'] = Configure::read('App.default_gcode_postscript');
		}
		return true;
	}

/**
 * validateMaterialThickness method
 *
 * Custom validation method to check if material_thickness supplied is sane
 * 
 * @return boolean|mixed
 */
	public function validateMaterialThickness() {
		if (!$this->data[$this->alias]['home_before']) {
			return true;
		}
		if ($this->data[$this->alias]['material_thickness'] == '0') {
			return true;
		}
		if (empty($this->data[$this->alias]['material_thickness'])) {
			return __('Material thickness is required if "Home Before" is enabled.');
		}
		if ($this->data[$this->alias]['material_thickness'] < 0) {
			return __('Material thickness cannot be negative');
		}
		if ($this->data[$this->alias]['material_thickness'] > Configure::read('App.z_total')) {
			return __('Material thickness must be less than max z-height.');
		}
		return true;
	}

/**
 * isOwner method
 *
 * Return true if given user is allowed to edit / generate code for a project.
 * 
 * @param unknown $user_id
 * @param string $project_id
 * @return boolean
 */
	public function isOwner($user_id, $project_id = null) {
		if (empty($project_id)) {
			$project_id = $this->id;
		}
		
		$owner_id = $this->field('user_id', array('Project.id' => $project_id));
		
		if ($owner_id == $user_id) {
			return true;
		} else if (!$this->User->exists($owner_id)) {
			return true;
		} else {
			return false;
		}
	}
}
