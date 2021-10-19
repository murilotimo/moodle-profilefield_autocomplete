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
 * Format tiles external functions
 *
 * @package    format_tiles
 * @category   external
 * @copyright  2018 David Watson {@link http://evolutioncode.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.3
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

    //function get_field_id_by_name()


    function search_data ($q, $fieldname) {
        global $USER, $DB;
        $DB->set_debug(true);

        $profilefields = profile_get_user_fields_with_data($USER->id);
        $field = profile_get_custom_field_data_by_shortname($fieldname);

        $sql = $field->param1;
        $like1 = $DB->sql_like(
            'description',
            ':description', 
            $casesensitive = false, 
            $accentsensitive = false
        );

        $result = $DB->get_records_sql(
            $sql . ' WHERE ' . $like1,
            ['description'=>'%'.$q.'%'],
            $limitfrom=0, 
            $limitnum=50
        );

        //var_dump($result);
        //var_dump('$field', $field);
        //var_dump($USER);
        return $result;
    }


}