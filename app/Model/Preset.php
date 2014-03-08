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
		'power' => array(
			'over0' => array(
				'rule' => array('comparison', '>', 0),
				'message' => 'Power must be between 0%% and 100%%',
			),
			'lessequal100' => array(
				'rule' => array('comparison', '<=', 100),
				'message' => 'Power must be between 0%% and 100%',		),
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

	public $belongsTo = array('User');
	
	public $virtualFields = array(
		'displayName' => 'IF(user_id IS NULL, CONCAT("Global: ", name), name)',
		'isGlobal' => 'user_id IS NULL',
	);
	
	public $displayField = 'displayName';
	
/**
 * getList method
 *
 * Get list of presets for populating combo boxes
 * 
 * @param User $user_id
 * @return array
 */
	public function getList($user_id = null) {
		if (!empty($user_id)) {
			$conditions = array('OR' => 	array(
				'Preset.user_id =' => $user_id,
				'Preset.user_id' => null
			));
		} else {
			$conditions = array('Preset.user_id' => null);
		}
		$order = array(
			'Preset.user_id DESC',
			'Preset.name'
		);
		$presets = $this->find('list', array('conditions' => $conditions, 'order' => $order));
		$presets[1] = 'Custom';
		return $presets;
	}

/**
 * isOwner method
 *
 * Returns true if supplied user is owner of this object.
 * 
 * @param unknown $user_id
 * @param string $id
 * @return boolean
 */
	public function isOwner($user_id, $id = null) {
		if (empty($id)) {
			$id = $this->id;
		}
		
		return ($this->field('user_id', array('id' => $id)) == $user_id);
	}

/**
 * makeGlobal method
 *
 * Make a given preset id Global by removing the user_id.
 * 
 * @param string $id
 * @return Ambigous <mixed, boolean, multitype:>
 */
	public function makeGlobal($id = null) {
		if (empty($id)) {
			$id = $this->id;
		}
		
		return $this->save(array('Preset' => array('id' => $id, 'user_id' => null)));
	}
}
