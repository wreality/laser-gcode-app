<?php
App::uses('Operation', 'Model');

/**
 * Operation Test Case
 *
 * @property Operation $Operation
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
		array_map('unlink', glob($this->storagePath . DS . '*.gcode'));
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Operation);
		array_map('unlink', glob($this->storagePath . DS . '*.gcode'));
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

		$this->Operation->deleteGCode('101');
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

		$this->Operation->deleteGCode('101');
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
		$this->Operation->deleteGCode('101');
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
		$this->Operation->deleteGCode('101');
	}

	public function testM80Exists() {
		try {
			$result = $this->Operation->generateGCode('101');
		} catch (InternalErrorException $e) {
			$this->markTestSkipped();
			return;
		}

		$this->assertTrue($result);
		$gCode = $this->getGCode('101');

		$this->assertTrue($this->findInArray($gCode, '/^M80/'));
		$this->Operation->deleteGCode('101');
	}

	public function testCopyOperation() {
		$result = $this->Operation->copyOperation('101');
		$this->Operation->contain(array('Path'));
		$result = $this->Operation->read();

		$this->assertNotEmpty($result['Operation']['id']);
		$this->assertCount(1, $result['Path']);
	}

	public function testGetGCodeFilename() {
		$result = $this->Operation->getGCodeFilename('101');

		$this->assertStringStartsWith(Configure::read('LaserApp.storage_path'), $result);
		$this->assertStringEndsWith('101.gcode', $result);

		$this->Operation->id = '101';
		$result = $this->Operation->getGCodeFilename('101');

		$this->assertStringStartsWith(Configure::read('LaserApp.storage_path'), $result);
		$this->assertStringEndsWith('101.gcode', $result);
	}

	public function testGCodeExists() {
		$this->Operation->id = '102';
		$this->assertFalse($this->Operation->gCodeExists('101'));
		$this->assertFalse($this->Operation->gCodeExists());

		touch($this->Operation->getGCodeFilename('101'));
		$this->assertTrue($this->Operation->gCodeExists('101'));

		touch($this->Operation->getGCodeFilename());
		$this->assertTrue($this->Operation->gCodeExists());

		$this->Operation->deleteGCode('101');
		$this->Operation->deleteGCode();
	}

	public function testGCodeDelete() {
		$this->Operation->id = '102';

		touch($this->Operation->getGCodeFilename());
		touch($this->Operation->getGCodeFilename('101'));

		$this->Operation->deleteGCode('101');
		$this->assertFalse(file_exists($this->Operation->getGCodeFilename('101')));

		$this->Operation->deleteGCode();
		$this->assertFalse(file_exists($this->Operation->getGCodeFilename()));
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
