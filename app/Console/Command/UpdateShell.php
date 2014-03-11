<?php 

App::uses('AppShell', 'Console/Command');

class UpdateShell extends AppShell {
	public $uses = array('Project', 'User');

/**
 * run method
 *
 * Run all update methods.  Update methods should either check if they need to
 * be applied, or follow "first do no harm".
 *
 */
	public function run() {
		
		$this->updateProjectCounterCache();
		$this->updateUserCounterCache();
	}
	
/**
 * updateProjectCounterCache method
 *
 * For installations before the introduction of the project/operation counter
 * caches, update the counter caches, preserving modified dates.
 *
 */
	public function updateProjectCounterCache() {
		$list = $this->Project->find('list');
		$projects = array_keys($list);
		
		
		$this->out('<warning>Updating '.count($projects).' operation counts...</warning>', 0);
		if (!count($projects)) {
			$this-out('nothing to do.');
			return;
		}
		if ($this->params['dry-run']) {
			$this->out('dry-run.. skipping.');
			return;
		}
		foreach($projects as $project_id) {
			$count = $this->Project->Operation->find('count', array('conditions' => array('Operation.project_id' => $project_id)));
			$project = array(
				'Project' => array(
					'id' => $project_id,
					'operation_count' => $count,
					'modified' => false,
				)
			);
			
			if (!$this->Project->save($project)) {
				$this->out('<error>Error saving count.</error>');
				die();
			} else {
				$this->out('.', 0);
			}
			unset($project);
		}
		$this->out('done.');
	}
	
/**
 * updateUserCounterCache method
 *
 * For installations before user countercaching was implemented, compute all 
 * user countercaches, preserving modified times.
 *
 */
	public function updateUserCounterCache() {
		$list = $this->User->find('list');
		$users = array_keys($list);
		$this->out('<warning>Updating '.count($users). ' user project counts...</warning>', 0);
		if (!count($users)){
			$this->out('nothing to do.');
			return;
		}
		if ($this->params['dry-run']) {
			$this->out('dry-run.. skipping.');
			return;
		}
		foreach ($users as $user_id) {
			$this->User->id = $user_id;
			$all_count = $this->Project->find('count', array('conditions' => array('Project.user_id' => $user_id)));
			$public_count = $this->Project->find('count', array('conditions' => array('Project.user_id' => $user_id, 'Project.public' => Project::PROJ_PUBLIC)));
			$user = array(
				'User' => array(
					'id' => $user_id,
					'project_count' => $all_count,
					'public_count' => $public_count,
					'modified' => false,
				)
			);
			if (!$this->User->save($user)) {
				$this->out('<error>Error saving count.</error>');
				die();
			} else {
				$this->out('.', 0);
			}
			unset($user);
		}
		$this->out('done.');
	}

/**
 * (non-PHPdoc)
 * @see Shell::getOptionParser()
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
	
		$parser
			->description(__('Update database to latest version.'))
			->addOption('dry-run', array('short' => 'd', 'help' => 'Don\'t actually make any changes.', 'boolean' => true))
		;
		
		return $parser;
	}
}