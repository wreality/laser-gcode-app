<?php
/**
 * OperationFixture
 *
 */
class OperationFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $import = 'Operation';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '101',
			'project_id' => '101',
			'size_warning' => false,
			'order' => 1,
		),
		array(
			'id' => '101-Public',
			'project_id' => '101-Public',
			'size_warning' => false,
			'order' => 1,
		),
	);

}
