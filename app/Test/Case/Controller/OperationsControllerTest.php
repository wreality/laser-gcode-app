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
	);

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

		$this->testAction('/operation/delete/101');
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

		$this->testAction('/operation/delete/101');
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

		$this->testAction('/operation/copy/101');
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

		$this->testAction('/operation/copy/101');
		$this->assertNotEmpty($this->headers['Location']);
	}
}
