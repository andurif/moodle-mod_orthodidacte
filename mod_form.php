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
//
// This file is part of BasicLTI4Moodle
//
// BasicLTI4Moodle is an IMS BasicLTI (Basic Learning Tools for Interoperability)
// consumer for Moodle 1.9 and Moodle 2.0. BasicLTI is a IMS Standard that allows web
// based learning tools to be easily integrated in LMS as native ones. The IMS BasicLTI
// specification is part of the IMS standard Common Cartridge 1.1 Sakai and other main LMS
// are already supporting or going to support BasicLTI. This project Implements the consumer
// for Moodle. Moodle is a Free Open source Learning Management System by Martin Dougiamas.
// BasicLTI4Moodle is a project iniciated and leaded by Ludo(Marc Alier) and Jordi Piguillem
// at the GESSI research group at UPC.
// SimpleLTI consumer for Moodle is an implementation of the early specification of LTI
// by Charles Severance (Dr Chuck) htp://dr-chuck.com , developed by Jordi Piguillem in a
// Google Summer of Code 2008 project co-mentored by Charles Severance and Marc Alier.
//
// BasicLTI4Moodle is copyright 2009 by Marc Alier Forment, Jordi Piguillem and Nikolas Galanis
// of the Universitat Politecnica de Catalunya http://www.upc.edu
// Contact info: Marc Alier Forment granludo @ gmail.com or marc.alier @ upc.edu.

/**
 * This file defines the main lti configuration form
 *
 * @package    mod_orthodidacte
 * @copyright  2022 Anthony Durif, Université Clermont Auvergne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/config.php');
require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/url/locallib.php');
require_once($CFG->dirroot.'/vendor/autoload.php');

class mod_orthodidacte_mod_form extends moodleform_mod {

    function definition() {
        global $COURSE;
        $mform = $this->_form;
        $error = null;

        $groups = [];
        if (count(groups_get_all_groups($COURSE->id)) > 0) {
            foreach (groups_get_all_groups($COURSE->id) as $group) {
                $groups[$group->id] = $group->name;
            }
        }

        if (count($groups) > 0) {
            if (!empty($this->current->id)) {
                $this->current->groups_selection = json_decode($this->current->groups_selection);
            }

            $mform->addElement('header', 'general', get_string('general'));

            $mform->addElement('text', 'name', get_string('name'), 'size=80');
            $mform->addRule('name', null, 'required', null, 'client');
            $mform->setType('name', PARAM_TEXT);

            $this->standard_intro_elements();
            $element = $mform->getElement('introeditor');
            $attributes = $element->getAttributes();
            $attributes['rows'] = 5;
            $element->setAttributes($attributes);

            $espaceslist = orthodidacte_get_espaceslist_for_form();
            $mform->addElement('select', 'espace', get_string('espace', 'mod_orthodidacte'), $espaceslist);
            $mform->addRule('espace', null, 'required', null, 'client');

            $mform->addElement('searchableselector', 'groups_selection', get_string('group'), $groups, array('multiple'));

            $parcourslist = orthodidacte_get_parcourslist_for_form();
            $mform->addElement('select', 'parcours_type', get_string('parcours_type', 'mod_orthodidacte'), $parcourslist);
            $mform->addRule('parcours_type', null, 'required', null, 'client');

            $this->standard_coursemodule_elements();
            $this->add_action_buttons();
        } else {
            // There is no group in this course -> display error message.
            $this->standard_hidden_coursemodule_elements();
            $mform->addElement('html', get_string('no_group', 'mod_orthodidacte'));
            $mform->addElement('cancel', '', get_string('back_course', 'mod_orthodidacte'));
        }
    }

    function validation($data, $files)
    {
        $errors = parent::validation($data, $files);

        if (count($data['groups_selection']) < 1) {
            // No group selection.
            $errors['groups_selection'] = get_string('error_groups', 'mod_orthodidacte');
        }

        if ($data['espace'] == '') {
            // No formation selection.
            $errors['espace'] = get_string('err_required', 'form');
        }

        if ($data['parcours_type'] == '') {
            // No parcours type selection.
            $errors['parcours_type'] = get_string('err_required', 'form');
        }

        return $errors;
    }
}
