<?php
// This file is part of Moodle - https://moodle.org/
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
 * Provides {@see \mod_orthodidacte\output\mobile} class.
 *
 * @copyright  2022 Anthony Durif, Université Clermont Auvergne.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_orthodidacte\output;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/orthodidacte/lib.php');

/**
 * Controls the display of the plugin in the Mobile App.
 *
 * @package    mod_orthodidacte
 * @category  output
 * @copyright  2022 Anthony Durif, Université Clermont Auvergne.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mobile {

    /**
     * Return the data for the CoreCourseModuleDelegate delegate.
     *
     * @param object $args
     * @return object
     */
    public static function mobile_course_view($args) {
        global $OUTPUT, $USER, $DB, $CFG;

        $args = (object) $args;
        $cm = get_coursemodule_from_id('orthodidacte', $args->cmid);
        $context = \context_module::instance($cm->id);

        require_login($args->courseid, false, $cm, true, true);
        require_capability('mod/orthodidacte:view', $context);

        $orthodidacte = $DB->get_record('orthodidacte', ['id' => $cm->instance], '*', MUST_EXIST);
        $course = get_course($cm->course);

        // Pre-format some of the texts for the mobile app.
        $orthodidacte->name = external_format_string($orthodidacte->name, $context);
        [$orthodidacte->intro, $orthodidacte->introformat] = external_format_text($orthodidacte->intro, $orthodidacte->introformat, $context,
            'mod_orthodidacte', 'intro');

        $url = new \moodle_url('/course/view.php', array('id' => $course->id));
        $url = $url->out();
        $gotoresource = false;
        $msg = null;

        // We do not test groups members if the user has management rights on the course.
        if (!has_capability('moodle/course:update', \context_course::instance($course->id))) {
            // Test if the user is member of one of the course groups or if the user is member of more than one group.
            $user_groups = groups_get_user_groups($course->id, $USER->id)[0];
            if (count($user_groups) == 0) {
                $msg = get_string('error_not_in_group_mobile', 'mod_orthodidacte');
            }
            $groups = json_decode($orthodidacte->groups_selection);
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
                $msg = get_string('error_not_member_mobile', 'mod_orthodidacte');
            }
            if ($count > 1) {
                $msg = get_string('error_too_many_groups_mobile', 'mod_orthodidacte');
            }
        } else {
            $msg = null;
            $group = groups_get_group(json_decode($orthodidacte->groups_selection)[0]);
        }

        // Process to generate the Orthodidacte link if we do not have errors.
        if ($msg == null) {
            $profile = (strpos($USER->email,"@etu.") === false) ? "encadrant" : "apprenant";
            $userinfos = [
                'component' => $orthodidacte->espace,
                'id'        => $USER->username,
                'firstname' => $USER->firstname,
                'lastname'  => $USER->lastname,
                'email'     => $USER->email,
                'profile'   => $profile,
                'group'     => $group->name,
                'product'   => $orthodidacte->parcours_type,
            ];
            $form_params = [
                'auth_bearer'  => md5(get_config('mod_orthodidacte', 'authprefix') . date('Y-m')),
                'userinfo'     => json_encode($userinfos),
            ];

            try {
                $curl = curl_init(get_config('mod_orthodidacte', 'url'));
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $form_params);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                if ($CFG->proxyhost) {
                    curl_setopt($curl, CURLOPT_PROXY, $CFG->proxyhost);
                    curl_setopt($curl, CURLOPT_PROXYPORT, $CFG->proxyport);
                }
                $response = curl_exec($curl);
                if ($response) {
                    $elmts = json_decode($response);
                    if ($elmts->link !== null && filter_var($elmts->link, FILTER_VALIDATE_URL) !== false) {
                        $url = $elmts->link;
                        $gotoresource = true;
                    } else {
                        $msg = get_string('error_curl_mobile', 'mod_orthodidacte', get_string('error_url', 'mod_orthodidacte'));
                    }
                } else {
                    $msg = get_string('error_curl_mobile', 'mod_orthodidacte', get_string('error_request', 'mod_orthodidacte'));
                }
            }
            catch (\Exception $e) {
                $msg =  get_string('error_curl_mobile', 'mod_orthodidacte', $e->getMessage());
            }
        }

        $data = [
            'cmid' => $cm->id,
            'orthodidacte' => $orthodidacte,
            'error' => $msg,
            'gotoresource' => $gotoresource,
            'url' => $url
        ];

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('mod_orthodidacte/mobile_view', $data),
                ],
            ],
            'javascript' => '',
            'otherdata' => '',
            'files' => [],
        ];
    }
}
