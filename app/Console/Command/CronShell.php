App::uses('AppShell', 'Console');

<?php
class CronShell extends AppShell {

	public $uses = array('Project');

	public function daily() {
		$this->purgeEmptyProjects();
	}

	public function purgeEmptyProjects() {
		$list = $this->Project->find('list', array(
			'conditions' => array(
				'modified <=' => date('Y-m-d H:i:s', strtotime('-24 hours')),
				'operation_count' => '0',
			)
		));
		$projects = array_keys($list);

		$this->out('Ready to purge ' . count($projects) . ' projects..', 0);

		if ($this->params['dry-run']) {
			$this->out('dry-run, skipping.');
			return;
		}
		foreach ($projects as $projectId) {
			$this->Project->id = $projectId;
			if (!$this->Project->delete()) {
				$this->out('<error>Error deleting.</error>');
				die();
			} else {
				$this->out('.', 0);
			}
		}
	}

	public function getOptionParser() {
		$parser = parent::getOptionParser();

		$parser
			->addOption('dry-run', array('short' => 'd', 'help' => 'Don\'t actually make any changes.', 'boolean' => true))
			->addSubcommand('daily', array('help' => 'Execute daily cron tasks'));
		return $parser;
	}

}