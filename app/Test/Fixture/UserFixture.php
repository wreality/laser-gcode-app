f<?php
/**
 * UserFixture
 *
 */
class UserFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $import = 'User';

	public $records = array(
		array(
			'id' => '101',
			'username' => 'test1',
			'email' => 'test@example.com',
			'password' => '',
			'admin' => '0',
			'active' => '1',
			'validate_key' => '',
			'validate_data' => '',
			'created' => '2014-01-01 00:00:00',
			'modified' => '2014-01-01 00:00:00',
			'last_login' => '0000-00-00 00:00:00',
			'project_count' => '0',
			'public_count' => '0',
		),
		array(
			'id' => '102',
			'username' => 'test2',
			'email' => 'test2@example.com',
			'password' => '',
			'admin' => '0',
			'active' => '1',
			'validate_key' => '',
			'validate_data' => '',
			'created' => '2014-01-01 00:00:00',
			'modified' => '2014-01-01 00:00:00',
			'last_login' => '0000-00-00 00:00:00',
			'project_count' => '0',
			'public_count' => '0',
		),
	);
}
