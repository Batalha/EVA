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
 * ${PLUGINNAME} file description here.
 *
 * @package    ${PLUGINNAME}
 * @copyright  2021 SysBind Ltd. <service@sysbind.co.il>
 * @auther     celio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_celio extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_celio');
    }

    function get_content() {

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content          = new stdClass;
        $this->content->text    = get_string('celiobody', 'block_celio');
        $this->content->text    .= get_string('celioconteudo', 'block_celio');
        $this->content->footer  = get_string('celiofooter', 'block_celio');

        return $this->content;
    }
}