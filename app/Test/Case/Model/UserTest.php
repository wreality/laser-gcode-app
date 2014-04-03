<?php
App::uses('User', 'Model');

/**
 * User Test Case
 *
 * @coversDefaultClass User
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
 * Test that password confirmations are correctly saved when creating or editing
 * a user.
 * 
 * @covers ::newUser
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

/**
 * testNoDuplicateEmailOnCreate method
 *
 * Test that new users are not allowed to be created with an existing email
 * address.
 * 
 * @covers ::newUser
 */
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

/**
 * testNoDuplicateEmailOnUpdate method
 *
 * Test that users can't be updated with an existing email address.
 *
 * @covers ::updateEmail
 */
	public function testNoDuplicateEmailOnUpdate() {
		$data = array('User' => array(
			'id' => '102',
			'email' => 'test@example.com',
		));

		$result = $this->User->updateEmail($data);
		$this->assertFalse($result);
	}

/**
 * testNoDuplicateUsernameOnCreate method
 *
 * Test that new users are not alloweed to be created with an existing username.
 * 
 * @covers ::newUser
 */
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

/**
 * testNoDuplicateUsernameOnUpdate method
 *
 * Test that modified users cannot reuse an existing username.
 *
 * @covers ::updateUsername
 */
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
 * @covers ::beforeSave
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
 * @covers ::createValidationKey
 */
	public function testValidationKey() {
		$result = $this->User->createValidationKey('v');

		$this->assertStringStartsWith('v:', $result);
		$this->assertEqual(strlen($result), 42);
	}

/**
 * testCreateUserValidationKey method
 *
 * Validation key is created on user create.
 *
 * @covers ::newUser
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
 * Returns validation key prefixed by key type.
 * 
 * @covers ::findByValidationKey
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

/**
 * testSecretKeyRequired method
 *
 * Secret key must be provided and be correct if feature is enabled.
 * 
 * @covers ::newUser
 */	
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

/**
 * testRequireCurrentPassword method
 *
 * Require current password add validation rule requiring current password on
 * save.
 * 
 * @covers ::requireCurrentPassword
 */
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

/**
 * testSendValidationEmailNoResque method
 *
 * Send validation email when resque is not configured.
 * 
 * @covers ::enqueueEmail
 * @covers ::emailValidation
 */
	public function testSendValidationEmailNoResque() {
		if (class_exists('CakeResque')) {
			$this->markTestSkipped(__('Resque is configured.'));
		}

		$User = $this->getUserMockForEmail('verify_email', 'test@example.com');
		$User->enqueueEmail('Validation', '101');
	}

/**
 * testSendResetPasswordEmailNoResque method
 *
 * Send password reset email when resque is not configured.
 * 
 * @covers ::enqueueEmail
 * @covers ::emailResetPassword
 */
	public function testSendResetPasswordEmailNoResque() {
		if (class_exists('CakeResque')) {
			$this->markTestSkipped();
		}

		$User = $this->getUserMockForEmail('reset_password', 'test@example.com');
		$User->enqueueEmail('ResetPassword', '101');
	}

/**
 * testSendUpdateEmailEmailNoResque method
 *
 * Send update email verification when resque is not configured.
 *
 * @covers ::enqueueEmail
 * @covers ::emailUpdateEmail
 */
	public function testSendUpdateEmailEmailNoResque() {
		if (class_exists('CakeResque')) {
			$this->markTestSkipped();
		}

		$User = $this->getUserMockForEmail('update_email', 'update@example.com');
		$data = array('User' => array(
			'id' => '101',
			'validate_key' => $User->createValidationKey('u'),
			'validate_data' => 'update@example.com'
		));
		$User->saveValidateData($data);
		$User->enqueueEmail('UpdateEmail', '101');
	}

/**
 * testSendInvalidateEmailNoResque method
 *
 * Send invalidate email when resque is not configured.
 *
 * @covers ::enqueueEmail
 * @covers ::emailInvalidatePassword
 */
	public function testSendInvalidateEmailNoResque() {
		if (class_exists('CakeResque')) {
			$this->markTestSkipped();
		}

		$User = $this->getUserMockForEmail('invalidate_password', 'test@example.com');
		$User->enqueueEmail('InvalidatePassword', '101');
	}

/**
 * getUserMockForEmail method
 *
 * Return a mock user object prepared to mock email functions.
 * 
 * @param string $template
 * @param string $emailAddress
 * @return Ambigous <Model, PHPUnit_Framework_MockObject_MockObject, object, mixed>
 */
	public function getUserMockForEmail($template, $emailAddress) {
		$User = $this->getMockForModel('User', array('_getMailer'));

		$mailer = $this->getMock('CakeEmail', array('to',
			'emailFormat',
			'subject',
			'replyTo',
			'from',
			'template',
			'viewVars',
			'send',
			'config'));

		$mailer->expects($this->once())
		->method('emailFormat')
		->will($this->returnSelf());

		$mailer->expects($this->once())
		->method('config')
		->will($this->returnSelf());

		$mailer->expects($this->once())
		->method('subject')
		->will($this->returnSelf());

		$mailer->expects($this->once())
		->method('viewVars')
		->will($this->returnSelf());

		$mailer->expects($this->once())
		->method('to')
		->with($emailAddress)
		->will($this->returnSelf());

		$mailer->expects($this->once())
		->method('template')
		->with($template)
		->will($this->returnSelf());

		$mailer->expects($this->once())
		->method('send')
		->will($this->returnValue(true));

		$User->expects($this->once())
		->method('_getMailer')
		->will($this->returnValue($mailer));

		return $User;
	}
}
