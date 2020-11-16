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
 * All constant in one place
 *
 * @package   theme_vetagro
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_vetagro\local;

use coding_exception;
use context_system;
use dml_exception;
use moodle_exception;
use moodle_url;
use stored_file;

defined('MOODLE_INTERNAL') || die;

/**
 * Theme constants. In one place.
 *
 * @package   theme_vetagro
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class utils {
    /**
     * Random image file area name
     */
    const RANDOM_IMAGE_FILE_AREA = 'randomimage';

    /**
     * Converts the addresses config string into an array of information that can be
     * then added to the footer via the "footer_address" mustache template.
     * Structure:
     *     addresslabel|address|tel
     *
     * Example structure:
     *     Campus agronomique|89 Avenue de l’Europe, 63370 Lempdes|04 73 98 13 13
     *
     * Converted into: an object with title, address, tel fields.
     *
     * @return array
     * @throws dml_exception
     */
    public static function convert_addresses_config() {
        $configtext = get_config('theme_vetagro', 'addresses');

        $lineparser = function($setting, $index, &$currentobject) {
            if (!empty($setting)) {
                switch ($index) {
                    case 0:
                        $currentobject->title = $setting;
                        break;
                    case 1:
                        $currentobject->address = $setting;
                        break;
                    case 2:
                        $currentobject->tel = $setting;
                        break;
                }
            }
        };
        return \theme_clboost\local\utils::convert_from_config($configtext, $lineparser);
    }

    /**
     * Converts the membership config string into an array of information that can be
     * then added to the footer via the "footer_address" mustache template.
     * Structure:
     *     addresslabel|address|tel
     *
     * Example structure:
     *     Etablissement sous tutelle du ministère de l\'Agriculture et de
     * l\'alimentation,[[pix:theme_vetagro|ministere-agriculture-alimentation]]
     *
     * Converted into: an object with title and absolute url
     *
     * @param moodle_page $page
     * @return array
     * @throws dml_exception
     */
    public static function convert_membership_config($page) {
        $configtext = get_config('theme_vetagro', 'membership');

        $lineparser = function($setting, $index, &$currentobject) use ($page) {
            if (!empty($setting)) {
                switch ($index) {
                    case 0:
                        $currentobject->title = $setting;
                        break;
                    case 1:
                        $currentobject->url = '';
                        if (strpos($setting, '[[pix:') === 0) {
                            $matches = [];
                            preg_match('/\[\[pix:(.+)\|(.+)\]\]/', $setting, $matches);
                            if ($matches) {
                                $currentobject->url = $page->theme->image_url($matches[2], $matches[1]);
                            }

                        } else {
                            try {
                                $currentobject->url = (new moodle_url($setting))->out();
                            } catch (moodle_exception $e) {
                                $currentobject->url = new moodle_url('');
                            }
                        }
                        break;
                }
            }
        };
        // Line separator is comma as we use '|' for the url information.
        return \theme_clboost\local\utils::convert_from_config($configtext, $lineparser, ',');
    }

    /**
     * Get random image URL
     *
     * @param string $themename
     * @return moodle_url
     * @throws coding_exception
     * @throws dml_exception
     */
    public static function get_random_image_url($themename) {
        $fs = get_file_storage();
        $syscontextid = context_system::instance()->id;
        $allfiles = $fs->get_area_files($syscontextid,
            'theme_' . $themename,
            'randomimage');

        $filesurl = [
            new moodle_url("/theme/{$themename}/pix/bg-right.jpg")
        ];
        foreach ($allfiles as $file) {
            if ($file->is_valid_image()) {
                $filesurl[] = moodle_url::make_pluginfile_url(
                    $syscontextid,
                    'theme_' . $themename,
                    self::RANDOM_IMAGE_FILE_AREA,
                    0,
                    $file->get_filepath(),
                    $file->get_filename()
                );
            }
        }
        $randomindex = random_int(1, count($filesurl)) - 1;
        return $filesurl[$randomindex];
    }
}