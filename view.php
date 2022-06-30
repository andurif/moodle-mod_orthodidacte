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
 * Orthodidacte module main user interface
 *
 * @package  mod_orthodidacte
 */

require_once('../../local/libs/config.php');
require_once("lib.php");
require_once("$CFG->dirroot/mod/url/locallib.php");
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->dirroot.'/vendor/autoload.php');


$id = required_param('id', PARAM_INT);    // Course Module ID
$u = optional_param('u', 0, PARAM_INT); // URL instance id
$redirect = optional_param('redirect', 0, PARAM_BOOL);

if ($u) {  // Two ways to specify the module
    $resource = $DB->get_record('orthodidacte', array('id' => $u), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('orthodidacte', $resource->id, $resource->course, false, MUST_EXIST);
} else {
    $cm = get_coursemodule_from_id('orthodidacte', $id, 0, false, MUST_EXIST);
    $resource = $DB->get_record('orthodidacte', array('id' => $cm->instance), '*', MUST_EXIST);
}
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/orthodidacte:view', $context);

$params = array(
    'context' => $context,
    'objectid' => $resource->id
);

$PAGE->set_url('/mod/orthodidacte/view.php', array('id' => $cm->id));

// We do not test groups members if the user has management rights on the course.
if (!has_capability('moodle/course:update', context_course::instance($COURSE->id))) {
    // Test if the user is member of one of the course groups or if the user is member of more than one group.
    $user_groups = groups_get_user_groups($course->id, $USER->id)[0];
    if (count($user_groups) == 0) {
        $msg = get_string('error_not_in_group', 'mod_orthodidacte');
    }
    $groups = json_decode($resource->groups_selection);
    $count = 0;
    foreach ($user_groups as $user_group) {
        if (in_array($user_group, $groups)) {
            if ($count == 0) {
                $group = groups_get_group($user_group);
            }
            $count++;
        }
    }
    if ($count == 0) {
        $msg = get_string('error_not_member', 'mod_orthodidacte');
    }
    if ($count > 1) {
        $msg = get_string('error_too_many_groups', 'mod_orthodidacte');
    }
} else {
    $msg = null;
    $group = groups_get_group(json_decode($resource->groups_selection)[0]);
}

// Displays error message.
if ($msg != null) {
    $PAGE->set_title($resource->name);
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();
    echo $OUTPUT->heading(format_string($resource->name));
    echo $msg;
    $course_url = new moodle_url('/course/view.php', array('id' => $course->id));
    echo "<a href='" . $course_url. "' class='btn btn-secondary' style='text-align:center;'>" . get_string('back_course', 'mod_orthodidacte') . "</a>";
    echo $OUTPUT->footer();
    exit;
}

// Send informations to Orthodidacte.
$profile = (strpos($USER->email,"@etu.") === false) ? "encadrant" : "apprenant";
$userinfos = [
    'component' => $resource->espace,
    'id'        => $USER->username,
    'firstname' => $USER->firstname,
    'lastname'  => $USER->lastname,
    'email'     => $USER->email,
    'profile'   => $profile,
    'group'     => $group->name,
    'product'   => $resource->parcours_type,
];
$form_params = [
    'auth_bearer'  => md5(get_config('mod_orthodidacte', 'authprefix') . date('Y-m')),
    'userinfo'     => json_encode($userinfos),
];

// Get token and redirect to generated link.
if ($params) {
    $PAGE->set_title($resource->name);
    $PAGE->set_heading($course->fullname);

    try {
        $client = new GuzzleHttp\Client();

        $params = ($CFG->proxyhost) ? ['form_params' => $form_params, 'proxy' => $CFG->proxyhost] : ['form_params' => $form_params];
        $response = $client->post(get_config('mod_orthodidacte', 'url'), $params);
        if ($response->getStatusCode() == 200) {
            $elmts = json_decode($response->getBody()->getContents());
            if ($elmts->link !== null && filter_var($elmts->link, FILTER_VALIDATE_URL) !== false) {
                if ($redirect) {
                    if (!course_get_format($course)->has_view_page()) {
                        // If course format does not have a view page, add redirection delay with a link to the edit page.
                        // Otherwise teacher is redirected to the external URL without any possibility to edit activity or course settings.
                        $editurl = null;
                        if (has_capability('moodle/course:manageactivities', $context)) {
                            $editurl = new moodle_url('/course/modedit.php', array('update' => $cm->id));
                            $edittext = get_string('editthisactivity');
                        } else if (has_capability('moodle/course:update', $context->get_course_context())) {
                            $editurl = new moodle_url('/course/edit.php', array('id' => $course->id));
                            $edittext = get_string('editcoursesettings');
                        }
                        if ($editurl) {
                            redirect($elmts->link, html_writer::link($editurl, $edittext)."<br/>".
                                get_string('pageshouldredirect'), 10);
                        }
                    }
                    redirect($elmts->link);
                }

                redirect($elmts->link);
            } else {
                echo $OUTPUT->header();
                echo $OUTPUT->heading(format_string($resource->name));
                echo get_string('error_curl', 'mod_orthodidacte', get_string('error_url', 'mod_orthodidacte'));
                echo $OUTPUT->footer();
                exit;
            }
        } else {
            echo $OUTPUT->header();
            echo $OUTPUT->heading(format_string($resource->name));
            echo get_string('error_curl', 'mod_orthodidacte', get_string('error_request', 'mod_orthodidacte'));
            echo $OUTPUT->footer();
            exit;
        }
    }
    catch (Exception $e) {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(format_string($resource->name));
        echo get_string('error_curl', 'mod_orthodidacte', $e->getMessage());
        echo $OUTPUT->footer();
        exit;
    }
}

$event = \mod_orthodidacte\event\course_module_viewed::create($params);
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('orthodidacte', $resource);
$event->trigger();

// Update 'viewed' state if required by completion system
$completion = new completion_info($course);
$completion->set_module_viewed($cm);
