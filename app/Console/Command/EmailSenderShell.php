App::uses('AppShell', 'Console');

<?php
class EmailSenderShell extends AppShell {
	public $uses = array('User');
	
	public function sendValidationEmail() {
		$this->User->sendValidationEmail($this->args[0], false);
	}
}