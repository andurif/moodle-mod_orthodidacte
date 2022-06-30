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
 * Displays information about all the orthodidacte modules in the requested course
 *
 * @package   mod_orthodidacte
 * @copyright 2022 Anthony Durif, Université Clermont Auvergne
 */

require_once("../../config.php");
require_once($CFG->dirroot.'/mod/orthodidacte/lib.php');
// For this type of page this is the course id.
$id = required_param('id', PARAM_INT);

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
require_login($course);
$PAGE->set_url('/mod/orthodidacte/index.php', array('id' => $id));
$PAGE->set_pagelayout('incourse');

$params = array(
    'context' => context_course::instance($course->id)
);
//$event = \mod_orthodidacte\event\course_module_instance_list_viewed::create($params);
//$event->add_record_snapshot('course', $course);
//$event->trigger();

// Print the header.
$strplural = get_string("modulenameplural", "orthodidacte");
$PAGE->navbar->add($strplural);
$PAGE->set_title($strplural);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($strplural));
require_capability('mod/orthodidacte:view', $params['context']);

$orthodidactes = get_all_instances_in_course('orthodidacte', $course);
if (!$orthodidactes) {
    notice('There are no instances of orthodidacte resources', "../../course/view.php?id=$course->id");
    die;
}

// Print the table
$table = new html_table();
$table->head = array(get_string('sectionname', 'format_'.$course->format), get_string('name'), get_string('espace', 'mod_orthodidacte'), get_string('parcours_type', 'mod_orthodidacte'));
$table->align = array('left', 'left', 'center', 'center');

foreach ($orthodidactes as $orthodidacte) {
    if (has_capability('mod/orthodidacte:view', context_module::instance($orthodidacte->coursemodule))) {
        if (!$orthodidacte->visible) {
            // Show dimmed if the mod is hidden.
            $link = '<a class="dimmed" href="view.php?id=' . $orthodidacte->coursemodule . '">' . format_string($orthodidacte->name) . '</a>';
        } else {
            // Show normal if the mod is visible.
            $link = '<a href="view.php?id=' . $orthodidacte->coursemodule . '">' . format_string($orthodidacte->name) . '</a>';
        }

        $table->data[] = array(get_section_name($course, $orthodidacte->section), $link, $orthodidacte->espace, $orthodidacte->parcours_type);
    }
}

echo html_writer::table($table);
echo $OUTPUT->footer();