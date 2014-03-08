<?php 

App::uses('AppShell', 'Console');

class UpdateShell extends AppShell {
	public $uses = array('Project');
	
	public function run() {
		if ($this->params['dry-run']) {
			$this->dispatchShell('schema create --name laser --dry');
			$this->dispatchShell('schema update --name laser --dry');
		} else {
			$this->dispatchShell('schema create --name laser');
			$this->dispatchShell('schema update --name laser');
		}
		$this->updateProjectCounterCache();
	}
	
	public function updateProjectCounterCache() {
		$list = $this->Project->find('list');
		$projects = array_keys($list);
		
		
		$this->out('<warning>Updating '.count($projects).' operation counts...', 0);
		if (!count($projects)) {
			$this-out('nothing to do.');
			return;
		}
		if ($this->params['dry-run']) {
			$this->out('dry-run.. skipping.');
			return;
		}
		foreach($projects as $project_id) {
			$this->Project->id = $project_id;
			$count = $this->Project->Operation->find('count', array('conditions' => array('Operation.project_id' => $project_id)));
			if (!$this->Project->saveField('operation_count', $count)) {
				$this->out('<error>Error saving count.</error>');
				die();
			} else {
				$this->out('.', 0);
			}
		}
		$this->out('done.');
	}
	
	public function getOptionParser() {
		$parser = parent::getOptionParser();
	
		$parser
			->description(__('Update database to latest version.'))
			->addOption('dry-run', array('short' => 'd', 'help' => 'Don\'t actually make any changes.', 'boolean' => true))
		;
		
		return $parser;
	}
}