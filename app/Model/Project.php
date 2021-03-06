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
	public static $statuses = array(
		Project::PROJ_PUBLIC => 'Public',
		Project::PROJ_PRIVATE => 'Private',
	);

	public $virtualFields = array(
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

	public $order = array(
			'Project.modified' => 'DESC',
	);

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
 * @param unknown $userId
 * @param string $projectId
 * @return boolean
 */
	public function isOwner($userId, $strict = false, $projectId = null) {
		if (empty($projectId)) {
			$projectId = $this->id;
		}

		$ownerId = $this->field('user_id', array('Project.id' => $projectId));

		if ($ownerId == $userId) {
			return true;
		} elseif (($strict === false) && (!$this->User->exists($ownerId))) {
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
 * isAnonymous method
 *
 * Return true if project is not owned by a user.
 *
 * @param string $id
 */
	public function isAnonymous($id = null) {
		if (empty($id)) {
			$id = $this->id;
		}
		return !$this->User->exists($this->field('user_id', array('id' => $id)));
	}

/**
 * isOwnerOrPublic method
 *
 * Returns true if project is owned by supplied user or is public.
 *
 * @param unknown $userId
 * @param string $id
 * @return boolean
 */
	public function isOwnerOrPublic($userId, $id = null) {
		if (empty($id)) {
			$id = $this->id;
		}

		if ($this->isOwner($userId, false, $id)) {
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
		if (empty($id)) {
			$id = $this->id;
		}

		return $this->save(array('Project' => array('id' => $id)));
	}

/**
 * saveDefaults method
 *
 * Save user project settings defaults
 *
 * @param User $userId
 * @param Project $data
 * @return array|boolean
 */
	public function saveDefaults($userId, $data) {
		$defaults = $this->getDefaults($userId, true);
		if (empty($defaults['id'])) {
			$this->create();
		} else {
			$data['Project']['id'] = $defaults['id'];
		}
		$data['Project']['public'] = Project::PROJ_DEFAULTS;
		$data['Project']['user_id'] = $userId;

		$fields = array_merge(array_keys($defaults), array('public', 'user_id'));
		return $this->save($data, true, $fields);
	}

/**
 * resetUserDefaults method
 *
 * Reset user defaults to system defaults, (by deleting any stored user defaults.
 *
 * @param User $userId
 * @return boolean
 */
	public function resetUserDefaults($userId) {
		return $this->deleteAll(array('Project.user_id' => $userId, 'Project.public' => Project::PROJ_DEFAULTS));
	}

/**
 * resetProjectDefaults method
 *
 * Reset supplied project to user/system defaults.
 *
 * @param User $userId
 * @param Project $id
 * @return Array|boolean
 */
	public function resetProjectDefaults($userId, $id = null) {
		if (empty($id)) {
			$id = $this->id;
		}
		$defaults = $this->getDefaults($userId);
		$fields = array_merge(array_keys($defaults), array('id'));
		$project = $this->find('first', array(
			'conditions' => array(
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
 * @param User $userId
 * @param boolean $includeId Optionally include the id of the defaults project.
 * @return Array
 */
	public function getDefaults($userId = null, $includeId = false) {
		$systemDefaults = array(
			'max_feedrate' => Configure::read('LaserApp.default_max_cut_feedrate'),
			'traversal_rate' => Configure::read('LaserApp.default_traversal_feedrate'),
			'home_before' => true,
			'clear_after' => false,
			'gcode_preamble' => Configure::read('LaserApp.default_gcode_preamble'),
			'gcode_postscript' => Configure::read('LaserApp.default_gcode_postscript'),
			'public' => Project::PROJ_PRIVATE,
		);
		if (!empty($userId)) {
			$fields = array_keys($systemDefaults);
			if ($includeId) {
				$fields[] = 'id';
			}
			$project = $this->find('first', array(
				'conditions' => array(
					'Project.user_id' => $userId,
					'Project.public' => Project::PROJ_DEFAULTS,
				),
				'fields' => $fields,
				'contain' => array(),
			));
			if ($project) {
				return array_merge($systemDefaults, $project['Project']);
			} else {
				return $systemDefaults;
			}
		} else {
			return $systemDefaults;
		}
	}

/**
 * copyProject method
 *
 * Copy a project, optionally supplying a new name.
 *
 * @param string $title
 * @param string $id
 * @return boolean
 */
	public function copyProject($title = null, $id = null) {
		if (empty($id)) {
			$id = $this->id;
		}

		$this->contain(array(
			'Operation' => array(
				'Path'
			)
		));

		$project = $this->read(null, $id);

		if (!empty($title)) {
			$project['Project']['project_name'] = $title;
		}
		$this->create();
		$project['Project']['id'] = null;
		foreach ($project['Operation'] as &$operation) {
			$operation['id'] = null;
			$operation['project_id'] = null;
			foreach ($operation['Path'] as &$path) {
				$path['id'] = null;
				$path['operatoin_id'] = null;
			}
		}
		return $this->saveAssociated($project, array('validate' => false, 'deep' => true));
	}

/**
 * updateOverviews method
 *
 * Update all operation overviews for a given project.
 *
 * @param string $id
 */
	public function updateOverviews($id = null) {
		if (empty($id)) {
			$id = $this->id;
		}
		$this->contain(array('Operation'));
		$project = $this->read(null, $id);
		foreach ($project['Operation'] as $operation) {
			$this->Operation->updateOverview($operation['id']);
		}
	}

/**
 * newProject method
 *
 * Create a new proejct.
 *
 * @param string $userId
 * @return Ambigous <mixed, boolean, multitype:>
 */
	public function newProject($userId = null) {
		$this->create();

		$data['Project'] = $this->getDefaults($userId);
		$data['Project']['user_id'] = $userId;
		return $this->save($data);
	}

/**
 * claimProject method
 *
 * Claim a project by a user.
 *
 * @param unknown $userId
 * @param string $id
 * @return Ambigous <mixed, boolean, multitype:>
 */
	public function claimProject($userId, $id = null) {
		if (empty($id)) {
			$id = $this->id;
		}

		return $this->save(array('Project' => array('id' => $id, 'user_id' => $userId)));
	}
}
