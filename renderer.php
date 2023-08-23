<?php

class block_superframe_renderer extends plugin_renderer_base {

 public function display_view_page($url, $width, $height, $courseid, $blockid) {
        global $USER;

        $this->page->requires->js_call_amd('block_superframe/modal_amd', 'init',
        array('data' =>
            array(
                'title' => get_string('about', 'block_superframe'),
                'body' => get_string('modalbody', 'block_superframe'),
                'footer' => get_string('modalfooter', 'block_superframe')
            )
        )
    );
        $data = new stdClass();

        // Page heading and iframe data.
        $data->heading = get_string('pluginname', 'block_superframe');
        $data->url = $url;
        $data->height = $height;
        $data->width = $width;
        $data->returnlink = new moodle_url('/course/view.php', ['id' => $courseid]);
        $data->returntext = get_string('returncourse', 'block_superframe');

        // Add the user data.
        $data->fullname = fullname($USER);
        $data->image = $USER->picture;

        // Text for the links and the size parameter.
        $strings = array();
        $strings['custom'] = get_string('custom', 'block_superframe');
        $strings['small'] = get_string('small', 'block_superframe');
        $strings['medium'] = get_string('medium', 'block_superframe');
        $strings['large'] = get_string('large', 'block_superframe');

        // Create the data structure for the links.
        $links = array();
        $link = new moodle_url('/blocks/superframe/view.php', ['courseid' => $courseid,
            'blockid' => $blockid]);
        
        foreach ($strings as $key => $string) {
            $links[] = ['link' => $link->out(false, ['size' => $key]), 'text' => $string];
        }

        $data->linkdata = $links;

        // Start output to browser.
        echo $this->output->header();

        // Render the data in a Mustache template.
        echo $this->render_from_template('block_superframe/frame', $data);

        // Finish the page.
        echo $this->output->footer();
    }

    public function display_block_table($records) {

        // Prepare the data for the template.
        $table = new stdClass();

        // Table headers.
        $table->tableheaders = [
            get_string('blockid', 'block_superframe'),
            get_string('blockname', 'block_superframe'),
            get_string('course', 'block_superframe'),
            get_string('catname', 'block_superframe'),
        ];

        // Build the data rows.
        foreach ($records as $record) {
            $data = array();
            $data[] = $record->id;
            $data[] = $record->blockname;
            $data[] = $record->shortname;
            $data[] = $record->catname;
            $table->tabledata[] = $data;
        }

        // Start output to browser.
        echo $this->output->header();

        // Call our template to render the data.
        echo $this->render_from_template('block_superframe/block_data', $table);

        // Finish the page.
        echo $this->output->footer();
    }

    public function fetch_block_content($blockid, $courseid) {
        global $DB, $SITE, $USER;

        $text = "Javascript Button";
        $this->page->requires->js_call_amd('block_superframe/button_amd', 'button', ['text' => $text]);
        $data = new stdClass();
        $name = $USER->firstname.' '.$USER->lastname;
        $this->page->requires->js_call_amd('block_superframe/test_amd', 'init', ['name' => $name]);
        $data->headingclass = 'block_superframe_heading';
        $data->welcome = get_string('welcomeuser', 'block_superframe', $name);
        $context = context_block::instance($blockid);
        // Check the capability.
        if (has_capability('block/superframe:seeviewpagelink', $context)) {
            $data->url = new moodle_url('/blocks/superframe/view.php', ['blockid' => $blockid, 'courseid' => $courseid]);
            $data->text = get_string('viewlink', 'block_superframe');
        }

        // Add a link to the popup page.
        $data->popurl = new moodle_url('/blocks/superframe/block_data.php');
        $data->poptext = get_string('poptext', 'block_superframe');

        // Add a link to the table.
        $data->tableurl = new moodle_url('/blocks/superframe/tablemanager.php');
        $data->tabletext = get_string('tabletext', 'block_superframe');

        if(has_capability('block/superframe:seelist', $context)){
        $users = self::get_course_users($courseid);    
        $data->heading .= "List of students enrolled";
        foreach ($users as $user) {
             $data->list[] = $user->firstname . ' ' . $user->lastname; 
        }
        }
        if ($courseid != $SITE->id) { // Prevent issue when the block is shown on the view page.
            // Was using MUST_EXIST, but what if they'd not viewed the course yet or were not enrolled - an error is shown!
            $data->access = $DB->get_field('user_lastaccess', 'timeaccess', ['courseid' => $courseid,
                'userid' => $USER->id]);
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

