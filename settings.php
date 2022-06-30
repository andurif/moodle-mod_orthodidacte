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
 * Settings for orthodidacte plugin.
 *
 * @package    mod_orthodidacte
 * @copyright  2022 Anthony Durif, Université Clermont Auvergne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('mod_orthodidacte/url',
        get_string('url', 'mod_orthodidacte'),
        get_string('url_desc', 'mod_orthodidacte'),
        ''
    ));

    $settings->add(new admin_setting_configtext('mod_orthodidacte/authprefix',
        get_string('authprefix', 'mod_orthodidacte'),
        get_string('authprefix_desc', 'mod_orthodidacte'),
        'XXXX-Orthodidacte'
    ));

    $settings->add(new admin_setting_configtextarea('mod_orthodidacte/parcoursitems',
        get_string('parcoursitems', 'mod_orthodidacte'),
        get_string('parcoursitems_desc', 'mod_orthodidacte'),
        get_string('parcoursitems_default', 'mod_orthodidacte'),
        PARAM_RAW,
        '50',
        '7'
    ));

    $settings->add(new admin_setting_configtextarea('mod_orthodidacte/espaceitems',
        get_string('espaceitems', 'mod_orthodidacte'),
        get_string('espaceitems_desc', 'mod_orthodidacte'),
        get_string('espaceitems_default', 'mod_orthodidacte'),
        PARAM_RAW,
        '50',
        '10'
    ));
}
