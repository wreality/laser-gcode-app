<?php

class AllTestsTest extends CakeTestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('All Application Tests');
		$suite->addTestFile(TESTS . 'Case/Model/UserTest.php');
		return $suite;
	}
}