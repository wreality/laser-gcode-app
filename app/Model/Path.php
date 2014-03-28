<?php
App::uses('AppModel', 'Model');
/**
 * Path Model
 *
 * @property Operation $Operation
 */
class Path extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'file' => array(
			'validupload' => array(
				'rule' => array('validateValidUpload'),
			)
		),
		'preset_id' => array(
			'preset_id' => array(
				'rule' => array('validateImportPreset'),
			),
		),
		'operation_id' => array(
			'uuid' => array(
				'rule' => array('uuid'),
				'required' => true,
				'on' => 'create',
			),
			'uuid_update' => array(
				'rule' => array('uuid'),
				'on' => 'update',
			),
		),
		'file_hash' => array(
			'notempty' => array(
				'rule' => array('notempty'),
			),
		),
		'order' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'file_name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
			),
		),
		'power' => array(
			'over0' => array(
				'rule' => array('comparison', '>', 0),
				'message' => 'Power must be between 0%% and 100%%',
			),
			'lessequal100' => array(
				'rule' => array('comparison', '<=', 100),
				'message' => 'Power must be between 0%% and 100%%',		),
		),
		'speed' => array(
			'over0' => array(
				'rule' => array('comparison', '>', 0),
				'message' => 'Speed must be between 0%% and 100%%',
			),
			'lessequal100' => array(
				'rule' => array('comparison', '<=', 100),
				'message' => 'Speed must be between 0%% and 100%%')
		),

	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Operation' => array(
			'className' => 'Operation',
			'foreignKey' => 'operation_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Preset',
	);

/**
 * validateValidUpload method
 *
 * Validate error status of file upload and confirm that file produces GCode
 *
 * @param unknown $check
 * @return mixed|boolean
 */
	public function validateValidUpload($check) {
		switch ($this->data[$this->alias]['file']['error']) {
			case UPLOAD_ERR_INI_SIZE:
				return __('File upload exceeds the max size allowed.');
			case UPLOAD_ERR_FORM_SIZE:
				return __('File upload exceed application max size allowed.');
			case UPLOAD_ERR_PARTIAL:
				return __('File upload was not completed.');
			case UPLOAD_ERR_NO_FILE:
				if (empty($this->data[$this->alias]['id'])) {
					return __('Path file upload is required.');
				} else {
					return true;
				}
				break;
			case UPLOAD_ERR_NO_TMP_DIR:
				return __('Error uploading temproary file.');
			case UPLOAD_ERR_CANT_WRITE:
				return __('Unable to write uploaded file to disk.');
		}

		if (!in_array($this->data[$this->alias]['file']['type'], Configure::read('App.allowed_file_types'))) {
			return __('Invalid file type.  Only pdf is currently supported.');
		}
		App::import('Model', 'GCode');
		$GCode = new GCode();

		$gc = $GCode->pstoedit(100, 100, 6000, $this->data[$this->alias]['file']['tmp_name']);
		if (empty($gc)) {
			return __('No GCode generated from upload.  Are there vectors in your PDF ?');
		}
		$this->data[$this->alias]['file_hash'] = md5_file($this->data[$this->alias]['file']['tmp_name']);
		$this->data[$this->alias]['file_name'] = $this->data[$this->alias]['file']['name'];
		return true;
	}

/**
 * validateImportPreset method
 *
 * Retrieve preset and replace path speed and power with appropriate values.
 *
 * @param unknown $check
 * @return boolean|mixed
 */
	public function validateImportPreset($check) {
		if (($this->data[$this->alias]['preset_id'] == 1)) {
			return true;
		}
		if (empty($this->data[$this->alias]['preset_id'])) {
			return __('Select a preset or "Custom" to add a path.');
		}
		App::import('Model', 'Preset');
		$Preset = new Preset();
		if (!$Preset->exists($this->data[$this->alias]['preset_id'])) {
			return __('Invalid preset.');
		} else {
			$p = $Preset->read(null, $this->data[$this->alias]['preset_id']);

			$this->data[$this->alias]['power'] = $p['Preset']['power'];
			$this->data[$this->alias]['speed'] = $p['Preset']['speed'];
			return true;
		}
	}

