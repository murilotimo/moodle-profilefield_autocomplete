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
 * Contains definition of cutsom user profile field.
 *
 * @package    profilefield_autocomplete
 * @category   profilefield
 * @copyright  2021 Murilo Timo Neto
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class profile_define_autocomplete extends profile_define_base
{

    /**
     * Prints out the form snippet for the part of creating or
     * editing a profile field specific to the current data type
     *
     * @param moodleform $form reference to moodleform for adding elements.
     */
    function define_form_specific(\MoodleQuickForm $mform)
    {
        $mform->addElement(
            'text',
            'param1',
            get_string('sqlfields', 'profilefield_autocomplete'),
            'size="50"'
        );
        $mform->setType('param1', PARAM_RAW);
        $mform->setDefault('param1', ''); // defaults to ''
        $mform->addHelpButton(
            'param1',
            'sqlfields',
            'profilefield_autocomplete'
        );
    }

    /**
     * Validate the data from the add/edit profile field form
     * that is specific to the current data type
     *
     * @param object $data from the add/edit profile field form
     * @param object $files files uploaded
     * @return array associative array of error messages
     */
    function define_validate_specific($data, $files)
    {
        global $DB;
        $err = array();
        try {
            list($id,$label,$table) = explode(',', $data->param1);
            $sql = "SELECT $id AS ID, $label AS DATA FROM $table";

            if (!isset($sql) || $sql == '') {
                $err['configdata[param1]'] = get_string('err_required', 'form');
            } else {
                $resultset = $DB->get_records_sql($sql);
                if (!$resultset) {
                    $err['configdata[param1]'] = get_string('queryerrorfalse', 'profilefield_autocomplete');
                } else {
                    if (count($resultset) == 0) {
                        $err['configdata[param1]'] = get_string('queryerrorempty', 'profilefield_autocomplete');
                    } else {
                        $firstval = reset($resultset);
                        if (!object_property_exists($firstval, 'id')) {
                            $err['configdata[param1]'] = get_string('queryerroridmissing', 'profilefield_autocomplete');
                        } else {
                            if (!object_property_exists($firstval, 'data')) {
                                $err['configdata[param1]'] = get_string('queryerrordatamissing', 'profilefield_autocomplete');
                            } else if (!empty($data->configdata['defaultvalue'])) {
                                // Def missing.
                                $defaultvalue = $data->configdata['defaultvalue'];
                                $options = array_column($resultset, 'data', 'id');
                                $values = explode(',', $defaultvalue);

                                if ($data->configdata['param2'] == 0 && count($values) > 1) {
                                    $err['configdata[defaultvalue]'] = get_string(
                                        'queryerrormulipledefault',
                                        'profilefield_autocomplete',
                                        count($values)
                                    );
                                } else if ($data->configdata['param2'] == 0 && !array_key_exists($defaultvalue, $options)) {
                                    $err['configdata[defaultvalue]'] = get_string(
                                        'queryerrordefaultmissing',
                                        'profilefield_autocomplete',
                                        $defaultvalue
                                    );
                                } else {
                                    foreach ($values as $val) {
                                        if (!array_key_exists($val, $options)) {
                                            $err['configdata[defaultvalue]'] = get_string(
                                                'queryerrordefaultmissing',
                                                'profilefield_autocomplete',
                                                $val
                                            );
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $err['configdata[param1]'] = get_string(
                'sqlerror',
                'profilefield_autocomplete'
            ) . ': ' . $e->getMessage();
        }
        return $err;
    }
}
