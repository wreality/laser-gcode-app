<?php
App::uses('ProjectsController', 'Controller');

/**
 * ProjectsController Test Case
 *
 * @coversDefaultClass ProjectController
 */
class ProjectsControllerTest extends ControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.project',
		'app.operation',
		'app.path',
		'app.setting',
		'app.preset',
		'app.user',
		'app.session'
	);

/**
 * testNewProjectAnonymous method
 *
 * New projects created anonymously
 *
 * @covers ::add
 */
	public function testNewProjectAnonymous() {
		$Projects = $this->generate('Projects', array(
			'models' => array(
			),
			'components' => array(
				'Auth' => array('user'),
			)
		));

		$Projects->Auth
			->staticExpects($this->once())
			->method('user')
			->with('id')
			->will($this->returnValue(false));

		$this->testAction('/projects/add');
		$this->assertNotEmpty($this->headers['Location']);
		$this->assertRegExp('%/projects/edit/(.*)+$%', $this->headers['Location']);
		$this->assertTrue($Projects->Project->isAnonymous($Projects->Project->id));
	}

/**
 * testNewProjectUser method
 *
 * New projects created with a user attached.
 *
 * @covers ::add
 */
	public function testNewProjectUser() {
		$Projects = $this->generate('Projects', array(
			'components' => array(
				'Auth' => array('user')
			)
		));
		$Projects->Auth
			->staticExpects($this->once())
			->method('user')
			->with('id')
			->will($this->returnValue('101'));

		$this->testAction('/projects/add');
		$this->assertNotEmpty($this->headers['Location']);
		$this->assertRegExp('%/projects/edit/(.*)+%', $this->headers['Location']);
		$this->assertTrue($Projects->Project->isOwner('101', $Projects->Project->id));
	}

	public function testMakeClaim() {
		$Projects = $this->generate('Projects', array(
			'components' => array(
				'Auth' => array('user', '_isAllowed'),
				'Session' => array('setFlash'),
			)
		));

		$Projects->Auth
			->expects($this->once())
			->method('_isAllowed')
			->will($this->returnValue(true));
		$Projects->Auth
			->staticExpects($this->any())
			->method('user')
			->with('id')
			->will($this->returnValue('101'));
		$Projects->Session
			->expects($this->once())
			->method('setFlash')
			->with($this->anything(), 'bs_success');
		$this->testAction('/projects/make_claim/999');
		$this->assertNotEmpty($this->headers['Location']);
	}

	public function testMakeClaimAlreadyClaimed() {
		$Projects = $this->generate('Projects', array(
			'components' => array(
				'Auth' => array('user', '_isAllowed'),
				'Session' => array('setFlash'),
			)
		));

		$Projects->Auth
			->expects($this->once())
			->method('_isAllowed')
			->will($this->returnValue(true));
		$Projects->Auth
			->staticExpects($this->any())
			->method('user')
			->with('id')
			->will($this->returnValue('101'));
		$Projects->Session
			->expects($this->once())
			->method('setFlash')
			->with($this->anything(), 'bs_error');
		$this->testAction('/projects/make_claim/101');
	}
}
