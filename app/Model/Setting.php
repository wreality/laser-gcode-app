<?php
App::uses('AppModel', 'Model');
/**
 * Setting Model
 *
 */
class Setting extends AppModel {
/**
 * Primary key field
 *
 * @var 
 */
	public $primaryKey = 'key';
	
	
	protected $managedSettings = array(
		
		
		
		
		array(
			'key' => 'App.default_max_cut_feedrate',
			'type' => 'text',
			'title' => 'Default max cut feedrate',
			'units' => 'mm/min',
		), array(
			'key' => 'App.default_traversal_feedrate',
			'type' => 'text',
			'title' => 'Default traversal feedrate',
			'units' => 'mm/min',
		), array (
			'key' =>  'debug',
			'type' =>  'enum',
			'validate' => '/^[0-3]$/',
			'title' =>  'Debug Value',
			'help_text' =>  'Set to Production mode for public installations.  Debug levels disable model caching and display SQL log and stack trace.',
			'enum_data' =>  'a:4:{i:0;s:15:"Production Mode";i:1;s:20:"Show Errors/Warnings";i:2;s:28:"Show Errors/Warnings/SQL Log";i:3;s:14:"Developer Mode";}',
		), array(
			'key' => 'App.default_gcode_preamble',
			'type' => 'longtext',
			'title' => 'Default GCODE preamble'
		), array(
			'key' => 'App.default_gcode_postscript',
			'type' => 'longtext',
			'title' => 'Default GCODE postscript',
		), array(
			'key' => 'App.z_total',
			'type' => 'text',
			'title' => 'Total Z Height',
			'units' => 'mm',
		), array(
			'key' => 'App.focal_length',
			'type' => 'text',
			'title' => 'Focal length',
			'units' => 'mm',
		), array(
			'key' => 'App.z_feedrate',
			'type' => 'text',
			'title' => 'Z Move Feedrate',
			'units' => 'mm'
		),
	);
/**
 * Validation rules
 *
 * @var array
 */
	
	public function updateSettings() {
		foreach ($this->managedSettings as $setting) {
			$data = array();
			if ($key = $this->findByKey($setting['key'])) {
				$data['Setting'] = array_merge($key['Setting'], $setting);
				$this->save($data);
			} else {
				$this->create();
				$data['Setting'] = $setting;
				$this->save($data);
			}
		}
		Cache::delete('settings');
	}
	public function getSettings() {
		
		$settings = Cache::read('settings');
		if ($settings === false) {
			$settings = $this->find('all');
			Cache::write('settings', $settings);
		}
	
		foreach ($settings as $setting) {
			if (($setting['Setting']['value'] != null)) {
				Configure::write($setting['Setting']['key'], $setting['Setting']['value']);
			}
		}
	}
	
	public function saveSetting($key, $value) {
		$data = array(
			'Setting' => array(
				'key' => $key,
				'value' => $value,
			)
		);
		$this->save($data);
	}
	
	public function afterSave($created) {
		Cache::delete('settings');
	}
	
}
