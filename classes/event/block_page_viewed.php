<?php
namespace block_superframe\event;
/**
 * The block page viewed event class
 *
 * If the view mode needs to be stored as well, you may need to
 * override methods get_url() and get_legacy_log_data(), too.
 *
 */
class block_page_viewed extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    public static function get_name() {
        return get_string('pageviewed', 'block_superframe');
    }
    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' has
                viewed a page with the id '$this->objectid'
                in the block Super frame with course
                module id '$this->contextinstanceid'.";
    }

}