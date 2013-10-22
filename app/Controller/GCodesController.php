<?php
App::uses('AppController', 'Controller');
/**
 * 
 * @author Brian Adams
 * @property GCode $GCode
 *
 */
class GCodesController extends AppController {
	
	public function align() {
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['GCode']['gcode'] = $this->GCode->alignment($this->request->data);
		}
	}
	
	public function focus() {
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['GCode']['gcode'] = $this->GCode->focus($this->request->data);
		}
	}
	
	public function index() {
		
	}
}