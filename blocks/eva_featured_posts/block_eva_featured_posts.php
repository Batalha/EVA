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
require_once($CFG->dirroot. '/theme/evagu/ccn/block_handler/ccn_block_handler.php');
require_once($CFG->dirroot. '/theme/evagu/ccn/blog_handler/ccn_blog_handler.php');

class block_eva_featured_posts extends block_base {

    /**
     * Start block instance.
     */
    function init() {
        $this->title = get_string('pluginname', 'block_eva_featured_posts');
    }

    /**
     * The block is usable in all pages
     */
     function applicable_formats() {
       $ccnBlockHandler = new ccnBlockHandler();
       return $ccnBlockHandler->ccnGetBlockApplicability(array('all'));
     }


    /**
     * Customize the block title dynamically.
     */

   function specialization() {
       global $CFG, $DB;

       include($CFG->dirroot . '/theme/evagu/ccn/block_handler/specialization.php');
       if (empty($this->config)) {
         $this->config = new \stdClass();
         $this->config->title = 'Featured Posts';
         // $this->config->subtitle = 'Cum doctus civibus efficiantur in imperdiet deterruisCum doctus civibus efficiantur in imperdiet deterruisset.';
         // $this->config->hover_text = 'Preview Course';
         // $this->config->hover_accent = 'Top Seller';
         // $this->config->button_text = 'View all courses';
         // $this->config->button_link = $CFG->wwwroot . '/course';
         // $this->config->course_image = '1';
         // $this->config->description = '0';
         // $this->config->price = '1';
         // $this->config->enrol_btn = '0';
         // $this->config->enrol_btn_text = 'Buy Now';
         // $this->config->courses = $ccnCourses;
         // $this->config->color_bg = 'rgb(0, 8, 70)';
         // $this->config->color_title = 'rgb(255,255,255)';
         // $this->config->color_subtitle = 'rgb(255,255,255)';
         // $this->config->color_course_title = 'rgb(255,255,255)';
         // $this->config->color_course_subtitle = 'rgb(255, 234, 193)';
         // $this->config->color_course_price = 'rgb(255, 0, 95)';
         // $this->config->color_button = 'rgb(255, 0, 95)';
         // $this->config->color_course_enrol_btn = '#79b530';
       }
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


        // print_object($this->config->posts);

        $text = '';

        $ccnBlogHandler = new ccnBlogHandler();

        foreach($this->config->posts as $post){
          $ccnGetPostDetails = $ccnBlogHandler->ccnGetPostDetails($post);
          print_object($ccnGetPostDetails);
          $text .= '<div class="item">
          <a href="'.format_text($data->$slide_url, FORMAT_HTML, array('filter' => true)).'">
<div class="blog_post">
<div class="thumb">
<img class="img-fluid w100" src="' . moodle_url::make_file_url("$CFG->wwwroot/pluginfile.php", "/{$this->context->id}/block_eva_featured_posts/slides/" . $i . '/' . $mainfile) . '" alt="">';
if($PAGE->theme->settings->blog_post_date != 1){
$text .='<span class="post_date">'.userdate($data->$slide_date, '%d %B', 0).'</span>';
}
$text .='
</div>
<div class="details">
<h5>'.format_text($data->$slide_subtitle, FORMAT_HTML, array('filter' => true)).'</h5>
<h4>'.format_text($data->$slide_title, FORMAT_HTML, array('filter' => true)).'</h4>
</div>
</div>
</a>
</div>';
        }

        if ($data->slidesnumber > 0) {
            $text = '		<section class="blog_post_container bgc-fa">
		<div class="container">
			<div class="row">
				<div class="col-lg-6 offset-lg-3">
					<div class="main-title text-center">';
						if(!empty($data->title)){
              $text .='<h3 class="mt0 mb0">'.format_text($data->title, FORMAT_HTML, array('filter' => true)).'</h3>';
            }
            $text .='
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<div class="feature_post_slider">
';
            $fs = get_file_storage();
            for ($i = 1; $i <= $data->slidesnumber; $i++) {
                $sliderimage = 'file_slide' . $i;
                $slide_title = 'slide_title' . $i;
                $slide_subtitle = 'slide_subtitle' . $i;
                $slide_date = 'slide_date' . $i;
                $slide_url = 'slide_url' . $i;

                if (!empty($data->$sliderimage)) {
                    $files = $fs->get_area_files($this->context->id, 'block_eva_featured_posts', 'slides', $i, 'sortorder DESC, id ASC', false, 0, 0, 1);
                    if (count($files) >= 1) {
                        $mainfile = reset($files);
                        $mainfile = $mainfile->get_filename();
                    } else {
                        continue;
                    }

                    $text .= '
                    <div class="item">
                    <a href="'.format_text($data->$slide_url, FORMAT_HTML, array('filter' => true)).'">
  <div class="blog_post">
    <div class="thumb">
      <img class="img-fluid w100" src="' . moodle_url::make_file_url("$CFG->wwwroot/pluginfile.php", "/{$this->context->id}/block_eva_featured_posts/slides/" . $i . '/' . $mainfile) . '" alt="">';
      if($PAGE->theme->settings->blog_post_date != 1){
        $text .='<span class="post_date">'.userdate($data->$slide_date, '%d %B', 0).'</span>';
      }
      $text .='
    </div>
    <div class="details">
      <h5>'.format_text($data->$slide_subtitle, FORMAT_HTML, array('filter' => true)).'</h5>
      <h4>'.format_text($data->$slide_title, FORMAT_HTML, array('filter' => true)).'</h4>
    </div>
  </div>
  </a>
</div>';
                }

            }
            $text .= '
            </div>
          				</div>
          			</div>
          		</div>
          	</section>';
        }

        $this->content = new stdClass;
        $this->content->footer = '';
        $this->content->text = $text;

        return $this->content;
  }

