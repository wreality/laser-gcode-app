<?php 
	
	App::uses('AppShell', 'Console/Command');

class EmailSenderShell extends AppShell {
	
	
	
	public function send() {
		$class = ClassRegistry::init($this->args[0]);
		
		$class->{'email'.$this->args[1]}($this->args[2]);
	}
	
	
}