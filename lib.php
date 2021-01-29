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
 * Theme plugin version definition.
 *
 * @package   theme_vetagro
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use theme_vetagro\local\utils;

defined('MOODLE_INTERNAL') || die();

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 * @throws coding_exception
 */
function theme_vetagro_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    return theme_clboost\local\utils::generic_pluginfile('vetagro',
        $course, $cm, $context, $filearea, $args, $forcedownload, $options);
}

/**
 * Inject additional SCSS.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_vetagro_get_extra_scss($theme) {
    $extracss = theme_clboost_get_extra_scss($theme);

    $frontpageimagesurls = utils::get_frontpage_images_url($theme->name);
    if (empty($frontpageimagesurls)) {
        $frontpageimagesurls[utils::IMAGE_SIZE_TYPE_NORMAL] = '[[pix:theme|home/background-home]]';
        $frontpageimagesurls[utils::IMAGE_SIZE_TYPE_LG] = '[[pix:theme|home/background-home2x]]';
        $frontpageimagesurls[utils::IMAGE_SIZE_TYPE_XL] = '[[pix:theme|home/background-home3x]]';
    }
    $fpimagedef = '
    #page-site-index {
        .fp-header {';
    foreach ($frontpageimagesurls as $type => $fpdef) {
        $bgdef = "background-image: url($fpdef);";
        if ($type != utils::IMAGE_SIZE_TYPE_NORMAL) {
            $fpimagedef .= " @include media-breakpoint-up($type) {
                $bgdef
             }";
        } else {
            $fpimagedef .= $bgdef;
        }

    }
    $fpimagedef .= '
        }
    }';
    return $extracss . $fpimagedef;
}

/**
 * Map icons for font-awesome themes.
 */
function theme_vetagro_get_fontawesome_icon_map() {
    return [
        'theme_vetagro:teacherdb' => 'fa-cogs'
    ];
}