    /**
     * Serialize and store config data
     */
    function instance_config_save($data, $nolongerused = false) {
        global $CFG;

        $filemanageroptions = array('maxbytes'      => $CFG->maxbytes,
                                    'subdirs'       => 0,
                                    'maxfiles'      => 1,
                                    'accepted_types' => array('.jpg', '.png', '.gif'));

        for($i = 1; $i <= $data->slidesnumber; $i++) {
            $field = 'file_slide' . $i;
            if (!isset($data->$field)) {
                continue;
            }

            file_save_draft_area_files($data->$field, $this->context->id, 'block_eva_featured_posts', 'slides', $i, $filemanageroptions);
        }

        parent::instance_config_save($data, $nolongerused);
    }

    /**
     * When a block instance is deleted.
     */
    function instance_delete() {
        global $DB;
        $fs = get_file_storage();
        $fs->delete_area_files($this->context->id, 'block_eva_featured_posts');
        return true;
    }

    /**
     * Copy any block-specific data when copying to a new block instance.
     * @param int $fromid the id number of the block instance to copy from
     * @return boolean
     */
    public function instance_copy($fromid) {
        global $CFG;

        $fromcontext = context_block::instance($fromid);
        $fs = get_file_storage();

        if (!empty($this->config) && is_object($this->config)) {
            $data = $this->config;
            $data->slidesnumber = is_numeric($data->slidesnumber) ? (int)$data->slidesnumber : 0;
        } else {
            $data = new stdClass();
            $data->slidesnumber = 0;
        }

        $filemanageroptions = array('maxbytes'      => $CFG->maxbytes,
                                    'subdirs'       => 0,
                                    'maxfiles'      => 1,
                                    'accepted_types' => array('.jpg', '.png', '.gif'));

        for($i = 1; $i <= $data->slidesnumber; $i++) {
            $field = 'file_slide' . $i;
            if (!isset($data->$field)) {
                continue;
            }

            // This extra check if file area is empty adds one query if it is not empty but saves several if it is.
            if (!$fs->is_area_empty($fromcontext->id, 'block_eva_featured_posts', 'slides', $i, false)) {
                $draftitemid = 0;
                file_prepare_draft_area($draftitemid, $fromcontext->id, 'block_eva_featured_posts', 'slides', $i, $filemanageroptions);
                file_save_draft_area_files($draftitemid, $this->context->id, 'block_eva_featured_posts', 'slides', $i, $filemanageroptions);
            }
        }

        return true;
    }

    /**
     * The block should only be dockable when the title of the block is not empty
     * and when parent allows docking.
     *
     * @return bool
     */
    public function instance_can_be_docked() {
        return (!empty($this->config->title) && parent::instance_can_be_docked());
    }
    public function html_attributes() {
      global $CFG;
      $attributes = parent::html_attributes();
      include($CFG->dirroot . '/theme/evagu/ccn/block_handler/attributes.php');
      return $attributes;
    }

}