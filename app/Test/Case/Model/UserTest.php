<?php
App::uses('User', 'Model');

/**
 * User Test Case
 *
 */
class UserTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.user',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->User = ClassRegistry::init('User');
		$this->User->recursive = -1;
		Configure::write('LaserApp.user_secret_enabled', false);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->User);

		parent::tearDown();
	}

/**
 * testUserValidatePassword method
 *
 * Test that password confirmations are correctly saved when creating or editing a user.
 *
 */
	public function testUserValidatePassword() {
		$data = array('User' => array(
			'username' => 'emailtest',
			'email' => 'emailtest@example.com',
			'password' => 'blah',
			'confirm_password' => 'foo',
		));
		$result = $this->User->newUser($data);
		$this->assertFalse($result, 'Non-matching confirmation saved (create).');

		$data['User']['confirm_password'] = '';
		$result = $this->User->newUser($data);
		$this->assertFalse($result, 'Empty confirmation saved (create).');

		$data['User']['confirm_password'] = 'blah';
		$result = $this->User->newUser($data);
		$this->assertArrayHasKey('id', $result['User'], 'Correct confirmation not saved (create).');

		$data['User']['id'] = $result['User']['id'];
		$data['User']['confirm_password'] = 'foo';

		$result = $this->User->newUser($data);
		$this->assertFalse($result, 'Non-matching confirmation saved (edit).');

		$data['User']['confirm_password'] = '';
		$result = $this->User->save($data);
		$this->assertFalse($result, 'Empty confirmation saved (edit)');

		$data['User']['confirm_password'] = 'blah';
		$result = $this->User->save($data);
		$this->assertArrayHasKey('id', $result['User'], 'Correct confirmation not saved (edit)');
	}

	public function testNoDuplicateEmailOnCreate() {
		$data = array('User' => array(
			'username' => 'emailtest',
			'email' => 'test@example.com',
			'password' => 'blah',
			'confirm_password' => 'blah',
		));

		$result = $this->User->newUser($data);
		$this->assertFalse($result);
	}

	public function testNoDuplicateEmailOnUpdate() {
		$data = array('User' => array(
			'id' => '102',
			'email' => 'test@example.com',
		));

		$result = $this->User->updateEmail($data);
		$this->assertFalse($result);
	}

	public function testNoDuplicateUsernameOnCreate() {
		$data = array('User' => array(
			'username' => 'test1',
			'email' => 'testuser@example.com',
			'password' => 'blah',
			'confirm_password' => 'blah',
		));

		$result = $this->User->newUser($data);
		$this->assertFalse($result);
	}

	public function testNoDuplicateUsernameOnUpdate() {
		$data = array('User' => array(
			'id' => '102',
			'username' => 'test1'
		));

		$result = $this->User->updateUsername($data);
		$this->assertFalse($result);
	}
/**
 * testPasswordHashed method
 *
 * Test that passwords are being hashed when saved.
 *
 */
	public function testPasswordHashed() {
		$data = array('User' => array(
			'username' => 'testhash',
			'email' => 'testhash@example.com',
			'password' => 'blah',
			'confirm_password' => 'blah',
		));

		$result = $this->User->newUser($data);

		$this->assertNotEmpty($result['User']['password'], 'Password saved empty.');
		$this->assertNotContains($data['User']['password'], $result['User']['password'], 'Password saved in plaintext');

		$data['User']['id'] = $result['User']['id'];
		$data['User']['password'] = $data['User']['confirm_password'] = 'foo';
		$result2 = $this->User->updatePassword($data, false);

		$this->assertNotEmpty($result2['User']['password'], 'Password saved empty (edit');
		$this->assertNotContains($data['User']['password'], $result2['User']['password'], 'Password saved in plaintext (edit).');
		$this->assertNotEqual($result2['User']['password'], $result['User']['password'], 'Passwork hases collide.');
	}

/**
 * testValidationKey method
 *
 * Test that validation keys are generated correctly.
 *
 */
	public function testValidationKey() {
		$result = $this->User->createValidationKey('v');

		$this->assertStringStartsWith('v:', $result);
		$this->assertEqual(strlen($result), 42);
	}

/**
 * testCreateUserValidationKey method
 *
 * Test that validation key is created on user create.
 *
 */
	public function testCreateUserValidationKey() {
		$data = array('User' => array(
			'username' => 'testkey',
			'email' => 'testkey@example.com',
			'password' => 'blah',
			'confirm_password' => 'blah',
		));
		$result = $this->User->newUser($data);

		$this->assertNotEmpty($result['User']['validate_key']);
		$this->assertStringStartsWith('v:', $result['User']['validate_key']);
	}

/**
 * testFindByValidateKey method
 *
 */
	public function testFindByValidateKey() {
		$data = array('User' => array(
			'username' => 'testkey',
			'email' => 'testkey@example.com',
			'password' => 'blah',
			'confirm_password' => 'blah',
		));

		$result = $this->User->newUser($data);

		$result2 = $this->User->findByValidateKey('v', substr($result['User']['validate_key'], 2));

		$this->assertEqual($result2['User']['id'], $result['User']['id']);
	}

	public function testSecretKeyRequired() {
		$data = array('User' => array(
			'username' => 'testsecret',
			'email' => 'testsecret@example.com',
			'password' => 'blah',
			'confirm_password' => 'blah',
		));

		Configure::write('LaserApp.user_secret_enabled', true);
		Configure::write('LaserApp.user_secret', 'SECRET');

		$this->User->create();
		$result = $this->User->newUser($data);
		$this->assertFalse($result);

		$data['User']['user_secret'] = 'FOO';
		$result = $this->User->newUser($data);
		$this->assertFalse($result);

		$data['User']['user_secret'] = 'SECRET';
		$result = $this->User->newUser($data);
		$this->assertArrayHasKey('User', $result);
	}

	public function testRequireCurrentPassword() {
		$data = array('User' => array(
			'username' => 'testcurrent',
			'email' => 'testcurrent@example.com',
			'password' => 'blah',
			'confirm_password' => 'blah',
		));

		$result = $this->User->newUser($data);

		$this->User->requireCurrentPassword();
		unset($data['User']['password'], $data['User']['confirm_password']);
		$data['User']['id'] = $result['User']['id'];
		$result = $this->User->updatePassword($data);
		$this->assertFalse($result);

		$data['User']['current_password'] = 'blah';
		$result = $this->User->updatePassword($data);
		$this->assertArrayHasKey('User', $result);
	}
}
