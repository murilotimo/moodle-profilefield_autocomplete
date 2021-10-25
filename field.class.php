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
 * @package    profilefield_autocomplete
 * @category   profilefield
 * @copyright  2021 Murilo Timo Neto
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class profile_field_autocomplete extends profile_field_base {

    /** @var array $selected_options */
    public $selected_options;

    /** @var array $selected_options */
    public $sql;

    
    /**
     * Constructor
     *
     * Pulls out the options for myprofilefield from the database and sets the
     * the corresponding key for the data if it exists
     *
     * @param int $fieldid id of user profile field
     * @param int $userid id of user
     */
    public function __construct($fieldid=0, $userid=0, $fielddata=null) {
        global $DB;
        parent::__construct($fieldid, $userid, $fielddata=null);

        if (!empty($this->field)) {
            
            $datafield = $DB->get_field(
                'user_info_data', 
                'data', 
                array('userid' => $this->userid, 
                'fieldid' => $this->fieldid)
            );

            if ($datafield !== false) {
                $this->data = explode("|", $datafield);

                list($id,$label,$table) = explode(',', $this->field->param1);
                $this->sql = "SELECT $id AS ID, $label AS DATA FROM $table";

                list($insql, $inparams) = $DB->get_in_or_equal($this->data);
                $this->selected_options = $DB->get_records_sql_menu(
                    $this->sql . " WHERE $id " . $insql,
                    $inparams
                );
               
            } else {
                $this->data = $this->field->defaultdata;
            }
        }
    }

    /**
     * Returns the options available as an array.
     *
     * @return array
     */
    public function get_options_array() : array {
        global $DB;
        $sql = $this->sql;
        $options = array();

        if ($sql) {
            $resultset = $DB->get_records_sql_menu($sql, $params=null, $limitfrom=0, $limitnum=10);
            $options = $resultset + $this->selected_options;
        }

        return $options;
    }

    /**
     * Adds the profile field to the moodle form class
     *
     * @param moodleform $mform instance of the moodleform class
     */
    function edit_field_add($mform) {
        $options = $this->get_options_array();
        $formattedoptions = $options;
        
        $attributes = [
            'multiple' => true,
            'noselectionstring' => format_string($this->field->description),
            'placeholder' => format_string($this->field->name),
            'ajax' => 'profilefield_autocomplete/form-data-selector'
        ];
        
        $mform->addElement(
            'autocomplete',
            $this->inputname,
            format_string($this->field->name),
            $formattedoptions,
            $attributes
        );
    }

    /**
     * Display the data for this field
     *
     * @return string data for custom profile field.
     */
    function display_data() {
        $html = html_writer::start_tag('ul');
        foreach ($this->selected_options as $key => $value) {
            $html .= html_writer::tag('li', $value, ['data-id'=>$key]);
        }
        $html .= html_writer::end_tag('ul');
        return $html;
    }

    /**
     * Sets the default data for the field in the form object
     *
     * @param moodleform $mform instance of the moodleform class
     */
    function edit_field_set_default(&$mform) {
        if (!empty($default)) {
            $mform->setDefault($this->inputname, $this->field->defaultdata);
        }
    }


    /**
     * Process the data before it gets saved in database
     *
     * @param stdClass $data from the add/edit profile field form
     * @param stdClass $datarecord The object that will be used to save the record
     * @return stdClass
     */
    function edit_save_data_preprocess($data, &$datarecord) {
        $string = '';
        if (is_array($data)){
            $string = implode("|",$data);
        }
        return $string;
    }

    /**
     * HardFreeze the field if locked.
     *
     * @param moodleform $mform instance of the moodleform class
     */
    function edit_field_set_locked(&$mform) {
        if (!$mform->elementExists($this->inputname)) {
            return;
        }
        if ($this->is_locked() and !has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM))) {
            $mform->hardFreeze($this->inputname);
            $mform->setConstant($this->inputname, $this->data);
        }
    }
}