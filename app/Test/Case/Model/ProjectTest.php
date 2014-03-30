<?php
App::uses('Project', 'Model');

/**
 * Project Test Case
 *
 */
class ProjectTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.user',
		'app.project',
		'app.operation',
		'app.path'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Project = ClassRegistry::init('Project');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Project);

		parent::tearDown();
	}

	public function testCreateNewProject() {
		$this->Project->create();
		$result = $this->Project->save(array('Project' => array('user_id' => '192.1.1.1')));

		$this->assertArrayHasKey('Project', $result);
	}

	public function testNewProjectSystemDefaults() {
		$this->Project->create();
		$result = $this->Project->save(array('Project' => array('user_id' => '')));

		$this->assertEqual($result['Project']['max_feedrate'], Configure::read('LaserApp.default_max_cut_feedrate'));
		$this->assertEqual($result['Project']['traversal_rate'], Configure::read('LaserApp.default_traversal_feedrate'));
		$this->assertEqual($result['Project']['home_before'], true);
		$this->assertEqual($result['Project']['clear_after'], false);
		$this->assertEqual($result['Project']['gcode_preamble'], Configure::read('LaserApp.default_gcode_preamble'));
		$this->assertEqual($result['Project']['gcode_postscript'], Configure::read('LaserApp.default_gcode_postscript'));
	}

	public function testNewProjectUserDefaults() {
		$defaults = array('Project' => array(
			'max_feedrate' => 4000,
			'traversal_rate' => 299,
			'home_before' => false,
			'clear_after' => true,
			'gcode_preamble' => 'Foo',
			'gcode_postscript' => 'Bar',
		));

		$result = $this->Project->saveDefaults('101', $defaults);
		$this->assertArrayHasKey('Project', $result);

		$this->Project->create();
		$result = $this->Project->save(array('Project' => array('user_id' => '101')));

		$this->assertEqual($result['Project']['max_feedrate'], 4000);
		$this->assertEqual($result['Project']['traversal_rate'], 299);
		$this->assertEqual($result['Project']['home_before'], false);
		$this->assertEqual($result['Project']['clear_after'], true);
		$this->assertEqual($result['Project']['gcode_preamble'], 'Foo');
		$this->assertEqual($result['Project']['gcode_postscript'], 'Bar');
	}
}
