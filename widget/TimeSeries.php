<?php
class TimeSeries implements IteratorAggregate {
	private $_data = array();
	private $_timestamps_given;
	
	public $attributes;
	
	public function __construct(array $data, array $attributes = array()) {
		$this->attributes = $attributes;
		
		$first_value = reset($data);
		$first_key = key($data);
		if ($first_key === 0 && is_array($first_value) && count($first_value) == 2) {
			// Assuming each row is array(time, value)
			$this->checkTimeFormat($first_value[0]);
			$this->_data = $this->ingestMatrix($data);
		} else {
			// Assuming each row is time => value
			$this->checkTimeFormat($first_key);
			$this->_data = $this->ingestAssociative($data);
		}
		ksort($this->_data);
	}
	
	private function checkTimeFormat($time) {
		if (is_numeric($time) && (int)$time == $time) {
			$this->_timestamps_given = true;
		} elseif (is_string($time) && strtotime($time) !== false) {
			$this->_timestamps_given = false;
		}
		throw new UnexpectedValueException("Time is not a valid time string or timestamp.");
	}
	
	private function ingestMatrix(array &$data) {
		foreach ($data as $i => $row) {
			try {
				$this->_data[$this->_timestamps_given ? $row[0] : strtotime($row[0])] = $row[1];
			} catch (Exception $e) {
				$message = $e->getMessage();
				throw new Exception("Could not process data - Exception at row $i : $message");
			}
		}
	}
	
	private function ingestAssociative(array &$data) {
		if ($this->_timestamps_given) {
			$this->_data = $data;
		} else {
			foreach ($data as $time => $value) {
				$this->_data[strtotime($time)] = $value;
			}
		}
	}
	
	public function getData($format = null) {
		// TODO
		return $this->_data;
	}
	
	public function getIterator() {
		return new ArrayIterator($this->getData());
	}
}