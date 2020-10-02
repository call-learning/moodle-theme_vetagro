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

defined('MOODLE_INTERNAL') || die;

/**
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 *
 * @package   theme_envf
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \theme_clboost\output\core_renderer {

    public function get_template_additional_information() {
        $additionalinfo = parent::get_template_additional_information();
        $additionalinfo->addresses = static::convert_addresses_config();
        return $additionalinfo;
    }

    /**
     * Converts a string into a structured array of addresses which can
     * then be added to the footer.
     *
     * Structure:
     *     addresslabel|address|tel
     *
     * Example structure:
     *     Campus agronomique|89 Avenue de l’Europe, 63370 Lempdes|04 73 98 13 13
     *
     * @return array
     */
    protected static function convert_addresses_config() {
        $configtext = get_config('theme_vetagro', 'addresses');
        $lines = explode("\n", $configtext);
        $addresses = [];
        foreach ($lines as $linenumber => $line) {
            $line = trim($line);
            if (strlen($line) == 0) {
                continue;
            }
            // Parse item settings.
            $addresslabel = null;
            $address = null;
            $tel = null;
            $settings = explode('|', $line);
            foreach ($settings as $i => $setting) {
                $setting = trim($setting);
                if (!empty($setting)) {
                    switch ($i) {
                        case 0:
                            $addresslabel = $setting;
                            break;
                        case 1:
                            $address = $setting;
                            break;
                        case 2:
                            $tel = $setting;
                            break;
                    }
                }
            }
            $addresses[] = (object) [
                'title' => $addresslabel,
                'address' => $address,
                'tel' => $tel
            ];
        }
        return $addresses;
    }
}