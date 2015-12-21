<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Time since enrol condition.
 *
 * @package availability_timesinceenrol
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_timesinceenrol;

defined('MOODLE_INTERNAL') || die();

/**
 * timesinceenrol condition.
 *
 * @package availability_timesinceenrol
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class condition extends \core_availability\condition {

    /**
     * Constructor.
     *
     * @param \stdClass $structure Data structure from JSON decode
     * @throws \coding_exception If invalid data structure.
     */
    public function __construct($structure) {
        if (isset($structure->mintimesinceenrol)) {
            $this->mintimesinceenrol = $structure->mintimesinceenrol;
        }
    }

    public function save() {
        $result = (object)array('type' => 'timesinceenrol');

        if ($this->businessemail) {
            $result->mintimesinceenrol = $this->mintimesinceenrol;
        }
        return $result;
    }

    /**
     * Returns a JSON object which corresponds to a condition of this type.
     *
     * Intended for unit testing, as normally the JSON values are constructed
     * by JavaScript code.
     *
     * @param int $mintimesinceenrol in seconds
     * @return stdClass Object representing condition
     */
    public static function get_json($mintimesinceenrol) {
        return (object)array('type' => 'timesinceenrol', 'mintimesinceenrol' => $mintimesinceenrol);
    }

    public function is_available($not, \core_availability\info $info, $grabthelot, $userid) {
        global $PAGE, $CFG;
        require_once($CFG->dirroot. '/enrol/locallib.php');
        $course = $info->get_course();
        $enrolmanager = new \course_enrolment_manager($PAGE, $course);
        $allow = true;
        if (!$enrolments = $enrolmanager->get_user_enrolments($userid)) {
            $allow = false;
        }
        foreach ($enrolments as $enrol) {
            if ((time() - $enrol->timestart) < $this->mintimesinceenrol ) {
                $allow = false;
            }
        }
        if (!$not) {
            $allow = !$allow;
        }
        return $allow;
    }

    public function get_description($full, $not, \core_availability\info $info) {
        return $this->get_either_description($not, false);
    }
    /**
     * Shows the description using the different lang strings for the standalone
     * version or the full one.
     *
     * @param bool $not True if NOT is in force
     * @param bool $standalone True to use standalone lang strings
     */
    protected function get_either_description($not, $standalone) {
        return get_string('eitherdescription', 'availability_timesinceenrol', $this->mintimesinceenrol / (3600 * 24));
    }

    protected function get_debug_string() {
        return gmdate('Y-m-d H:i:s');
    }

    public function update_after_restore($restoreid, $courseid, \base_logger $logger, $name) {
        // Update the date, if restoring with changed date.
        $dateoffset = \core_availability\info::get_restore_date_offset($restoreid);
        if ($dateoffset) {
            $this->time += $dateoffset;
            return true;
        }
        return false;
    }
}
