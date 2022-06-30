<?php
$string['modulename'] = 'Orthodidacte';
$string['modulename_help'] = 'Mod which permits to link a moodle course, a group and a formation in the Orthodidacte tool';
$string['modulenameplural'] = 'Orthodidacte';
$string['pluginname'] = 'Orthodidacte';
$string['pluginadministration'] = 'Orthodidacte resource';
$string['orthodidacte:addinstance'] = 'Add an Orthodidacte resource';
$string['orthodidacte:view'] = 'View an Orthodidacte resource';

$string['url'] = 'Orthodidacte URL';
$string['url_desc'] = 'URL used to generate the redirection link to the Orthodidacte tool.';
$string['authprefix'] = 'Authentification bearer prefix';
$string['authprefix_desc'] = 'Prefix used for Orthodidacte web service call as authentification bearer prefix.';
$string['parcoursitems'] = 'Parcours type items';
$string['parcoursitems_desc'] = 'Items list for "parcours type" field available in the Orthodidacte activity creation form.<br/>
            Each line defines an item and is separated by a pipe. This consists of 1) a string for the code (usually provided by Orthodidacte)
            and 2) a string for the label (available in the form list).';
$string['parcoursitems_default'] = 'Parcours1_Code|Parcours1_Label
Parcours2_Code|Parcours2_Label
Parcours3_Code|Parcours3_Label';
$string['espaceitems'] = 'Orthodidacte formation items';
$string['espaceitems_desc'] = 'List of Orthodidacte formations available in the Orthodidacte activity creation form.<br/>
            Each line defines a formation and is separated by a pipe. This consists of 1) a string for the code (usually provided by Orthodidacte)
            and 2) a string for the label (available in the form list).';
$string['espaceitems_default'] = 'Formation1_Code|Formation1_Label
Formation2_Code|Formation2_Label
Formation3_Code|Formation3_Label';

$string['back_course'] = 'Back to course';
$string['espace'] = 'Formation';
$string['espace_placeholder'] = 'Choose a formation...';
$string['parcours_type'] = 'Parcours type';
$string['parcours_placeholder'] = 'Choose a parcours type...';
$string['no_group'] = '<div class="alert alert-danger">There is no group in your course, it will not be possible to create an Orthodidacte resource on this course.<br>
                                                Retry this resource creation when groups will be created and full.</div>';
$string['error_groups'] = 'Please select at least one group.';
$string['error_too_many_groups'] = '<div class="alert alert-danger">You are member of more than one group in this course, so you cannot access this specific Orthodidacte resource.<br/>
            Please contact course manager(s) to rectify.</div>';
$string['error_too_many_groups_mobile'] = 'You are member of more than one group in this course, so you cannot access this specific Orthodidacte resource.<br/>Please contact course manager(s) to rectify.';
$string['error_not_in_group'] = '<div class=\'alert alert-danger\'>You are not member of a group in this course, so you cannot access this specific Orthodidacte resource.<br/>
            Please contact course manager(s) to rectify.</div>';
$string['error_not_in_group_mobile'] = 'You are not member of a group in this course, so you cannot access this specific Orthodidacte resource.<br/>Please contact course manager(s) to rectify.';
$string['error_not_member'] = '<div class=\'alert alert-danger\'>You are not member of a selected group, so you cannot access this specific Orthodidacte resource.<br/>
            Please contact course manager(s) to rectify.</div>';
$string['error_not_member_mobile'] = 'You are not member of a selected group, so you cannot access this specific Orthodidacte resource.<br/>Please contact course manager(s) to rectify.';
$string['error_curl'] = '<div class=\'alert alert-danger\'>An error occured during the call of Orthodidacte: {$a}.</div>';
$string['error_curl_mobile'] = 'An error occured during the call of Orthodidacte: {$a}.';
$string['error_request'] = 'request aborbed';
$string['error_url'] = 'the URL returned by Orthodidacte is incorrect';

$string['privacy:metadata'] = 'Orthodidacte resource plugin does not store or transmit any personal data.';

$string['gotoresource'] = 'Go to the Orthodidacte resource';
$string['opennav'] = 'OPEN IN THE BROWSER';