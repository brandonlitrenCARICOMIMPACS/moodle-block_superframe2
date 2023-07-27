<?php

class block_superframe_renderer extends plugin_renderer_base {

 public function display_view_page($url, $width, $height) {
        global $USER;
        $data = new stdClass();

        // Page heading and iframe data.
        $data->heading = get_string('pluginname', 'block_superframe');
        $data->url = $url;
        $data->height = $height;
        $data->width = $width;

        // Add the user data.
        $data->fullname = fullname($USER);
        $data->image = $USER->picture;

        // Start output to browser.
        echo $this->output->header();

        // Render the data in a Mustache template.
        echo $this->render_from_template('block_superframe/frame', $data);

        // Finish the page.
        echo $this->output->footer();
    }

public function fetch_block_content($blockid, $courseid) {
        global $USER;

        $data = new stdClass();

        $data->welcome = get_string('welcomeuser', 'block_superframe', $USER);
        $context = \context_block::instance($blockid);
        // Check the capability.
        if (has_capability('block/superframe:seeviewpagelink', $context)) {
            $data->url = new moodle_url('/blocks/superframe/view.php', ['blockid' => $blockid, 'courseid' => $courseid]);
            $data->text = get_string('viewlink', 'block_superframe');
        }

        if(has_capability('block/superframe:seelist', $context)){
        $users = self::get_course_users($courseid);    
        $data->heading .= "List of students enrolled";
        foreach ($users as $user) {
             $data->list[] = $user->firstname . ' ' . $user->lastname; 
        }
        }
        // Render the data in a Mustache template.
        return $this->render_from_template('block_superframe/block', $data);
    }
    private static function get_course_users($courseid) {
        global $DB;

        $sql = "SELECT u.id, u.firstname, u.lastname ";
        $sql .= "FROM {course} c ";
        $sql .= "JOIN {context} x ON c.id = x.instanceid ";
        $sql .= "JOIN {role_assignments} r ON r.contextid = x.id ";
        $sql .= "JOIN {user} u ON u.id = r.userid ";
        $sql .= "WHERE c.id = :courseid ";
        $sql .= "AND r.roleid = :roleid";

        // In real world query should check users are not deleted/suspended.
        $records = $DB->get_records_sql($sql, ['courseid' => $courseid, 'roleid' => 5]);

        return $records;
    }
}

