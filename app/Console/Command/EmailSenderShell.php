<?php 

	App::uses('AppShell', 'Console/Command');

class EmailSenderShell extends AppShell {

/**
 * send method
 * 
 * Send emails (invoked by resque scheduler)
 * 
 * @throws Exception
 */
	public function send() {
		$class = ClassRegistry::init($this->args[0]);

		try {
			$class->{'email' . $this->args[1]}($this->args[2]);
		} catch (Exception $e) {
			if (class_exists('CakeResque')) {
				$cacheKey = 'email_failure_' . $this->args[2];
				$count = Cache::read($cacheKey);
				if ($count === false) {
					$count = 1;
					if (Cache::write($cacheKey, $count) === false) {
						throw $e; //Rethrow the error and don't requeue.
					}
				} else {
					$count = Cache::increment($cacheKey, 1);
				}

				if ($count <= Configure::read('App.max_email_retries')) {
					LogError('EMail sending failure (retry queued): ' . $this->args[0] . '.' . $this->args[1] . ' to ' . $this->args[2]);
					CakeResque::enqueueIn(30, 'default', 'EmailSenderShell', array('send', $this->args[0], $this->args[1], $this->args[2]));
				} else {
					LogError('Max retries exceeded sending email: ' . $this->args[0] . '.' . $this->args[1] . ' to ' . $this->args[2]);
				}
			}
			throw $e;// Rethrow so the queue shows the failed job.
		}
	}
}