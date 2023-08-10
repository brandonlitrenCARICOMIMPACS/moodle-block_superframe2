<?php
/**
 * Callback functions for block_superframe.
 *
 * @package block_superframe
 * @copyright 2018 Richard Jones 
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

function block_superframe_extend_navigation_course($navigation, $course, $context) {
    $url = new moodle_url('/blocks/superframe/block_data.php');
    $userlink = get_string('userlink', 'block_superframe');
    $navigation->add($userlink, $url, navigation_node::TYPE_SETTING,
        $userlink, 'superframe',
        new pix_icon('icon', '', 'block_superframe'));
}

function block_superframe_myprofile_navigation(core_user\output\myprofile\tree $tree,
    $user, $iscurrentuser, $course) {

    $url = new moodle_url('/blocks/superframe/block_data.php');
    $node = new core_user\output\myprofile\node('miscellaneous', 'superframe',
        get_string('userlink', 'block_superframe'), null, $url);
    $tree->add_node($node);
}