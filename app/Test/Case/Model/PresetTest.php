<?php
App::uses('Preset', 'Model');

/**
 * Preset Test Case
 *
 */
class PresetTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.preset'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Preset = ClassRegistry::init('Preset');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Preset);

		parent::tearDown();
	}

}
