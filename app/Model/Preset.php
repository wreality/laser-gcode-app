<?php
App::uses('AppModel', 'Model');
/**
 * Preset Model
 *
 */
class Preset extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	public function getList() {
		$presets = $this->find('list');
		$presets[1] = 'Custom';
		return $presets;
	}
}
