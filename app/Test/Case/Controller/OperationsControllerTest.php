<?php
App::uses('OperationsController', 'Controller');

/**
 * OperationsController Test Case
 *
 */
class OperationsControllerTest extends ControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.operation',
		'app.project',
		'app.path',
		'app.user',
		'app.preset',
		'app.setting',
		'app.session',
	);

	public function setUp() {
		parent::setUp();
		$this->storagePath = TMP . DS . 'tests' . DS . 'files';
		Configure::write('LaserApp.storage_path', $this->storagePath);
	}

/**
 * testDeleteByOwner method
 *
 * @return void
 */
	public function testDeleteByOwner() {
		$Operation = $this->generate('Operations', array(
			'models' => array(
				'Operation' => array('delete')
			),
			'components' => array(
				'Auth' => array('user'),
				'Session' => array('setFlash')
			)
		));
		$Operation->Auth
			->staticExpects($this->once())
			->method('user')
			->with('id')
			->will($this->returnValue('101'));
		$Operation->Session
			->expects($this->once())
			->method('setFlash')
			->with($this->anything(), 'bs_success');
		$Operation->Operation
			->expects($this->once())
			->method('delete')
			->will($this->returnValue(true));

		$this->testAction('/operations/delete/101');
		$this->assertNoTEmpty($this->headers['Location']);
	}

/**
 * testDeleteByOther method
 *
 * @expectedException ForbiddenException
 * @return void
 */
	public function testDeleteByOther() {
		$Operation = $this->generate('Operations', array(
			'models' => array(
				'Operation' => array('delete')
			),
			'components' => array(
				'Auth' => array('user'),
				'Session' => array('setFlash')
			)
		));
		$Operation->Auth
			->staticExpects($this->once())
			->method('user')
			->with('id')
			->will($this->returnValue('102'));

		$this->testAction('/operations/delete/101');
	}

/**
 * testCopyByOther method
 *
 *
 * @expectedException ForbiddenException
 */
	public function testCopyByOther() {
		$Operation = $this->generate('Operations', array(
			'models' => array(
				'Operation' => array('copyOperation')
			),
			'components' => array(
				'Auth' => array('user'),
				'Session' => array('setFlash')
			)
		));
		$Operation->Auth
			->staticExpects($this->once())
			->method('user')
			->with('id')
			->will($this->returnValue('102'));

		$this->testAction('/operations/copy/101');
	}

	public function testCopy() {
		$Operation = $this->generate('Operations', array(
			'models' => array(
				'Operation' => array('copyOperation', 'updateOverview')
			),
			'components' => array(
				'Auth' => array('user'),
				'Session' => array('setFlash')
			)
		));

		$Operation->Auth
			->staticExpects($this->once())
			->method('user')
			->with('id')
			->will($this->returnValue('101'));
		$Operation->Operation
			->expects($this->once())
			->method('copyOperation')
			->will($this->returnValue(true));
		$Operation->Operation
			->expects($this->once())
			->method('updateOverview');
		$Operation->Session
			->expects($this->once())
			->method('setFlash')
			->with($this->anything(), 'bs_success');

		$this->testAction('/operations/copy/101');
		$this->assertNotEmpty($this->headers['Location']);
	}

	public function testAddOperationOwnProject() {
		$Operation = $this->generate('Operations', array(
			'models' => array(
				'Operation' => array('save')
			),
			'components' => array(
				'Auth' => array('user'),
				'Session' => array('setFlash')
			)
		));

		$Operation->Auth
			->staticExpects($this->once())
			->method('user')
			->with('id')
			->will($this->returnValue('101'));
		$Operation->Operation
			->expects($this->once())
			->method('save')
			->will($this->returnValue(true));
		$Operation->Session
			->expects($this->once())
			->method('setFlash')
			->with($this->anything(), 'bs_success');

		$this->testAction('/operations/add/101');
		$this->assertNotEmpty($this->headers['Location']);
	}

/**
 * testAddProjectNotOwnProject method
 *
 * @expectedException ForbiddenException
 *
 */
	public function testAddOperationNotOwnProject() {
		$Operation = $this->generate('Operations', array(
			'models' => array(
				'Operation' => array('save')
			),
			'components' => array(
				'Auth' => array('user'),
				'Session'
			)
		));

		$Operation->Auth
			->staticExpects($this->once())
			->method('user')
			->with('id')
			->will($this->returnValue('102'));

		$this->testAction('/operations/add/101');
	}

