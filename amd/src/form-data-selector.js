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
 * Profile Field Autocomplete plugin.
 *
 * @module     profilefield_autocomplete/form-user-selector
 * @copyright  2021 Murilo Timo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax'], function ($, Ajax) {
   return /** @alias module:profilefield_autocomplete/form-data-selector */ {
      processResults: function (selector, results) {
         var options = [];
         $.each(results, function (index, data) {
            options.push({
               value: data.id,
               label: data.data
            });
         });
         return options;
      },

      /**
       * Source of data for Ajax element.
       *
       * @param {String} selector The selector of the auto complete element.
       * @param {String} query The query string.
       * @param {Function} callback A callback function receiving an array of results.
       * @param {Function} failure A callback function to be called in case of failure, receiving the error message.
       * @return {Void}
      */
      transport: function (selector, query, success, failure) {
         var fieldname = selector.replace('#id_profile_field_','');
         var promise;

         promise = Ajax.call([{
            methodname: 'profilefield_autocomplete_search_data',
            args: {
               'search': query,
               'fieldname': fieldname
            }
         }]);

         promise[0].then(success).fail(failure);
      }
   };
});