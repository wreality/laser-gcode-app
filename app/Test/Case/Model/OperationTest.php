<?php
App::uses('Operation', 'Model');

/**
 * Operation Test Case
 *
 */
class OperationTest extends CakeTestCase {

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
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Operation = ClassRegistry::init('Operation');
		$this->storagePath = TMP . DS . 'tests' . DS . 'files';
		Configure::write('LaserApp.storage_path', $this->storagePath);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Operation);

		parent::tearDown();
	}

	public function testGCodeRates() {
		try {
			$result = $this->Operation->generateGCode('101');
		} catch (InternalErrorException $e) {
			$this->markTestSkipped();
			return;
		}

		$this->assertTrue($result);
		$this->assertTrue(file_exists($this->storagePath . DS . '101.gcode'));

		$gCode = $this->getGCode('101');

		$this->assertTrue($this->findInArray($gCode, '/^G0(.*)F4000$/'));
		$this->assertTrue($this->findInArray($gCode, '/^G1(.*)F1000$/'));
		$this->assertTrue($this->findInArray($gCode, '/^M3 S100$/'));

		unlink($this->storagePath . DS . '101.gcode');
	}

	public function testHomeGCode() {
		$data = array('Project' => array(
			'id' => '101',
			'home_before' => true
		));

		$this->assertArrayHasKey('Project', $this->Operation->Project->save($data, false));

		try {
			$result = $this->Operation->generateGCode('101');
		} catch (InternalErrorException $e) {
			$this->markTestSkipped();
			return;
		}

		$this->assertTrue($result);
		$gCode = $this->getGCode('101');
		$this->assertTrue($this->findInArray($gCode, '/^G28 X0 Y0 F[\d]+$/'));

		unlink($this->storagePath . DS . '101.gcode');
	}

	public function testPreamble() {
		$data = array('Project' => array(
			'id' => '101',
			'gcode_preamble' => '; Foo',
		));

		$this->assertArrayHasKey('Project', $this->Operation->Project->save($data, false));

		try {
			$result = $this->Operation->generateGCode('101');
		} catch (InternalErrorException $e) {
			$this->markTestSkipped();
			return;
		}

		$this->assertTrue($result);
		$gCode = $this->getGCode('101');
		$this->assertTrue($this->findInArray($gCode, '/^; Foo$/', $lineNumber));
		$this->assertEquals(0, $lineNumber);
	}

	public function testPostscript() {
		$data = array('Project' => array(
			'id' => '101',
			'gcode_postscript' => '; Bar',
		));

		$this->assertArrayHasKey('Project', $this->Operation->Project->save($data, false));

		try {
			$result = $this->Operation->generateGCode('101');
		} catch (InternalErrorException $e) {
			$this->markTestSkipped();
			return;
		}

		$this->assertTrue($result);

		$gCode = $this->getGCode('101');

		$this->assertTrue($this->findInArray($gCode, '/^; Bar$/', $lineNumber));
		$this->assertEquals(count($gCode) - 1, $lineNumber);
	}

	public function testCopyOperation() {
		$result = $this->Operation->copyOperation('101');
		$this->Operation->contain(array('Path'));
		$result = $this->Operation->read();

		$this->assertNotEmpty($result['Operation']['id']);
		$this->assertCount(1, $result['Path']);
	}

	public function getGCode($operationId) {
		return file($this->storagePath . DS . $operationId . '.gcode', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	}

	public function findInArray($haystack, $regex, &$lineNumber = null) {
		foreach ($haystack as $lineNumber => $line) {
			$match = preg_match($regex, $line);
			if ($match == 1) {
				return true;
			}
		}
		return false;
	}
}
