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

/**
 * Contains class core_tag_renderer
 *
 * @package   core_tag
 * @copyright 2015 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
namespace theme_evagu\output\core;
use context_system;
use html_writer;
use core_tag_area;
use moodle_url;
use core_tag_collection;


/**
 * Class core_tag_renderer
 *
 * @package   core_tag
 * @copyright 2015 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tag_renderer extends \core_tag_renderer {

    /**
     * Renders the tag search page
     *
     * @param string $query
     * @param int $tagcollid
     * @return string
     */
    public function tag_search_page($query = '', $tagcollid = 0) {
        $rv = $this->output->heading(get_string('searchtags', 'tag'), 2);

        $searchbox = $this->search_form($query, $tagcollid);
        $rv .= html_writer::div($searchbox, '', array('id' => 'tag-search-box', 'class' => 'ccnSearchHuge'));

        $tagcloud = core_tag_collection::get_tag_cloud($tagcollid, false, 150, 'name', $query);
        $searchresults = '';
        if ($tagcloud->get_count()) {
            $searchresults = $this->output->render_from_template('theme_evagu/ccn_tag_index_cloud',
                    $tagcloud->export_for_template($this->output));
            $rv .= html_writer::div($searchresults, '', array('id' => 'tag-search-results', 'class' => 'ccnSepMd'));
        } else if (strval($query) !== '') {
            $rv .= '<div class="tag-search-empty">' . get_string('notagsfound', 'tag', s($query)) . '</div>';
        }

        return $rv;
    }

    /**
     * Renders the tag index page
     *
     * @param core_tag_tag $tag
     * @param \core_tag\output\tagindex[] $entities
     * @param int $tagareaid
     * @param bool $exclusivemode if set to true it means that no other entities tagged with this tag
     *             are displayed on the page and the per-page limit may be bigger
     * @param int $fromctx context id where the link was displayed, may be used by callbacks
     *            to display items in the same context first
     * @param int $ctx context id where to search for records
     * @param bool $rec search in subcontexts as well
     * @param int $page 0-based number of page being displayed
     * @return string
     */
    public function tag_index_page($tag, $entities, $tagareaid, $exclusivemode, $fromctx, $ctx, $rec, $page) {
        global $CFG;
        $this->page->requires->js_call_amd('core/tag', 'initTagindexPage');

        $tagname = $tag->get_display_name();
        $systemcontext = context_system::instance();

        if ($tag->flag > 0 && has_capability('moodle/tag:manage', $systemcontext)) {
            $tagname = '<span class="flagged-tag">' . $tagname . '</span>';
        }

        $rv = '';
        $rv .= $this->output->heading($tagname, 2);

        $rv .= $this->tag_links($tag);

        if ($desciption = $tag->get_formatted_description()) {
            $rv .= $this->output->box($desciption, 'generalbox tag-description');
        }

        $relatedtagslimit = 10;
        $relatedtags = $tag->get_related_tags();
        $taglist = new \core_tag\output\taglist($relatedtags, get_string('relatedtags', 'tag'),
                'tag-relatedtags', $relatedtagslimit);
        $rv .= $this->output->render_from_template('theme_evagu/ccn_tag_taglist_large',
                $taglist->export_for_template($this->output));

        // Display quick menu of the item types (if more than one item type found).
        $entitylinks = array();
        foreach ($entities as $entity) {
            if (!empty($entity->hascontent)) {
                $entitylinks[] = '<li><a href="#'.$entity->anchor.'">' .
                        core_tag_area::display_name($entity->component, $entity->itemtype) . '</a></li>';
            }
        }

        if (count($entitylinks) > 1) {
            $rv .= '<div class="tag-index-toc mb20"><ul class="inline-list">' . join('', $entitylinks) . '</ul></div>';
        } else if (!$entitylinks) {
            $rv .= '<div class="tag-noresults">' . get_string('noresultsfor', 'tag', $tagname) . '</div>';
        }

        // Display entities tagged with the tag.
        $content = '';
        foreach ($entities as $entity) {
            if (!empty($entity->hascontent)) {
              if(isset($_GET["ta"]) || isset($_GET["excl"])){
                $content .= $this->output->render_from_template('theme_evagu/ccn_tag_index_deeper', $entity->export_for_template($this->output));
              } else {
                $content .= $this->output->render_from_template('theme_evagu/ccn_tag_index', $entity->export_for_template($this->output));
              }
            }
        }

        if ($exclusivemode) {
            $rv .= $content;
        } else if ($content) {
            $rv .= html_writer::div($content, 'tag-index-items');
        }

        // Display back link if we are browsing one tag area.
        if ($tagareaid) {
            $url = $tag->get_view_url(0, $fromctx, $ctx, $rec);
            $rv .= '<div class="mbp_pagination">
                      <ul class="page_navigation">
                        <li class="page-item ccn-page-item-prev">
                          <a href="'.$url.'" class="page-link">
                            <span class="flaticon-left-arrow"></span> '.get_string('back').'
                          </a>
                        </li>
                      </ul>
                    </div>';
        }

        return $rv;
    }

    /**
     * Prints a box that contains the management links of a tag
     *
     * @param core_tag_tag $tag
     * @return string
     */
    protected function tag_links($tag) {
        if ($links = $tag->get_links()) {
            $content = '<ul class="inline-list"><li>' . implode('</li> <li>', $links) . '</li></ul>';
            return html_writer::div($content, 'tag-management-box mb10');
        }
        return '';
    }

    /**
     * Prints the tag search box
     *
     * @param string $query last search string
     * @param int $tagcollid last selected tag collection id
     * @return string
     */
    protected function search_form($query = '', $tagcollid = 0) {
        $searchurl = new moodle_url('/tag/search.php');
        $output = ' <form action="' . $searchurl . '">
                      <div class="input-group"><label class="accesshide" for="searchform_query">' . get_string('searchtags', 'tag') . '</label>
                        <input id="searchform_query" class="form-control" name="query" type="text" size="40" placeholder="'.get_string('searchtags', 'tag').'" value="' . s($query) . '" />';
                        $tagcolls = core_tag_collection::get_collections_menu(false, true, get_string('inalltagcoll', 'tag'));
                        if (count($tagcolls) > 1) {
                            $output .= '<label class="accesshide" for="searchform_tc">' . get_string('selectcoll', 'tag') . '</label>';
                            $output .= html_writer::select($tagcolls, 'tc', $tagcollid, null, array('id' => 'searchform_tc'));
                        }
        $output .= '    <div class="input-group-append">
                          <button class="btn btn-outline-secondary" name="go" type="submit"><span class="flaticon-magnifying-glass"></span></button>
                        </div>
                      </div>
                    </form>';

        return $output;
    }

}
