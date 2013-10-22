<?php
App::uses('AppModel', 'Model');
class GCode extends AppModel {
	
	public $useTable = false;
	
	protected $gcode = array();
	
	public function resetGCode() {
		$this->gcode = array();
	}
	
	public function getGCode($clear = false) {
		$gcode =  $this->gcode;
		if ($clear) {
			$this->resetGCode();
		}
		return $gcode;
	}

	public function alignmentGCode($max_travel, $axis = 'X', $oppositeAxisPosition = 0, $pulsePower = 20, $pulseTime = 5, $axisStep = 50) {
		$this->resetGCode();
		$this->insertComment(sprintf('Aligning %s-Axis', $axis));
		$this->startOpCode(true);
		$this->newLine();
		for ($value=0; $value <= $max_travel; $value = $value + $axisStep) {
			$this->insertComment(sprintf('Step: %0.2f', $value));
			if ($axis == 'Y') {
				$this->moveTo($oppositeAxisPosition, $value, false, 6000);
				$this->laserPulse($pulsePower, $pulseTime);
			} else if ($axis == 'X') {
				$this->moveTo($value, $oppositeAxisPosition, false, 6000);
				$this->laserPulse($pulsePower, $pulseTime);
			}
			$this->newLine();
		}
		$this->endOpCode();
		return $this->getGcode(true);	
		
		
	}
	
	public function alignment($data) {
		
		return implode("\n", $this->alignmentGCode(
			$data[$this->alias]['max_travel'],
			$data[$this->alias]['axis'],
			$data[$this->alias]['oppositeAxisPosition'],
			$data[$this->alias]['pulsePower'],
			$data[$this->alias]['pulseTime'],
			$data[$this->alias]['axisStep']
		));
	}
	
	public function focusGCode($targetZ, $travel = 10, $divs = 10) {
		$this->resetGCode();
		
		for ($i = 0; $i < $divs; $i++) {
			$z = $targetZ + (($travel/2)*-1) + ($i * ($travel/($divs)));
			$steps[] = sprintf('%0.2f',$z);
		}
		$this->insertComment(sprintf('Focus Steps: %s', implode(', ',$steps)));
		$this->newLine();
		$this->startOpCode(false);
		$this->insertComment('Home Z Axis');
		$this->homeAxis(false, false, true);
		$this->zeroAxis(true, true, true);
		$this->newLine();
		
		for ($i = 0; $i < $divs; $i ++) {
			$z = $targetZ + (($travel/2)*-1)+ ($i * ($travel/($divs)));
			$x = $i * 5;
			$this->insertComment(sprintf('Step: %0.2f',$z));
			$this->moveTo($x, 0, $z, 150);
			$this->laserOn(20);
			$this->moveTo($x, 10, false, 1000);
			$this->laserOff();
			$this->newLine();
		}
		$this->endOpCode();
		return $this->getGcode(true);
	}
	
	public function focus($data) {
		return implode("\n",$this->focusGCode(
			$data[$this->alias]['targetZ'],
			$data[$this->alias]['travel'],
			$data[$this->alias]['divs']
		));
		
	}
	
	public function homeAxis($x = true, $y = true, $z = false) {
		$gcode = 'G28';
		if ($x) {
			$gcode .= ' X0';
		}
		if ($y) {
			$gcode .= ' Y0';
		}
		if ($z) {
			$gcode .= ' Z0 F150';
		} else {
			$gcode .= ' F6000';
		}
		$this->gcode[] = $gcode;
		return $gcode;
	}
	
	public function moveTo($x, $y, $z, $feedRate) {
		$gcode = 'G1';
		if ($x !== false) {
			$gcode .= ' X'.$x;
		}
		if ($y !== false) {
			$gcode .= ' Y'.$y;
		}
		if ($z !== false) {
			$gcode .= ' Z'.$z;
		}
		$gcode .= ' F'.$feedRate;
		$this->gcode[] = $gcode;
		return $gcode;
	}
	
	public function laserPulse($power, $duration) {
		
		$gcode[] = $this->laserOn($power);
		$gcode[] = $this->dwell($duration);
		$gcode[] = $this->laserOff();
		return $gcode;
		
	}
	
	public function laserOn($power) {
		$gcode = 'M3 S'.(($power/100)*Configure::read('App.power_scale'));
		$this->gcode[] = $gcode;
		return $gcode;
	}
	
	public function laserOff() {
		$gcode = 'M5';
		$this->gcode[] = $gcode;
		return $gcode;
	}
	
	public function fanOn() {
		$gcode = 'M106 ;turn on stepper fan';
		$this->gcode[] = $gcode;
		return $gcode;
	}
	
	public function fanOff() {
		$gcode = 'M107  ;turn off stepper fan';
		$this->gcode[] = $gcode;
		return $gcode;
	}
	
	public function disableSteppers() {
		$gcode = 'M84   ; disable steppers';
		$this->gcode[] = $gcode;
		return $gcode;
	}
	
	public function unitsMM() {
		$gcode = 'G21   ; set units to mm';
		$this->gcode[] = $gcode;
		return $gcode;
	}
	
	public function positionAbsolute() {
		$gcode = 'G90   ; set absolute positioning';
		$this->gcode[] = $gcode;
		return $gcode;
	}
	
	public function dwell($duration) {
	   $gcode = 'G4 P'.$duration;
	   $this->gcode[] = $gcode;
	   return $gcode;
	}
	public function startOpCode($zero = true) {
		$this->fanOn();
		$this->disableSteppers();
		$this->unitsMM();
		$this->positionAbsolute();
		if ($zero) {
			$this->homeAxis();
		}
		$this->zeroAxis();
	}
	
	public function endOpCode($stepperDisable = true) {
		$this->positionAbsolute();
		$this->fanOff();
		if ($stepperDisable) {
			$this->disableSteppers();
		}
	}
	
	public function insertComment($comment) {
		$this->gcode[] = '; '.$comment;
	}
	
	public function newLine() {
		$this->gcode[] = '';
	}
	
	public function zeroAxis($x = true, $y = true, $z = false) {
		$gcode = 'G92';
		if ($x) {
			$gcode .= ' X0';
		}
		if ($y) {
			$gcode .= ' Y0';
		}
		if ($z) {
			$gcode .= ' Z0';
		}
		$gcode .= ' ; zero axis';
		$this->gcode[] = $gcode;
		return $gcode;
	}
	
	public function insertGCode($gcode = array()) {
		$this->gcode = array_merge($this->gcode, $gcode);
	}
	
	public function pstoedit($speed, $power, $traversal, $filename) {
			$gcode = array();
			$command = Configure::read('App.pstoedit_command');
			$command = str_replace('{{POWER}}', (int)($power), $command);
			$command = str_replace('{{SPEED}}', (int)($speed), $command);
			$command = str_replace('{{TRAVERSAL}}', (int)($traversal), $command);
			$command = str_replace('{{FILE}}', $filename, $command);
			exec($command, $gcode);
			$this->gcode[] = $gcode;
			return $gcode;
	}
	
	public function writeFile($filename) {
		return file_put_contents($filename, implode("\n", $this->gcode));
	}
}