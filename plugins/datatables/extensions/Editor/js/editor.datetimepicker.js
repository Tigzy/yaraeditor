/**
 * Date and time picker in Editor, Bootstrap style. This plug-in provides
 * integration between
 * [Bootstrap DateTimePicker](http://www.malot.fr/bootstrap-datetimepicker/)
 * control and Editor. Fields can use this control by specifying `datetime` as
 * the Editor field type.
 *
 * @name Bootstrap DateTimePicker (1)
 * @summary Date and time input selector styled with Bootstrap
 * @requires [Bootstrap DateTimePicker](http://www.malot.fr/bootstrap-datetimepicker/)
 *
 * @opt `e-type object` **`opts`**: DateTimePicker initialisation options
 *     object. Please refer to the Bootstrap DateTimePicker documentation for
 *     the full range of options available.
 * @opt `e-type object` **`attrs`**: Attributes that are applied to the
 *     `-tag input` element used for the date picker.
 *
 * @method **`inst`**: Get the DateTimePicker instance so you can call its API
 *     methods directly.
 *
 * @example
 *     
 * new $.fn.dataTable.Editor( {
 *   "ajax": "php/dates.php",
 *   "table": "#example",
 *   "fields": [ {
 *          "label": "First name:",
 *          "name": "first_name"
 *      }, {
 *          "label": "Last name:",
 *          "name": "last_name"
 *      }, {
 *          "label": "Updated date:",
 *          "name": "updated_date",
 *          "type": "datetime",
 *          "opts": {
 *              format: 'yyyy-mm-dd'
 *          }
 *      }, {
 *          "label": "Registered date:",
 *          "name": "registered_date",
 *          "type": "datetime",
 *          "opts": {
 *              format: 'd M yy'
 *          }
 *      }
 *   ]
 * } );
 */

(function( factory ){
	if ( typeof define === 'function' && define.amd ) {
		// AMD
		define( ['jquery', 'datatables', 'datatables-editor'], factory );
	}
	else if ( typeof exports === 'object' ) {
		// Node / CommonJS
		module.exports = function ($, dt) {
			if ( ! $ ) { $ = require('jquery'); }
			factory( $, dt || $.fn.dataTable || require('datatables') );
		};
	}
	else if ( jQuery ) {
		// Browser standard
		factory( jQuery, jQuery.fn.dataTable );
	}
}(function( $, DataTable ) {
'use strict';


if ( ! DataTable.ext.editorFields ) {
	DataTable.ext.editorFields = {};
}

var _fieldTypes = DataTable.Editor ?
    DataTable.Editor.fieldTypes :
    DataTable.ext.editorFields;


_fieldTypes.datetime = {
	create: function ( conf ) {
		var that = this;

		conf._input = $('<input/>')
			.attr( $.extend( {
				id: conf.id,
				type: 'text',
				'class': 'datetimepicker'
			}, conf.attr || {} ) )
			.datetimepicker( $.extend( {}, conf.opts ) );

		return conf._input[0];
	},

	get: function ( conf ) {
		return conf._input.val();
	},

	set: function ( conf, val ) {
		conf._input.val( val );
	},

	// Non-standard Editor methods - custom to this plug-in. Return the jquery
	// object for the datetimepicker instance so methods can be called directly
	inst: function ( conf ) {
		return conf._input;
	}
};


}));
