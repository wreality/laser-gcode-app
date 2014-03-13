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
	const PROJ_DEFAULTS = 2;
	
/**
 * Enum for access modes.
 * 
 * @var unknown
 */
	static $statuses = array(
		Project::PROJ_PUBLIC => 'Public',
		Project::PROJ_PRIVATE => 'Private',
	);

	public $virtualFields = array(
		'isAnonymous' => 'user_id IS NULL',
		'isEmpty' => 'operation_count < 1',
		'notEmpty' => 'operation_count > 0',
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
		)
	);

/**
 * belongsTo associations
 * 
 * @var unknown
 */	
	public $belongsTo = array(
		'User' => array(
			'counterCache' => array(
				'project_count' => array(),
				'public_count' => array('Project.public' => Project::PROJ_PUBLIC)
			)
		)
	);

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
			$defaults = $this->getDefaults($this->data[$this->alias]['user_id']);
			$this->data[$this->alias] = array_merge($this->data[$this->alias], $defaults);
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

/**
 * isPublic method
 *
 * Returns true if the selected project is public.
 * 
 * @param string $id
 * @return boolean
 */
	public function isPublic($id = null) {
		if (empty($id)) {
			$id = $this->id;
		}
		
		return ($this->field('public', array('id' => $id)) == Project::PROJ_PUBLIC);
	}
	
/**
 * isOwnerOrPublic method
 *
 * Returns true if project is owned by supplied user or is public.
 * 
 * @param unknown $user_id
 * @param string $id
 * @return boolean
 */
	public function isOwnerOrPublic($user_id, $id = null) {
		if (empty($id)) {
			$id = $this->id;
		}
		
		if ($this->isOwner($user_id)) {
			return true;
		} else {
			return $this->isPublic($id);
		}
	}

/**
 * updateModified method
 *
 * Force update of project's modified datetime.
 * 
 * @param Project $id
 * @return Ambigous <mixed, boolean, multitype:>
 */
	public function updateModified($id = null) {
		if (empty	($id)) {
			$id = $this->id;
		}
		
		return $this->save(array('Project' => array('id' => $id)));
	}

/**
 * saveDefaults method
 *
 * Save user project settings defaults
 * 
 * @param User $user_id
 * @param Project $data
 * @return array|boolean 
 */
	public function saveDefaults($user_id, $data) {
		$defaults = $this->getDefaults($user_id, true);
		if (empty($defaults['id'])) {
			$this->create();
		} else {
			$data['Project']['id'] = $defaults['id'];
		}
		$data['Project']['public'] = Project::PROJ_DEFAULTS;
		$data['Project']['user_id'] = $user_id;
		$fields = array_merge(array_keys($defaults), array('public', 'user_id'));
		return $this->save($data, true, $fields);
	}

/**
 * resetUserDefaults method
 *
 * Reset user defaults to system defaults, (by deleting any stored user defaults.
 * 
 * @param User $user_id
 * @return boolean
 */
	public function resetUserDefaults($user_id) {
		return $this->deleteAll(array('Project.user_id' => $user_id, 'Project.public' => Project::PROJ_DEFAULTS));
	}
	
/**
 * resetProjectDefaults method
 *
 * Reset supplied project to user/system defaults.
 * 
 * @param User $user_id
 * @param Project $id
 * @return Array|boolean
 */
	public function resetProjectDefaults($user_id, $id = null) {
		if (empty($id)) {
			$id = $this->id;
		}
		$defaults = $this->getDefaults($user_id);
		$fields = array_merge(array_keys($defaults), array('id'));
		$project = $this->find('first', array(
			'conditions' =>  array(
				'Project.id' => $id,
			),
			'contain' => array(),
			'fields' => $fields
		));
		$project['Project'] = array_merge($project['Project'], $defaults);

		return $this->save($project, true, $fields);
	}

/**
 * getDefaults method
 *
 * Return user defaults, or system defaults if user defaults don't exist.
 * 
 * @param User $user_id
 * @param boolean $include_id Optionally include the id of the defaults project.
 * @return Array
 */
	public function getDefaults($user_id = null, $include_id = false) {
		$system_defaults = array(
			'max_feedrate' => Configure::read('LaserApp.default_max_cut_feedrate'),
			'traversal_rate' => Configure::read('LaserApp.default_traversal_feedrate'),
			'home_before' => true,
			'clear_after' => false,
			'gcode_preamble' => Configure::read('LaserApp.default_gcode_preamble'),
			'gcode_postscript' => Configure::read('LaserApp.default_gcode_postscript'),
		);
		if (!empty($user_id)) {
			$fields = array_keys($system_defaults);
			if ($include_id) {
				$fields[] = 'id';
			}
			$project = $this->find('first', array(
				'conditions' => array(
					'Project.user_id' => $user_id,
					'Project.public' => Project::PROJ_DEFAULTS,
				),
				'fields' => $fields,
				'contain' => array(),
			));
			if ($project) {
				return array_merge($system_defaults, $project['Project']);
			} else {
				return $system_defaults;
			}
		} else {
			return $system_defaults;
		}
	}
}
