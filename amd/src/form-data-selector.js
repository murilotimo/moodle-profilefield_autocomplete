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
 * Enrolled user selector module.
 *
 * @module     mod_forum/form-user-selector
 * @copyright  2019 Shamim Rezaie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/templates'], function ($, Ajax, Templates) {
   return /** @alias module:profilefield_autocomplete/form-data-selector */ {


      /**                                                                                                                         
       * Source of data for Ajax element.
       *
       * @param {String} selector The selector of the auto complete element.
       * @param {String} query The query string.                        
       * @param {Function} callback A callback function receiving an array of results.
       * @param {Function} failure A callback function to be called in case of failure, receiving the error message.
       * @return {Void}                                                                                                           
      */
      transport: function (selector, query, callback, failure) {
         return true;
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
      processResults: function (selector, results) {
         var users = [];
         $.each(results, function (index, user) {
            users.push({
               value: user.id,
               label: user._label
            });
         });
         return users;
      },


   }
});