<?php namespace ProcessWire;

/**
 * ProfilerPro Event
 * 
 * Objects of this class are only used in return results from ProfilerPro::find(), 
 * as other profiler events are recorded as a simple PHP array. 
 * 
 * @property string $id
 * @property string $parent_id
 * @property string $name
 * @property int $event_type
 * @property int $request_method
 * @property float $average_time
 * @property string $average_time_str
 * @property float $start_time
 * @property string $start_time_str
 * @property float $total_time
 * @property string $total_time_str
 * @property float $last_time
 * @property string $last_time_str
 * @property float $fastest_time
 * @property string $fastest_time_str
 * @property float $slowest_time
 * @property string $slowest_time_str
 * @property array $elapsed_times
 * @property int $pages_id
 * @property int $qty
 * @property int $created
 * @property int $modified
 * 
 */
class ProfilerProEvent extends WireData {
	public function __construct() {
		$this->setArray(array(
			'id' => '',
			'parent_id' => '',
			'name' => '',
			'event_type' => '',
			'request_method' => 0,
			'average_time' => 0.0, 
			'start_time' => 0.0,
			'total_time' => 0.0,
			'last_time' => 0.0,
			'fastest_time' => 0.0,
			'slowest_time' => 0.0,
			'elapsed_times' => array(), 
			'pages_id' => 0,
			'qty' => 0,
			'created' => 0,
			'modified' => 0,
		));
	}
	public function __get($key) {
		$value = parent::__get($key);
		if($value === null && strpos($key, '_str')) {
			$value = parent::__get(str_replace('_str', '', $key));
			if(is_float($value)) { 
				$value = (string) $value;
				if(strpos($value, ',') !== false) {
					// to handle localized floats, the '_str' versions always return using periods as decimals
					$value = str_replace(',', '.', $value);
				}
			}
		}
		return $value;
	}
}