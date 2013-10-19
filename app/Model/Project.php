<?php
App::uses('AppModel', 'Model');
/**
 * Project Model
 *
 * @property Operation $Operation
 */
class Project extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed

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
/*
	public $validate = array(
		'material_thickness' => array(
			'validThickness' => array(
				'rule' => array('validateMaterialThickness'),
			)
		),
	);
	*/
	
	public function beforeSave($options = array()) {
		if (empty($this->id) && empty($this->data[$this->alias]['id'])) {
			$this->data[$this->alias]['max_feedrate'] = Configure::read('App.default_max_cut_feedrate');
			$this->data[$this->alias]['traversal_rate'] = Configure::read('App.default_traversal_feedrate');
			$this->data[$this->alias]['home_before'] = true;
			$this->data[$this->alias]['clear_after'] = true;
			$this->data[$this->alias]['gcode_preamble'] = Configure::read('App.default_gcode_preamble');
			$this->data[$this->alias]['gcode_postscript'] = Configure::read('App.default_gcode_postscript');
		}
		return true;
	}

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
}
