<?php
App::uses('AppModel', 'Model');
class GCode extends AppModel {

	public $useTable = false;

	protected $_gcode = array();

	protected $_traversalRate = null;

	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->_traversalRate = Configure::read('LaserApp.default_traversal_rate');
	}

	public function setTraversalRate($traversalRate) {
		$this->_traversalRate = $traversalRate;
	}

	public function resetGCode() {
		$this->_gcode = array();
	}

	public function getGCode($clear = false) {
		$gcode = $this->_gcode;
		if ($clear) {
			$this->resetGCode();
		}
		return $gcode;
	}

	public function alignmentGCode($maxTravel, $axis = 'X', $oppositeAxisPosition = 0, $pulsePower = 20, $pulseTime = 5, $axisStep = 50) {
		$this->resetGCode();
		$this->insertComment(sprintf('Aligning %s-Axis', $axis));
		$this->startOpCode(true);
		$this->newLine();
		for ($value = 0; $value <= $maxTravel; $value = $value + $axisStep) {
			$this->insertComment(sprintf('Step: %0.2f', $value));
			if ($axis == 'Y') {
				$this->moveTo($oppositeAxisPosition, $value, false, 6000);
				$this->laserPulse($pulsePower, $pulseTime);
			} elseif ($axis == 'X') {
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
			$z = $targetZ + (($travel / 2) * -1) + ($i * ($travel / ($divs)));
			$steps[] = sprintf('%0.2f', $z);
		}
		$this->insertComment(sprintf('Focus Steps: %s', implode(', ', $steps)));
		$this->newLine();
		$this->startOpCode(false);
		$this->insertComment('Home Z Axis');
		$this->homeAxis(false, false, true);
		$this->zeroAxis(true, true, true);
		$this->newLine();

		for ($i = 0; $i < $divs; $i++) {
			$z = $targetZ + (($travel / 2) * -1) + ($i * ($travel / ($divs)));
			$x = $i * 5;
			$this->insertComment(sprintf('Step: %0.2f', $z));
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
		return implode("\n", $this->focusGCode(
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
			$gcode = $gcode . ' F' . $this->_traversalRate;
		}
		$this->_gcode[] = $gcode;
		return $gcode;
	}

	public function moveTo($x, $y, $z, $feedRate) {
		$gcode = 'G0';
		if ($x !== false) {
			$gcode .= ' X' . $x;
		}
		if ($y !== false) {
			$gcode .= ' Y' . $y;
		}
		if ($z !== false) {
			$gcode .= ' Z' . $z;
		}
		$gcode .= ' F' . $feedRate;
		$this->_gcode[] = $gcode;
		return $gcode;
	}

	public function laserPulse($power, $duration) {
		$gcode[] = $this->laserOn($power);
		$gcode[] = $this->dwell($duration);
		$gcode[] = $this->laserOff();
		return $gcode;
	}

	public function laserOn($power) {
		$gcode = 'M3 S' . (($power / 100) * Configure::read('LaserApp.power_scale'));
		$this->_gcode[] = $gcode;
		return $gcode;
	}

	public function laserOff() {
		$gcode = 'M5';
		$this->_gcode[] = $gcode;
		return $gcode;
	}

	public function fanOn() {
		$gcode = 'M106 ;turn on stepper fan';
		$this->_gcode[] = $gcode;
		return $gcode;
	}

	public function fanOff() {
		$gcode = 'M107  ;turn off stepper fan';
		$this->_gcode[] = $gcode;
		return $gcode;
	}

	public function disableSteppers() {
		$gcode = 'M84   ; disable steppers';
		$this->_gcode[] = $gcode;
		return $gcode;
	}

	public function unitsMM() {
		$gcode = 'G21   ; set units to mm';
		$this->_gcode[] = $gcode;
		return $gcode;
	}

	public function positionAbsolute() {
		$gcode = 'G90   ; set absolute positioning';
		$this->_gcode[] = $gcode;
		return $gcode;
	}

	public function enablePower() {
		$gcode = 'M80   ; enable accessories';
		$this->_gcode[] = $gcode;
		return $gcode;
	}

	public function disablePower() {
		$gcode = 'M81   ; disable accessories';
		$this->_gcode[] = $gcode;
		return $gcode;
	}

	public function dwell($duration) {
		$gcode = 'G4 P' . $duration;
		$this->_gcode[] = $gcode;
		return $gcode;
	}

	public function startOpCode($zero = true) {
		$this->fanOn();
		$this->enablePower();
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
		$this->disablePower();
	}

	public function insertComment($comment) {
		$this->_gcode[] = '; ' . $comment;
	}

	public function newLine() {
		$this->_gcode[] = '';
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
		$this->_gcode[] = $gcode;
		return $gcode;
	}

	public function insertGCode($gcode = array()) {
		$this->_gcode = array_merge($this->_gcode, $gcode);
	}

/**
 * pstoedit method
 *
 * Process pdf file using pstoedit add add the result to the gcode array
 *
 * @param unknown $speed
 * @param unknown $power
 * @param unknown $traversal
 * @param unknown $filename
 * @throws InternalErrorException if pstoedit returns with an error.
 * @return multitype:
 */
	public function pstoedit($speed, $power, $filename) {
			$gcode = array();
			$command = Configure::read('LaserApp.pstoedit_command');
			$command = str_replace('{{POWER}}', (int)($power), $command);
			$command = str_replace('{{SPEED}}', (int)($speed), $command);
			$command = str_replace('{{TRAVERSAL}}', (int)($this->_traversalRate), $command);
			$command = str_replace('{{FILE}}', $filename, $command);

			exec($command, $gcode, $return);
			if ($return) {
				throw new InternalErrorException();
			}
			$this->_gcode = array_merge($this->_gcode, $gcode);
			return $gcode;
	}

	public function writeFile($filename) {
		return file_put_contents($filename, implode("\n", $this->_gcode));
	}
}