/**
 * testOperationAddAnon method
 *
 * @expectedException ForbiddenException
 *
 */
	public function testOperationAddNotLoggedInOwnedProject() {
		$Operation = $this->generate('Operations', array(
			'models' => array(
				'Operation' => array('save')
			),
			'components' => array(
				'Auth' => array('user'),
				'Session' => array('setFlash')
			)
		));

		$Operation->Auth
			->staticExpects($this->once())
			->method('user')
			->will($this->returnValue(false));

		$this->testAction('/operations/add/101');
	}

	public function testOperationAddAnonAnonProject() {
		$Operation = $this->generate('Operations', array(
			'models' => array(
				'Operation' => array('save'),
			),
			'components' => array(
				'Auth' => array('user'),
				'Session' => array('setFlash')
			)
		));

		$Operation->Auth
			->staticExpects($this->once())
			->method('user')
			->with('id')
			->will($this->returnValue(false));
		$Operation->Operation
			->expects($this->once())
			->method('save')
			->will($this->returnValue(true));

		$result = $this->testAction('/operations/add/999');
		$this->assertStringEndsWith('projects/edit/999', $this->headers['Location']);
	}

	public function testPreviewOwnProject() {
		$Operation = $this->generate('Operations', array(
			'components' => array(
				'Auth' => array('user'),
				'Session'

			)
		));

		$Operation->Auth
			->staticExpects($this->once())
			->method('user')
			->with('id')
			->will($this->returnValue('101'));


		touch($Operation->Operation->getGCodeFilename('101'));
		$result = $this->testAction('/operations/preview/101', array('return' => 'vars'));
		$this->assertEqual($result['operation_id'], '101');
		$Operation->Operation->deleteGCode('101');
	}

/**
 * testOperationAddAnon method
 *
 * @expectedException ForbiddenException
 *
 */
	public function testNotPreviewAnothersOperation() {
		$Operation = $this->generate('Operations', array(
			'components' => array(
				'Auth' => array('user'),
				'Session'
			)
		));

		$Operation->Auth
			->staticExpects($this->once())
			->method('user')
			->will($this->returnValue('102'));

		$this->testAction('/operations/preview/101');
	}

	public function testPreviewPublicOperation() {
		$Operation = $this->generate('Operations', array(
			'components' => array(
				'Auth' => array('user'),
				'Session',
			),
		));

		$Operation->Auth
			->staticExpects($this->once())
			->method('user')
			->will($this->returnValue(false));

		touch($Operation->Operation->getGCodeFilename('101-Public'));
		$result = $this->testAction('/operations/preview/101-Public', array('return' => 'vars'));
		$Operation->Operation->deleteGCode('101-Public');

		$this->assertEquals('101-Public', $result['operation_id']);
	}

	public function testDownloadOwnOperation() {
		return $this->markTestIncomplete();
		$Operation = $this->generate('Operations', array(
			'components' => array(
				'Auth' => array('user'),
				'Session',
			),
		));

		$Operation->Auth
			->staticExpects($this->once())
			->method('user')
			->will($this->returnValue('101'));
		$Operation->response
			->expects($this->once())
			->method('file')
			->will($this->returnValue(true));
		$Operation->Operation->generateGCode('101');
		$result = $this->testAction('/operations/download/101');
		$Operation->Operation->deleteGCode('101');
		$this->assertStringStartsWith('attachment; filename=', $this->headers['Content-Disposition']);
	}

/**
 * testNotDownloadAnothersOperation method
 *
 * @expectedException ForbiddenException
 *
 */
	public function testNotDownloadAnothersOperation() {
		$Operation = $this->generate('Operations', array(
			'components' => array(
				'Auth' => array('user'),
				'Session'
			)
		));

		$Operation->Auth
			->staticExpects($this->once())
			->method('user')
			->will($this->returnValue('102'));

		$this->testAction('/operations/download/101');
	}

	public function testDownloadPublicOperation() {
		return $this->markTestIncomplete();
		$Operation = $this->generate('Operations', array(
			'components' => array(
				'Auth' => array('user'),
				'Session',
			),
		));

		$Operation->Auth
			->staticExpects($this->once())
			->method('user')
			->will($this->returnValue(false));
		$Operation->response
			->expects($this->once())
			->method('file')
			->will($this->returnValue(true));

		$Operation->Operation->generateGCode('101-Public');
		$this->testAction('/operations/download/101-Public');
		$Operation->Operation->deleteGCode('101-Public');

		$this->assertStringStartsWith('attachment; filename=', $this->headers['Content-Disposition']);
	}
}

