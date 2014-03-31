<?php
/**
 * PathFixture
 *
 */
class PathFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $import = 'Path';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '101',
			'operation_id' => '101',
			'file_hash' => 'testfile',
			'order' => '1',
			'file_name' => 'testfile',
			'power' => 100,
			'speed' => 100,
			'preset_id' => null,
			'height' => 10,
			'width' => 10,
		),
	);
}
