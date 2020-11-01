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
 * Unit tests for theme_clboost\local\utils
 *
 * @package   theme_vetagro
 * @category   test
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use theme_vetagro\local\utils;

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for theme_vetagro\local\utils
 *
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_clboost_utils_test extends advanced_testcase {

    /**
     * @var stored_file $file file added to the randomimage filearea
     */
    protected $file;

    public function setUp() {
        global $CFG;
        parent::setUp();
        $fs = get_file_storage();
        $syscontext = context_system::instance();
        $filerecord = array(
            'contextid' => $syscontext->id,
            'component' => 'theme_vetagro',
            'filearea' => utils::RANDOM_IMAGE_FILE_AREA,
            'filename' => 'testimage.jpg',
            'itemid' => 0,
            'filepath' => '/',
        );
        $filepath = $CFG->dirroot . '/theme/vetagro/tests/fixtures/testimage.jpg';
        $this->file = $fs->create_file_from_pathname($filerecord, $filepath);

    }

    public function tearDown() {
        parent::tearDown();
        $this->file->delete();
    }

    /**
     * Check that the image is random
     *
     * @throws coding_exception
     * @throws dml_exception
     */
    public function test_get_random_image() {
        global $CFG;
        $this->resetAfterTest();
        $theme = theme_config::load('clboost');

        $imageurl1 = \theme_vetagro\local\utils::get_random_image_url('vetagro');
        $imageurl2 = \theme_vetagro\local\utils::get_random_image_url('vetagro');

        $possibleimage = array('bg-right.jpg', 'testimage.jpg');
        $this->assertTrue(in_array(basename($imageurl1->get_path()), $possibleimage));
        $this->assertTrue(in_array(basename($imageurl2->get_path()), $possibleimage));
    }
}
