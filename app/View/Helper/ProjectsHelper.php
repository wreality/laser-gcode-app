<?php
/**
 * Projects Html Helper
 *
 * PHP 5/CakePHP 2.0
 *
 * Cakestrap: https://github.com/calmsu/cakestrap
 * Copyright 2012, Michigan State University Board of Trustees
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2012, Michigan State University Board of Trustees
 * @link          http://github.com/calmsu/cakestrap
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppHelper', 'View/Helper');
class ProjectsHelper extends AppHelper {
	public $helpers = array('Html');
	
	public function getThumbnailImage($project, $options = array()) {
		if (($project['Project']['operation_count']) && (file_exists(PDF_PATH.DS.$project['Operation'][0]['id'].'.png'))) {
			echo $this->Html->image('/files/'.$project['Operation'][0]['id'].'.png', $options);
		} else { 
			echo $this->Html->image('no-thumb.png', $options);
		}
	}
	
}