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

/**
 * testCreateNewProject method
 *
 * New projects should be created.
 *
 * @covers ::save
 */
	public function testCreateNewProject() {
		$this->Project->create();
		$result = $this->Project->save(array('Project' => array('user_id' => '192.1.1.1')));

		$this->assertArrayHasKey('Project', $result);
	}

/**
 * testNewProjectSystemDefaults method
 *
 * New projects should use system defaults.
 *
 * @covers ::getDefaults
 */
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

/**
 * testNewProjectUserDefaults method
 *
 * New projects should use user defaults if they exist.
 *
 * @covers ::saveDefaults
 * @covers ::getDefaults
 */
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

/**
 * testIsOwner method
 *
 * Return true if a user is the owner of a project.
 *
 * @covers ::isOwner
 */
	public function testIsOwner() {
		$this->Project->create();
		$result = $this->Project->save(array('Project' => array('user_id' => '101')));
		$this->Project->id = $result['Project']['id'];

		$this->assertTrue($this->Project->isOwner('101'));
		$this->assertFalse($this->Project->isOwner('102'));
	}

/**
 * testIsOwnerOrPublic method
 *
 * Return true is a project is owned by a user or if the project is public.
 *
 * @covers ::isOwnerOrPublic
 */
	public function testIsOwnerOrPublic() {
		$this->Project->create();
		$this->Project->save(array('Project' => array('user_id' => '101')));

		$this->assertFalse($this->Project->isOwnerOrPublic('102'));
		$this->assertTrue($this->Project->isOwnerOrPublic('101'));

		$this->Project->save(array('Project' => array('public' => Project::PROJ_PUBLIC)));

		$this->assertTrue($this->Project->isOwnerOrPublic('101'));
		$this->assertTrue($this->Project->isOwnerOrPublic('102'));
	}

/**
 * testIsPublic method
 *
 * Return true if a project is public.
 *
 * @covers ::isPublic
 */
	public function testIsPublic() {
		$this->assertFalse($this->Project->isPublic('101'));
		$this->assertTrue($this->Project->isPublic('101-Public'));
	}

/**
 * testIsAnonymous method
 *
 * Return true if a project is not owned by a user.
 *
 * @covers ::isAnonymous
 */
	public function testIsAnonymous() {
		$this->assertFalse($this->Project->isAnonymous('101'));
		$this->assertTrue($this->Project->isAnonymous('999'));
	}

/**
 * testResetUserDefaults method
 *
 * User defaults should be deleted, allowing system defaults to take effect.
 *
 * @covers ::resetUserDefaults
 */
	public function testResetUserDefaults() {
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
		$this->Project->resetUserDefaults('101');
		$this->Project->create();
		$result = $this->Project->save(array('Project' => array('user_id' => '101')));

		$this->assertEqual($result['Project']['max_feedrate'], Configure::read('LaserApp.default_max_cut_feedrate'));
		$this->assertEqual($result['Project']['traversal_rate'], Configure::read('LaserApp.default_traversal_feedrate'));
		$this->assertEqual($result['Project']['home_before'], true);
		$this->assertEqual($result['Project']['clear_after'], false);
		$this->assertEqual($result['Project']['gcode_preamble'], Configure::read('LaserApp.default_gcode_preamble'));
		$this->assertEqual($result['Project']['gcode_postscript'], Configure::read('LaserApp.default_gcode_postscript'));
	}

/**
 * testUpdateModified method
 *
 * Update modified should update the modified date.
 *
 * @covers ::updateModified
 */
	public function testUpdateModified() {
		$this->Project->create();
		$this->Project->save(array('Project' => array('user_id' => '', 'modified' => '1971-01-01 00:00:00')));

		$result = $this->Project->updateModified();
		$now = new DateTime();
		$modified = new DateTime($result['Project']['modified']);
		$interval = $modified->diff($now);
		$seconds = $interval->format('%S');
		$this->assertLessThan(10, $seconds);
		$this->assertGreaterThanOrEqual(0, $seconds);
	}

/**
 * testCopyProject method
 *
 * Copy project to a new project.
 *
 * @covers ::copyProject
 */
	public function testCopyProject() {
		$result = $this->Project->copyProject('Project Copy', '101');
		$this->Project->contain(array(
			'Operation' => array(
				'Path'
			)
		));
		$result = $this->Project->read();

		$this->assertNotEmpty($result['Project']['id']);
		$this->assertEquals('Project Copy', $result['Project']['project_name']);
		$this->assertCount(1, $result['Operation']);
		$this->assertCount(1, $result['Operation'][0]['Path']);
	}
}
