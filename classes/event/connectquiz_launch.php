<?php
namespace mod_connectquiz\event;
defined('MOODLE_INTERNAL') || die();
class connectquiz_launch extends \core\event\base {
	protected function init() {
		global $CFG;
		$this->context = \context_system::instance();
		$this->data['crud'] = 'c'; // c(reate), r(ead), u(pdate), d(elete)
		if( $CFG->branch >= 27 ){
			$this->data['edulevel'] = self::LEVEL_OTHER;
		}else{
			$this->data['level'] = self::LEVEL_OTHER;
		}
		$this->data['objecttable'] = 'connectquiz';
// 		$this->data['anonymous'] = 1;
	}

	public static function get_name() {
		return get_string('launch', 'connectquiz');
	}

	public function get_description() {
		return isset( $this->other['description'] ) ? $this->other['description'] : serialize($this->other);
	}

	public function get_url() {
		global $CFG;
		return new \moodle_url( 'filter/connect/launch.php?url='.$this->other['acurl']); // There is no one single url
	}

}
?>