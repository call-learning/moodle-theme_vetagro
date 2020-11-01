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
 * Presets management
 *
 * @package   theme_vetagro
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_vetagro\output;

use custom_menu;
use html_writer;
use moodle_url;
use theme_vetagro\local\utils;

defined('MOODLE_INTERNAL') || die;

/**
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 *
 * @package   theme_vetagro
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \theme_clboost\output\core_renderer {

    /**
     * Get additional info for mustache template
     *
     * @return \stdClass
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function get_template_additional_information() {
        $additionalinfo = parent::get_template_additional_information();
        $additionalinfo->addresses = utils::convert_addresses_config();
        $additionalinfo->membership = utils::convert_membership_config($this->page);
        $additionalinfo->randomimageurl = utils::get_random_image_url($this->page->theme->name);
        return $additionalinfo;
    }

    /**
     * Override the user menu just to show a button when user not connected
     *
     * @param stdClass $user A user object, usually $USER.
     * @param bool $withlinks true if a dropdown should be built.
     * @return string HTML fragment.
     */
    public function user_menu($user = null, $withlinks = null) {
        global $USER, $CFG;
        require_once($CFG->dirroot . '/user/lib.php');

        if (is_null($user)) {
            $user = $USER;
        }

        // Note: this behaviour is intended to match that of core_renderer::login_info,
        // but should not be considered to be good practice; layout options are
        // intended to be theme-specific. Please don't copy this snippet anywhere else.
        if (is_null($withlinks)) {
            $withlinks = empty($this->page->layout_options['nologinlinks']);
        }

        // Add a class for when $withlinks is false.
        $usermenuclasses = 'usermenu';
        if (!$withlinks) {
            $usermenuclasses .= ' withoutlinks';
        }

        $returnstr = "";

        // If during initial install, return the empty return string.
        if (during_initial_install()) {
            return $returnstr;
        }

        $loginpage = $this->is_login_page();
        $loginurl = get_login_url();
        // If not logged in, show the typical not-logged-in string.
        if (!isloggedin()) {
            $returnstr .= "<a href=\"$loginurl\">" . get_string('login') . '</a>';
            return html_writer::div(
                html_writer::span(
                    \html_writer::link($loginurl, get_string('login'), array('class' => 'btn btn-primary'))
                ),
                $usermenuclasses
            );

        }
        return parent::user_menu($user, $withlinks);
    }

    /**
     * Returns the custom menu if one has been set
     *
     * Same as the core routine except we add further automatic menu (like catalog at the end)
     *
     * Theme developers: DO NOT OVERRIDE! Please override function
     * {@link core_renderer::render_custom_menu()} instead.
     *
     * @param string $custommenuitems - custom menuitems set by theme instead of global theme settings
     * @return string
     *
     * TODO : this should be overridable more easily (core issue)
     */
    public function custom_menu($custommenuitems = '') {
        global $CFG;

        if (empty($custommenuitems) && !empty($CFG->custommenuitems)) {
            $custommenuitems = $CFG->custommenuitems;
        }
        list($urltext, $url) = \local_resourcelibrary\locallib\utils::get_catalog_url();
        $custommenuitems .= "\n{$urltext}|{$url}";

        $custommenu = new custom_menu($custommenuitems, current_language());
        return $this->render_custom_menu($custommenu);
    }

    /**
     * We want to show the custom menus as a list of links in the footer on small screens.
     *
     * Just return the menu object exported so we can render it differently.
     * This is slightly different from core in two ways:
     *  - we add resource library menu
     *  - mark menu as external menus so we can style them differently.
     *
     * TODO : this should be overridable more easily (core issue)
     */
    public function custom_menu_flat() {
        global $CFG;
        $custommenuitems = '';

        if (empty($custommenuitems) && !empty($CFG->custommenuitems)) {
            $custommenuitems = $CFG->custommenuitems;
        }
        list($urltext, $url) = \local_resourcelibrary\locallib\utils::get_catalog_url();
        $custommenuitems .= "\n{$urltext}|{$url}";

        $custommenu = new custom_menu($custommenuitems, current_language());
        $langs = get_string_manager()->get_list_of_translations();
        $haslangmenu = $this->lang_menu() != '';

        if ($haslangmenu) {
            $strlang = get_string('language');
            $currentlang = current_language();
            if (isset($langs[$currentlang])) {
                $currentlang = $langs[$currentlang];
            } else {
                $currentlang = $strlang;
            }
            $this->language = $custommenu->add($currentlang, new moodle_url('#'), $strlang, 10000);
            foreach ($langs as $langtype => $langname) {
                $this->language->add($langname, new moodle_url($this->page->url, array('lang' => $langtype)), $langname);
            }
        }
        // Mark external links.
        $exportedlinks = $custommenu->export_for_template($this);
        $this->mark_external_link($exportedlinks);

        return $custommenu->export_for_template($this);
    }


    /**
     * Mark the link as an external link so we can style it.
     *
     * @param array $link
     */
    private function mark_external_link(&$link) {
        global $CFG;
        $link->isexternal = false;
        if ($link->url && (get_host_from_url($link->url) != get_host_from_url($CFG->wwwroot))) {
            $link->isexternal = true;
        }
        foreach ($link->children as $child) {
            $this->mark_external_link($child);
        }
    }

}