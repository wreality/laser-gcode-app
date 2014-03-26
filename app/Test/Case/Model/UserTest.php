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
			'username' => 'test',
			'email' => 'test@example.com',
			'password' => 'blah',
			'confirm_password' => 'foo',
		));
		$result = $this->User->save($data);
		$this->assertFalse($result, 'Non-matching confirmation saved (create).');
		
		$data['User']['confirm_password'] = '';
		$result = $this->User->save($data);
		$this->assertFalse($result, 'Empty confirmation saved (create).');
		
		$data['User']['confirm_password'] = 'blah';
		$result = $this->User->save($data); 
		$this->assertArrayHasKey('id', $result['User'], 'Correct confirmation not saved (create).');
		
		$data['User']['id'] = $result['User']['id'];
		$data['User']['confirm_password'] = 'foo';
		
		$result = $this->User->save($data);
		$this->assertFalse($result, 'Non-matching confirmation saved (edit).');
		
		$data['User']['confirm_password'] = '';
		$result = $this->User->save($data);
		$this->assertFalse($result, 'Empty confirmation saved (edit)');
		
		$data['User']['confirm_password'] = 'blah';
		$result = $this->User->save($data);
		$this->assertArrayHasKey('id', $result['User'], 'Correct confirmation not saved (edit)');
	}
	
/**
 * testPasswordHashed method
 *
 * Test that passwords are being hashed when saved.
 *
 */
	public function testPasswordHashed() {
		$data = array('User' => array(
			'username' => 'test',
			'email' => 'test@example.com',
			'password' => 'blah',
			'confirm_password' => 'blah',
		));
		
		$result = $this->User->save($data);
		
		$this->assertNotEmpty($result['User']['password'], 'Password saved empty.');
		$this->assertNotContains($data['User']['password'], $result['User']['password'], 'Password saved in plaintext');
		
		$data['User']['id'] = $result['User']['id'];
		$data['User']['password'] = $data['User']['confirm_password'] = 'foo';
		$result2 = $this->User->save($data);
		
		$this->assertNotEmpty($result2['User']['password'], 'Password saved empty (edit');
		$this->assertNotContains($data['User']['password'], $result2['User']['password'], 'Password saved in plaintext (edit).');
		$this->assertNotEqual($result2['User']['password'], $result['User']['password'], 'Passwork hases collide.');
		
	}
	
	public function testValidationKey() {
		$result = $this->User->createValidationKey('v');
		
		$this->assertStringStartsWith('v:', $result);
		
		$this->assertEqual(strlen($result), 42);
	}

}
