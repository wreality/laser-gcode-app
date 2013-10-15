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
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				'required' => true,
				//'last' => false, // Stop validation after this rule
				'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'uuid_update' => array(
				'rule' => array('uuid'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => true,
				//'last' => false, // Stop validation after this rule
				'on' => 'update', // Limit validation to 'create' or 'update' operations
			),
		),
		'file_hash' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'order' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'file_name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'speed' => array(
			'sane' => array(
				'rule' => array('range', 5, 101),
				'message' => 'Speed must be between 10 and 100%',
			),
		),

		'power' => array(
				'sane' => array(
						'rule' => array('range', 5, 101),
						'message' => 'Power must be between 5 and 100%',
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
		'Operation' => array(
			'className' => 'Operation',
			'foreignKey' => 'operation_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Preset',
	);

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
		$this->data[$this->alias]['file_hash'] = md5_file($this->data[$this->alias]['file']['tmp_name']);
		if (!move_uploaded_file($this->data[$this->alias]['file']['tmp_name'], PDF_PATH.DS.$this->data[$this->alias]['file_hash'].'.pdf')) {
			return __('Unable to move uploaded file.');
		}
		$this->data[$this->alias]['file_name'] = $this->data[$this->alias]['file']['name'];
		$this->data[$this->alias]['order'] = $this->field('order', array('operation_id' => $this->data[$this->alias]['operation_id']), array('Path.order' => 'DESC')) +1;
		if (extension_loaded('imagick')) {
		
			$image = new Imagick(PDF_PATH.DS.$this->data[$this->alias]['file_hash'].'.pdf');
			$res = $image->getImageGeometry();
			$this->data[$this->alias]['height'] = $res['height'];
			$this->data[$this->alias]['width'] = $res['width'];
			$image->setResolution(150,150);
			$image->setImageFormat('png');
			$image->setImageBackgroundColor('white');
			$image = $image->flattenImages();
			$image->writeImage(PDF_PATH.DS.$this->data[$this->alias]['file_hash'].'.png');
		} else {
			copy(PDF_PATH.DS.'no-image.png', PDF_PATH.DS.$this->data[$this->alias]['file_hash'].'.png');
		}
		return true;
		
		
	}

	public function validateImportPreset($check) {
		if ((empty($this->data[$this->alias]['preset_id']) || $this->data[$this->alias]['preset_id'] == 1)) {
			return true;
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
	
	public function beforeDelete($cascade = true) {
		$operation_id = $this->field('operation_id');
		$this->recursive = -1;
		$paths = $this->find('all', array(
				'conditions' => array(
					'Path.operation_id' => $operation_id,
					'Path.id <>' => $this->id,
				),
				'order' => array(
						'Path.order' => 'ASC'
				)
		));
		foreach ($paths as $pi => $path) {
			$this->id = $path['Path']['id'];
			$this->saveField('order', $pi+1);
		}
	}
}