/**
 * beforeDelete method
 *
 * When deleteing a path, reorder the remaining paths.
 *
 * (non-PHPdoc)
 * @see Model::beforeDelete()
 */
	public function beforeDelete($cascade = true) {
		$operationId = $this->field('operation_id');
		$this->recursive = -1;
		$paths = $this->find('all', array(
				'conditions' => array(
					'Path.operation_id' => $operationId,
					'Path.id <>' => $this->id,
				),
				'order' => array(
						'Path.order' => 'ASC'
				)
		));
		foreach ($paths as $pi => $path) {
			$this->id = $path['Path']['id'];
			$this->saveField('order', $pi + 1);
		}
	}

/**
 * beforeSave method
 *
 * Create thumbnail and order new paths to the bottom of the path order.
 *
 * (non-PHPdoc)
 * @see Model::beforeSave()
 */
	public function beforeSave($options = array()) {
		if (empty($this->id) && empty($this->data[$this->alias]['id'])) {
			$this->data[$this->alias]['order'] = $this->field('order', array('operation_id' => $this->data[$this->alias]['operation_id']), array('Path.order' => 'DESC')) + 1;

		}
		if ((!empty($this->data[$this->alias]['file'])) && ($this->data[$this->alias]['file']['error'] == 0)) {
			if (!move_uploaded_file($this->data[$this->alias]['file']['tmp_name'], PDF_PATH . DS . $this->data[$this->alias]['file_hash'] . '.pdf')) {
				return false;
			}

			if (extension_loaded('imagick')) {

				$image = new Imagick(PDF_PATH . DS . $this->data[$this->alias]['file_hash'] . '.pdf');
				$res = $image->getImageGeometry();
				$this->data[$this->alias]['height'] = $res['height'];
				$this->data[$this->alias]['width'] = $res['width'];
				$image->setResolution(150, 150);
				$image->setImageFormat('png');
				$image->setImageBackgroundColor('white');
				$image = $image->flattenImages();
				$image->writeImage(PDF_PATH . DS . $this->data[$this->alias]['file_hash'] . '.png');
			} else {
				copy(PDF_PATH . DS . 'no-image.png', PDF_PATH . DS . $this->data[$this->alias]['file_hash'] . '.png');
			}
			return true;
		}
		return true;
	}

/**
 * movePathUp method
 *
 * Move the given path up in the order.  Wrapper for Path::movePath
 *
 * @see Path::movePath()
 * @param Path $id
 * @return boolean
 */
	public function movePathUp($id = null) {
		return $this->movePath(PATH_MOVE_UP, $id);
	}

/**
 * movePathDown method
 *
 * Move the given path down in the order.  Wrapper for Path::movePath
 *
 * @see Path::movePath()
 * @param Path $id
 * @return boolean
 */
	public function movePathDown($id = null) {
		return $this->movePath(PATH_MOVE_DOWN, $id);
	}

/**
 * movePath method
 *
 * Move the given path in the direction indicated.
 *
 * @param int $dir
 * @param Path $id
 * @return boolean
 */
	public function movePath($dir, $id = null) {
		if (empty($id)) {
			$id = $this->id;
		}

		$this->recursive = -1;
		$path = $this->read();
		$exPath = $this->find('first', array(
			'conditions' => array(
				'order' => $path['Path']['order'] + ($dir),
				'operation_id' => $path['Path']['operation_id']
			)
		));

		if (!$exPath) {
			return false;
		}
		$exPath['Path']['order'] += ($dir * -1);
		$path['Path']['order'] += ($dir);

		if ($this->save($exPath) && $this->save($path)) {
			$this->Operation->updateOverview($path['Path']['operation_id']);
			return true;
		} else {

			return false;
		}
	}

/**
 * isOwner method
 *
 * Returns true if given user is allowed to edit / view the enclosing project.
 *
 * @param User $userId
 * @param Path $id
 * @return boolean
 */
	public function isOwner($userId, $id = null) {
		if (empty($id)) {
			$id = $this->id;
		}

		return $this->Operation->isOwner($userId, $this->field('operation_id', array('id' => $id)));
	}

/**
 * isOwnerOrPublic method
 *
 * Returns true if given user is allowed to view the enclosing project.
 *
 * @param unknown $userId
 * @param string $id
 * @return boolean
 */
	public function isOwnerOrPublic($userId, $id = null) {
		if (empty($id)) {
			$id = $this->id;
		}

		return $this->Operation->isOwnerOrPublic($userId, $this->field('operation_id', array('id' => $id)));
	}

}
