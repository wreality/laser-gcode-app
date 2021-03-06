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

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'project_id',
			'counterCache' => 'operation_count',
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

/**
 * updateOverview method
 *
 * Update the operation overview image.
 *
 * @param string $id
 * @return boolean
 */
	public function updateOverview($id = null) {
		if (empty($id)) {
			$id = $this->id;
		}
		$this->id = $id;

		$operation = $this->find('first', array('conditions' => array('Operation.id' => $id)));

		//$files = Set::extract('/Path/file_hash', $operation);
		if (!extension_loaded('imagick')) {
			copy(Configure::read('LaserApp.storage_path') . DS . 'no-preview.png', Configure::read('LaserApp.storage_path') . DS . $id . '.png');
			return false;
		}
		if (count($operation['Path']) == 0) {
			$this->saveField('size_warning', false);
			unlink(Configure::read('LaserApp.storage_path') . DS . $id . '.png');
			return true;
		} else {
			$image = new Imagick(Configure::read('LaserApp.storage_path') . DS . $operation['Path'][0]['file_hash'] . '.pdf');
			$warnSize = false;
			$size['width'] = $operation['Path'][0]['width'];
			$size['height'] = $operation['Path'][0]['height'];

			$image->setresolution(300, 300);
			$image->setImageFormat('png');
			if (count($operation['Path']) > 1) {
				$colors = Configure::read('App.colors');
				array_shift($operation['Path']);
				foreach ($operation['Path'] as $fi => $file) {
					$layer = new Imagick(Configure::read('LaserApp.storage_path') . DS . $file['file_hash'] . '.pdf');
					if (($file['width'] != $size['width']) || ($file['height'] != $size['height'])) {
						$warnSize = true;
					}
					$layer->paintOpaqueImage('#000000', $colors[$fi + 1], 50000);
					$layer->setImageFormat('png');
					$layer->setResolution(300, 300);
					$image->compositeImage($layer, Imagick::COMPOSITE_DEFAULT, 0, 0);
					$layer->destroy();
				}
			}
			$this->saveField('size_warning', $warnSize);
			$image->writeImage(Configure::read('LaserApp.storage_path') . DS . $id . '.png');
		}
	}

/**
 * getGCodeFilename method
 *
 * Returns full path to operations gcode.
 *
 * @param string $id
 * @return string
 */
	public function getGCodeFilename($id = null) {
		if (empty($id)) {
			$id = $this->id;
		}
		return Configure::read('LaserApp.storage_path') . DS . $id . '.gcode';
	}

/**
 * gCodeExists method
 *
 * Returns true if gcode actually exists on disk.
 *
 * @param string $id
 * @return boolean
 */
	public function gCodeExists($id = null) {
		if (empty($id)) {
			$id = $this->id;
		}
		return file_exists($this->getGCodeFilename($id));
	}

/**
 * deleteGCode method
 *
 * Helper function to delete gcode stored on disk.  Primarly user in test cases.
 *
 * @param string $id
 * @return boolean
 */
	public function deleteGCode($id = null) {
		if (empty($id)) {
			$id = $this->id;
		}
		return unlink($this->getGCodeFilename($id));
	}

/**
 * generateGcode method
 *
 * Save Gcode for operation.
 *
 * @param string $id
 * @return number
 */
	public function generateGcode($id = null) {
		if (!empty($id)) {
			$this->id = $id;
		}

		$this->contain(array(
			'Project',
			'Path',
		));
		$operation = $this->read();

		$gcode = array();
		App::import('Model', 'GCode');

		$GCode = new GCode();
		if ($operation['Operation']['order'] == 1) {
			if (!empty($operation['Project']['gcode_preamble'])) {
				$GCode->insertGCode(explode("\n", $operation['Project']['gcode_preamble']));
			}
		}

		$GCode->startOpCode($operation['Project']['home_before']);

		foreach ($operation['Path'] as $path) {
			$speed = $operation['Project']['max_feedrate'] * ($path['speed'] / 100);
			$power = ($path['power'] / 100) * Configure::read('LaserApp.power_scale');
			$GCode->insertComment('Start of path: ' . $path['file_name']);
			$GCode->insertComment(sprintf('; Speed: %d, Power: %d', $speed, $power));
			$GCode->newLine();
			$GCode->pstoedit($speed, $power, $operation['Project']['traversal_rate'], Configure::read('LaserApp.storage_path') . DS . $path['file_hash'] . '.pdf');
			$GCode->laserOff();
			$GCode->moveTo(0, 0, false, $operation['Project']['traversal_rate']);
			$GCode->insertComment('End of path: ' . $path['file_name']);
		}

		if ($operation['Operation']['order'] == ($operation['Project']['operation_count'])) {
			$GCode->endOpCode(true);
			if (!empty($operation['Project']['gcode_postscript'])) {
				$GCode->insertGCode(explode("\n", $operation['Project']['gcode_postscript']));
			}
		} else {
			$GCode->endOpCode(false);
		}

		return $GCode->writeFile($this->getGCodeFilename($id)) !== false;
	}

