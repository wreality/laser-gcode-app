<?php
App::uses('Project', 'Model');
/**
 * ProjectFixture
 *
 */
class ProjectFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $import = 'Project';

	public $records = array(
		array(
			'id' => '101',
			'project_name' => 'Test Project',
			'max_feedrate' => '1000',
			'traversal_rate' => '4000',
			'home_before' => false,
			'gcode_preamble' => '',
			'gcode_postscript' => '',
			'clear_after' => false,
			'material_thickness' => '10',
			'created' => '1971-01-01 00:00:00',
			'modified' => '1971-01-01 00:00:00',
			'operation_count' => '1',
			'user_id' => '101',
			'public' => Project::PROJ_PRIVATE,
		)
	);
}
