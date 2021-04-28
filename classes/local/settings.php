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

use admin_setting_configstoredfile;
use admin_setting_configtextarea;
use admin_settingpage;

defined('MOODLE_INTERNAL') || die;

/**
 * Theme settings. In one place.
 *
 * @package   theme_vetagro
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class settings extends \theme_clboost\local\settings {

    /**
     * Additional settings
     *
     * This is intended to be overriden in the subtheme to add new pages for example.
     *
     * @param admin_settingpage $settings
     */
    protected static function additional_settings(admin_settingpage &$settings) {
        // Advanced settings.
        $page = new admin_settingpage('additionalinfo',
            static::get_string('additionalinfo', 'theme_vetagro'));

        $setting = new \admin_setting_configcheckbox('theme_vetagro/isvetagrotice',
            static::get_string('isvetagrotice', 'theme_vetagro'),
            static::get_string('isvetagrotice_desc', 'theme_vetagro'),
            false,
            PARAM_BOOL);
        $page->add($setting);

        $setting = new admin_setting_configtextarea('theme_vetagro/addresses',
            static::get_string('addresses', 'theme_vetagro'),
            static::get_string('addresses_desc', 'theme_vetagro'),
            "Campus agronomique|89 Avenue de l’Europe, 63370 Lempdes|04 73 98 13 13\n" .
            "Campus vétérinaire|1 avenue Bourgelat, 69280 Marcy-l’Étoile|04 78 87 25 25",
            PARAM_RAW);
        $page->add($setting);

        $setting = new admin_setting_configtextarea('theme_vetagro/usefullinks',
            static::get_string('usefullinks', 'theme_vetagro'),
            static::get_string('usefullinks_desc', 'theme_vetagro'),
            "Nous contacter|http://www.vetagro-sup.fr/nous-contacter/\n" .
            "Plan d'accès|http://www.vetagro-sup.fr/plan-acces/",
            PARAM_RAW);
        $page->add($setting);

        $setting = new admin_setting_configtextarea('theme_vetagro/membership',
            static::get_string('membership', 'theme_vetagro'),
            static::get_string('membership_desc', 'theme_vetagro'),
            "Etablissement sous tutelle du ministère de l'Agriculture et de".
            " l'alimentation,[[pix:theme_vetagro|ministere-agriculture-alimentation]]",
            PARAM_RAW);
        $page->add($setting);

        $setting = new admin_setting_configstoredfile('theme_vetagro/randomimage',
            static::get_string('randomimage', 'theme_vetagro'),
            static::get_string('randomimage_desc', 'theme_vetagro'),
            utils::RANDOM_IMAGE_FILE_AREA,
            0,
            ['maxfiles' => -1]);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $setting = new admin_setting_configstoredfile('theme_vetagro/frontpageimage',
            static::get_string('frontpageimage', 'theme_vetagro'),
            static::get_string('frontpageimage_desc', 'theme_vetagro'),
            utils::FRONTPAGE_IMAGE_FILE_AREA);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $settings->add($page);
    }
}