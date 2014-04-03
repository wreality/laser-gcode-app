<?php
/**
 * AllTestsTest class
 *
 * @author wreality
 * @codeCoverageIgnore
 */
class AllTestsTest extends CakeTestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('All Application Tests');
		$suite->addTestFile(TESTS . 'Case/Model/UserTest.php');
		$suite->addTestFile(TESTS . 'Case/Model/ProjectTest.php');
		$suite->addTestFile(TESTS . 'Case/Model/OperationTest.php');
		$suite->addTestFile(TESTS . 'Case/Controller/OperationsControllerTest.php');
		return $suite;
	}
}