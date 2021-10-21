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

defined('MOODLE_INTERNAL') || die;
global $CFG;
require_once("$CFG->libdir/externallib.php");
require_once("$CFG->dirroot/user/profile/lib.php");

/**
 * 
 * @package    profilefield_autocomplete
 * @category   profilefield
 * @copyright  2021 Murilo Timo Neto
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class profilefield_autocomplete_external extends external_api
{


    public static function search_data_parameters() {
        return new external_function_parameters(
            array(
               'search' => new external_value(PARAM_TEXT, 'O texto da pesquisa'),
               'fieldname'=> new external_value(PARAM_TEXT, 'Nome do campo de pefil')
            )
        );
    }

    public static function search_data_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_TEXT, 'group record id'),
                    'data' => new external_value(PARAM_TEXT, 'multilang compatible name'),
                )
            )
        );
    }

    function search_data ($q, $fieldname) {
        global $DB;

        $fields = profile_get_custom_fields();

        $field = null;
        foreach ($fields as $ifield) {
            if ($ifield->shortname === $fieldname) {
                $field = $ifield;
            }
        }

        list($id,$label,$table) = explode(',', $field->param1);
        $sql = "SELECT $id AS ID, $label AS DATA FROM $table";
        
        $like = $DB->sql_like(
            $label,
            ":$label", 
            $casesensitive = false, 
            $accentsensitive = false
        );

        $result = $DB->get_records_sql(
            $sql . ' WHERE ' . $like,
            [$label=>'%'.$q.'%'],
            $limitfrom=0,
            $limitnum=50
        );

        return $result;
    }


}