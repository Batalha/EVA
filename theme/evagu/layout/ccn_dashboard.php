<?php
defined('MOODLE_INTERNAL') || die();
include($CFG->dirroot . '/theme/evagu/ccn/ccn_themehandler.php');
array_push($extraclasses, "ccn_context_dashboard");
$bodyclasses = implode(" ",$extraclasses);
$bodyattributes = $OUTPUT->body_attributes($bodyclasses);
include($CFG->dirroot . '/theme/evagu/ccn/ccn_themehandler_context.php');
echo $OUTPUT->render_from_template('theme_evagu/ccn_dashboard', $templatecontext);
