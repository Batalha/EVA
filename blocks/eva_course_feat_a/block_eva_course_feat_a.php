<?php
require_once($CFG->dirroot. '/theme/evagu/ccn/block_handler/ccn_block_handler.php');

class block_eva_course_feat_a extends block_base {

    /**
     * Start block instance.
     */
    function init() {
        $this->title = get_string('pluginname', 'block_eva_course_feat_a');
    }

    /**
     * The block is usable in all pages
     */
     function applicable_formats() {
       $ccnBlockHandler = new ccnBlockHandler();
       return $ccnBlockHandler->ccnGetBlockApplicability(array('course-view'));
     }

    /**
     * Customize the block title dynamically.
     */
    function specialization() {
        // if (isset($this->config->title)) {
        //     $this->title = $this->title = format_string($this->config->title, true, ['context' => $this->context]);
        // }
        global $CFG;
        include($CFG->dirroot . '/theme/evagu/ccn/block_handler/specialization.php');
    }

    /**
     * The block can be used repeatedly in a page.
     */
    function instance_allow_multiple() {
        return true;
    }

    /**
     * Build the block content.
     */
    function get_content() {
        global $CFG, $PAGE;

        require_once($CFG->libdir . '/filelib.php');


        if ($this->content !== NULL) {
            return $this->content;
        }

        if (!empty($this->config) && is_object($this->config)) {
            $data = $this->config;
            $data->slidesnumber = is_numeric($data->slidesnumber) ? (int)$data->slidesnumber : 0;
        } else {
            $data = new stdClass();
            $data->slidesnumber = 0;
        }
            $text = '
            <div class="feature_course_widget">
              <ul class="list-group">
                <h4 class="title">'. format_text($data->title, FORMAT_HTML, array('filter' => true)) .'</h4>';
                if ($data->slidesnumber > 0) {       for ($i = 1; $i <= $data->slidesnumber; $i++) {
                $item_title = 'item_title' . $i;
                $item_subtitle = 'item_subtitle' . $i;
                $text .= '
                <li class="d-flex justify-content-between align-items-center">
                    '.format_text($data->$item_title, FORMAT_HTML, array('filter' => true)).' <span class="float-right">'.format_text($data->$item_subtitle, FORMAT_HTML, array('filter' => true)).'</span>
                </li>';
              } }
            $text .= '
		          </ul>
	          </div>';

        $this->content = new stdClass;
        $this->content->footer = '';
        $this->content->text = $text;

        return $this->content;

  }


    /**
     * Serialize and store config data
     */
    // function instance_config_save($data, $nolongerused = false) {
    //     global $CFG;
    //
    //     $filemanageroptions = array('maxbytes'      => $CFG->maxbytes,
    //                                 'subdirs'       => 0,
    //                                 'maxfiles'      => 1,
    //                                 'accepted_types' => array('.jpg', '.png', '.gif'));
    //
    //     for($i = 1; $i <= $data->slidesnumber; $i++) {
    //         $field = 'file_slide' . $i;
    //         if (!isset($data->$field)) {
    //             continue;
    //         }
    //
    //         file_save_draft_area_files($data->$field, $this->context->id, 'block_eva_course_feat_a', 'slides', $i, $filemanageroptions);
    //     }
    //
    //     parent::instance_config_save($data, $nolongerused);
    // }

    /**
     * When a block instance is deleted.
     */
    // function instance_delete() {
    //     global $DB;
    //     $fs = get_file_storage();
    //     $fs->delete_area_files($this->context->id, 'block_eva_course_feat_a');
    //     return true;
    // }

    /**
     * Copy any block-specific data when copying to a new block instance.
     * @param int $fromid the id number of the block instance to copy from
     * @return boolean
     */
    // public function instance_copy($fromid) {
    //     global $CFG;
    //
    //     $fromcontext = context_block::instance($fromid);
    //     $fs = get_file_storage();
    //
    //     if (!empty($this->config) && is_object($this->config)) {
    //         $data = $this->config;
    //         $data->slidesnumber = is_numeric($data->slidesnumber) ? (int)$data->slidesnumber : 0;
    //     } else {
    //         $data = new stdClass();
    //         $data->slidesnumber = 0;
    //     }
    //
    //     $filemanageroptions = array('maxbytes'      => $CFG->maxbytes,
    //                                 'subdirs'       => 0,
    //                                 'maxfiles'      => 1,
    //                                 'accepted_types' => array('.jpg', '.png', '.gif'));
    //
    //     for($i = 1; $i <= $data->slidesnumber; $i++) {
    //         $field = 'file_slide' . $i;
    //         if (!isset($data->$field)) {
    //             continue;
    //         }
    //
    //         // This extra check if file area is empty adds one query if it is not empty but saves several if it is.
    //         if (!$fs->is_area_empty($fromcontext->id, 'block_eva_course_feat_a', 'slides', $i, false)) {
    //             $draftitemid = 0;
    //             file_prepare_draft_area($draftitemid, $fromcontext->id, 'block_eva_course_feat_a', 'slides', $i, $filemanageroptions);
    //             file_save_draft_area_files($draftitemid, $this->context->id, 'block_eva_course_feat_a', 'slides', $i, $filemanageroptions);
    //         }
    //     }
    //
    //     return true;
    // }

    /**
     * The block should only be dockable when the title of the block is not empty
     * and when parent allows docking.
     *
     * @return bool
     */
    // public function instance_can_be_docked() {
    //     return (!empty($this->config->title) && parent::instance_can_be_docked());
    // }

    public function html_attributes() {
      global $CFG;
      $attributes = parent::html_attributes();
      include($CFG->dirroot . '/theme/evagu/ccn/block_handler/attributes.php');
      return $attributes;
    }

}
