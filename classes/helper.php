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
 * Tool for sorting question categories in alphabetical order.
 *
 * @package    qbank_resortcategory
 * @copyright  2016 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qbank_resortcategory;

use context;

/**
 * Helper classClass for working with question category
 *
 * @package    qbank_resortcategory
 * @copyright  2016 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Sort category and all subcategories.
     *
     * @param string $categorypluscontext
     */
    public static function resort_category($categorypluscontext) {
        $parts = explode(',', $categorypluscontext);
        $categoryid = $parts[0];
        $contextid = $parts[1];
        $context = context::instance_by_id($contextid);
        require_capability('moodle/question:managecategory', $context);
        static::resort_category_recursive($categoryid, $contextid);
    }

    /**
     * Sort category and all subcategories.
     *
     * @param int $categoryid
     * @param int $contextid
     */
    private static function resort_category_recursive($categoryid, $contextid) {
        global $DB;

        $subcategories = $DB->get_records('question_categories',
                ['parent' => $categoryid, 'contextid' => $contextid], 'name ASC');
        $sortorder = 1;
        foreach ($subcategories as $subcategory) {
            static::resort_category_recursive($subcategory->id, $contextid);
            if ($subcategory->sortorder != $sortorder) {
                $subcategory->sortorder = $sortorder;
                $DB->update_record('question_categories', $subcategory);
            }
            $sortorder++;
        }
    }
}
