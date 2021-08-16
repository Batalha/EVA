<?php
/*
@ccnRef: @block_eva/block.php
*/

defined('MOODLE_INTERNAL') || die();

// if (!($this->config)) {
//   if(!($this->content)){
//     $this->content = new \stdClass();
//   }
//     $this->content->text = '<h5 class="mb30">'.$this->title.'</h5>';
//     return $this->content->text;
// }

// print_object($this);
$ccnBlockType = $this->instance->blockname;

$ccnCollectionFullwidthTop =  array(
  "eva_about_1",
  "eva_about_2",
  "eva_accordion",
  "eva_action_panels",
  "eva_blog_recent_slider",
  "eva_boxes",
  "eva_event_list",
  "eva_event_list_2",
  "eva_faqs",
  "eva_features",
  "eva_gallery_video",
  "eva_parallax",
  "eva_parallax_apps",
  "eva_parallax_counters",
  "eva_parallax_features",
  "eva_parallax_testimonials",
  "eva_parallax_subscribe",
  "eva_parallax_subscribe_2",
  "eva_partners",
  "eva_parallax_white",
  "eva_pills",
  "eva_price_tables",
  "eva_price_tables_dark",
  "eva_services",
  "eva_services_dark",
  "eva_simple_counters",
  "eva_hero_1",
  "eva_hero_2",
  "eva_hero_3",
  "eva_hero_4",
  "eva_hero_5",
  "eva_hero_6",
  "eva_hero_7",
  "eva_slider_1",
  "eva_slider_1_v",
  "eva_slider_2",
  "eva_slider_3",
  "eva_slider_4",
  "eva_slider_5",
  "eva_slider_6",
  "eva_slider_7",
  "eva_slider_8",
  "eva_steps",
  "eva_steps_dark",
  "eva_subscribe",
  "eva_tablets",
  "eva_course_categories",
  "eva_course_categories_2",
  "eva_course_categories_3",
  "eva_course_categories_4",
  "eva_course_categories_5",
  "eva_course_grid",
  "eva_course_grid_2",
  "eva_course_grid_3",
  "eva_course_grid_4",
  "eva_course_grid_5",
  "eva_course_grid_6",
  "eva_course_grid_7",
  "eva_course_grid_8",
  "eva_featuredcourses",
  "eva_featured_posts",
  "eva_featured_video",
  "eva_featured_teacher",
  "eva_courses_slider",
  "eva_users_slider",
  "eva_users_slider_2",
  "eva_users_slider_2_dark",
  "eva_users_slider_round",
  "eva_tstmnls",
  "eva_tstmnls_2",
  "eva_tstmnls_3",
  "eva_tstmnls_4",
  "eva_tstmnls_5",
  "eva_tstmnls_6",
);

$ccnCollectionAboveContent =  array(
  "eva_contact_form",
  "eva_course_overview",
  "eva_tabs",
);

$ccnCollectionBelowContent =  array(
  "eva_course_rating",
  "eva_more_courses",
  "eva_course_instructor",
);

$ccnCollection = array_merge($ccnCollectionFullwidthTop, $ccnCollectionAboveContent, $ccnCollectionBelowContent);

if (empty($this->config)) {
  if(in_array($ccnBlockType, $ccnCollectionFullwidthTop)) {
    $this->instance->defaultregion = 'fullwidth-top';
    $this->instance->region = 'fullwidth-top';
    $DB->update_record('block_instances', $this->instance);
  }
  if(in_array($ccnBlockType, $ccnCollectionAboveContent)) {
    $this->instance->defaultregion = 'above-content';
    $this->instance->region = 'above-content';
    $DB->update_record('block_instances', $this->instance);
  }
  if(in_array($ccnBlockType, $ccnCollectionBelowContent)) {
    $this->instance->defaultregion = 'below-content';
    $this->instance->region = 'below-content';
    $DB->update_record('block_instances', $this->instance);
  }
  /* Begin Legacy */
  if(!in_array($ccnBlockType, $ccnCollection)) {
    if(!($this->content)){
       $this->content = new \stdClass();
    }
    $this->content->text = '<h5 class="mb30">'.$this->title.'</h5>';
    return $this->content->text;
  }
  /* End Legacy */
}
