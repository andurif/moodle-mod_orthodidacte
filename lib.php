<?php

/**
 * Mandatory public API of url module
 *
 * @package    mod_orthodidacte
 * @copyright  2022 Anthony Durif, Université Clermont Auvergne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Defines the type of the activity and specific supports.
 * @param $feature
 * @return bool|int|null
 */
function orthodidacte_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_ARCHETYPE:           return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_BACKUP_MOODLE2:          return false;
        case FEATURE_SHOW_DESCRIPTION:        return true;
        default: return null;
    }
}

/**
 * Save an orthodidacte instance in the database.
 * @param object $data
 * @param object $mform
 * @return int new url instance id
 */
function orthodidacte_add_instance($data, $mform) {
    global $CFG, $DB, $USER, $COURSE;

    require_once($CFG->dirroot.'/mod/url/locallib.php');

    if (!empty($data->name)) {
        // Fix default display options.
        $displayoptions = array();
        $data->display = RESOURCELIB_DISPLAY_NEW;
        $data->displayoptions = serialize($displayoptions);
        $data->intro = $data->intro;
        $data->introformat = 1;
        $data->timemodified = time();
        $data->groups_selection = json_encode($data->groups_selection);

        $data->id = $DB->insert_record('orthodidacte', $data);
    }

    return $data->id;
}

/**
 * Update a courselinks instance.
 * @param object $data
 * @param object $mform
 * @return bool true if update is ok and false in other cases.
 */
function orthodidacte_update_instance($data, $mform) {
    global $CFG, $DB, $USER;

    require_once($CFG->dirroot.'/mod/url/locallib.php');

    if (!empty($data->name)) {
        // Fix default display options.
        $displayoptions = array();
        $data->display = RESOURCELIB_DISPLAY_NEW;
        $data->displayoptions = serialize($displayoptions);
        $data->intro = $data->intro;
        $data->introformat = 1;
        $data->timemodified = time();
        $data->groups_selection = json_encode($data->groups_selection);
        $data->id = $data->instance;

        $DB->update_record('orthodidacte', $data);

        return true;
    }

    return false;
}

/**
 * Delete a courselinks instance.
 * @param int $id
 * @return bool true if the deletion is ok and false in other cases.
 */
function orthodidacte_delete_instance($id) {
    global $DB;

    if (!$team = $DB->get_record('orthodidacte', array('id' => $id))) {
        return false;
    }

    $cm = get_coursemodule_from_instance('orthodidacte', $id);
    \core_completion\api::update_completion_date_event($cm->id, 'orthodidacte', $id, null);

    // note: all context files are deleted automatically
    $DB->delete_records('orthodidacte', array('id' => $team->id));

    return true;
}

/**
 * Add a get_coursemodule_info function in case adding 'extra' information for the course (see resource).
 *
 * @param stdClass $coursemodule The coursemodule object (record).
 * @return cached_cm_info An object on information that the courses
 *                        will know about (most noticeably, an icon).
 */
function orthodidacte_get_coursemodule_info($coursemodule) {
    global $CFG, $DB;
    require_once("$CFG->dirroot/mod/url/locallib.php");

    if (!$resource = $DB->get_record('orthodidacte', array('id' => $coursemodule->instance),
        'id, course, name, display, displayoptions, intro, introformat, espace, groups_selection, parcours_type')) {
        return null;
    }

    $info = new cached_cm_info();
    $info->name = $resource->name;

    $display = url_get_final_display_type($resource);

    if ($display == RESOURCELIB_DISPLAY_POPUP) {
        $fullurl = "$CFG->wwwroot/mod/orthodidacte/view.php?id=$coursemodule->id&amp;redirect=1";
        $options = empty($resource->displayoptions) ? array() : unserialize($resource->displayoptions);
        $width  = empty($options['popupwidth'])  ? 620 : $options['popupwidth'];
        $height = empty($options['popupheight']) ? 450 : $options['popupheight'];
        $wh = "width=$width,height=$height,toolbar=no,location=no,menubar=no,copyhistory=no,status=no,directories=no,scrollbars=yes,resizable=yes";
        $info->onclick = "window.open('$fullurl', '', '$wh'); return false;";
    } else if ($display == RESOURCELIB_DISPLAY_NEW) {
        $fullurl = "$CFG->wwwroot/mod/orthodidacte/view.php?id=$coursemodule->id&amp;redirect=1";
        $info->onclick = "window.open('$fullurl'); return false;";
    }

    if ($coursemodule->showdescription) {
        // Convert intro to html. Do not filter cached version, filters run at display time.
        $info->content = format_module_intro('orthodidacte', $resource, $coursemodule->id, false);
    }

    $course = get_course($resource->course); // Get cached course.
    $info->customdata = array('fullurl' => str_replace('&amp;', '&', url_get_full_url($resource, $coursemodule, $course)));

    return $info;
}

/**
 * Return the list of Orthodidacte formations available in the form.
 * @return array
 */
function orthodidacte_get_espaceslist_for_form() {
    $espaces = ['' => get_string('espace_placeholder', 'mod_orthodidacte')];
    $lines = explode("\n", get_config('mod_orthodidacte', 'espaceitems'));
    foreach ($lines as $line) {
        $items = explode("|", $line);
        $espaces[$items[0]] = trim($items[1]);
    }

    return $espaces;
}

/**
 * Returns the list of Orthodidacte parcours types available in the form.
 * @return array
 */
function orthodidacte_get_parcourslist_for_form() {
    $parcours = ['' => get_string('parcours_placeholder', 'mod_orthodidacte')];
    $lines = explode("\n", get_config('mod_orthodidacte', 'parcoursitems'));
    foreach ($lines as $line) {
        $items = explode("|", $line);
        $parcours[$items[0]] = trim($items[1]);
    }

    return $parcours;
}