/**
 * beforeDelete method
 *
 * When deleting an operation, reorder remaining operations appropriately.
 *
 * (non-PHPdoc)
 * @see Model::beforeDelete()
 */
	public function beforeDelete($cascade = true) {
		$projectId = $this->field('project_id');
		$this->recursive = -1;
		$operations = $this->find('all', array(
			'conditions' => array(
				'Operation.project_id' => $projectId,
				'Operation.id <>' => $this->id,
			),
		));
		foreach ($operations as $oi => $operation) {
			$this->id = $operation['Operation']['id'];
			$this->saveField('order', $oi + 1);
		}
	}

/**
 * beforeSave method
 *
 * Add newly created operation to bottom of operation list.
 *
 * (non-PHPdoc)
 * @see Model::beforeSave()
 */
	public function beforeSave($options = array()) {
		if (empty($this->id) && empty($this->data[$this->alias]['id'])) {
			$order = $this->field('order', array('project_id' => $this->data[$this->alias]['project_id']), array('order' => 'DESC')) + 1;
			$this->data[$this->alias]['order'] = $order;
		}
		return true;
	}

/**
 * afterSave method
 *
 * Trigger update of enclosing project's modified datetime.
 *
 * (non-PHPdoc)
 * @see Model::afterSave()
 */
	public function afterSave($created, $options = array()) {
		return $this->updateParentModified();
	}

/**
 * afterDelete method
 *
 * Trigger update of enclosing project's modified datetime.
 *
 * (non-PHPdoc)
 * @see Model::afterDelete()
 */
	public function afterDelete() {
		return $this->updateParentModified();
	}

/**
 * isOwner method
 *
 * Returns true if enclosing project is owned by the supplied user.
 *
 * @param User $userId
 * @param Operation $id
 * @return boolean
 */
	public function isOwner($userId, $id = null) {
		if (empty($id)) {
			$id = $this->id;
		}

		return $this->Project->isOwner($userId, false, $this->_getProjectId($id));
	}

/**
 * isOwnerOrPublic method
 *
 * Return true if enclosing project is owned by the supplied user, or if the
 * project is public.
 *
 * @param User $userId
 * @param Operation $id
 */
	public function isOwnerOrPublic($userId, $id = null) {
		if (empty($id)) {
			$id = $this->id;
		}

		return $this->Project->isOwnerOrPublic($userId, $this->_getProjectId($id));
	}

/**
 * _getProjectId method
 *
 * Returns the enclosing project id for a supplied operation.
 *
 * @param Operation $id
 * @return Ambigous <string, boolean, mixed>
 */
	protected function _getProjectId($id = null) {
		if (empty($id)) {
			$id = $this->id;
		}
		return $this->field('project_id', array('id' => $id));
	}

/**
 * updateModified method
 *
 * Force update of given operations modified datetime.
 *
 * @param Operation $id
 * @return Ambigous <mixed, boolean, multitype:>
 */
	public function updateModified($id = null) {
		if (empty($id)) {
			$id = $this->id;
		}

		return $this->save(array('Operation' => array('id' => $id)));
	}

/**
 * updateParentModified method
 *
 * Update parent project's modified datetime.
 *
 * @param Operation $id
 */
	public function updateParentModified($id = null) {
		if (empty($id)) {
			$id = $this->id;
		}

		return $this->Project->updateModified($this->field('project_id', array('id' => $id)));
	}

/**
 * copyOperation
 *
 * Copy given operation inside its current project.
 *
 * @param string $id
 * @return Ambigous <mixed, boolean, multitype:boolean , multitype:boolean unknown , multitype:>
 */
	public function copyOperation($id = null) {
		if (empty($id)) {
			$id = $this->id;
		}

		$this->contain(array('Path'));

		$operation = $this->read(null, $id);

		$operation['Operation']['id'] = null;
		$operation['Operation']['order'] = null;
		foreach ($operation['Path'] as &$path) {
			$path['id'] = null;
			$path['operation_id'] = null;
		}

		return $this->saveAssociated($operation, array('validate' => false));
	}
}
