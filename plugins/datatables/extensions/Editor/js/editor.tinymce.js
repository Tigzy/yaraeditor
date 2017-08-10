/**
 * Use the [TinyMCE](http://www.tinymce.com) WYSIWYG input control in Editor.
 *
 * @name TinyMCE
 * @summary WYSIWYG editor
 * @requires [TinyMCE](http://www.tinymce.com)
 * @depjs //cdn.tinymce.com/4/tinymce.min.js
 * 
 * @opt `e-type object` **`opts`**: TinyMCE options object which is used in the construction
 *   of the WYSIWYG text area. The options available are defined in the [TinyMCE
 *   documentation](http://www.tinymce.com/wiki.php/Configuration).
 *
 * @method **`tinymce`**: Get the TinyMCE instance. This is useful if you want to be
 *   able to interact with the TinyMCE instance directly, through the control's
 *   own API.
 *
 * @example
 *     
 * new $.fn.dataTable.Editor( {
 *   "ajax": "php/dates.php",
 *   "table": "#example",
 *   "fields": [ {
 *       "label": "Notes:",
 *       "name": "notes",
 *       "type": "tinymce",
 *       "opts": {
 *         skin : 'lightgray',
 *         // additional options if required...
 *       }
 *     }, 
 *     // additional fields...
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


_fieldTypes.tinymce = {
	create: function ( conf ) {
		var that = this;
		conf._safeId = DataTable.Editor.safeId( conf.id );
 
		conf._input = $('<div><textarea id="'+conf._safeId+'"></textarea></div>');
 
		// Because tinyMCE uses an editable iframe, we need to destroy and
		// recreate it on every display of the input
		this
			.on( 'open.tinymceInit-'+conf._safeId, function () {
				tinymce.init( $.extend( true, {
					selector: '#'+conf._safeId
				}, conf.opts, {
					init_instance_callback: function ( editor ) {
						if ( conf._initSetVal ) {
							editor.setContent( conf._initSetVal );
							conf._initSetVal = null;
						}
					}
				} ) );
	 
				var editor = tinymce.get( conf._safeId );

				if ( editor && conf._initSetVal ) {
					editor.setContent( conf._initSetVal );
					conf._initSetVal = null;
				}
			} )
			.on( 'close.tinymceInit-'+conf._safeId, function () {
				var editor = tinymce.get( conf._safeId );


				if ( editor ) {
					editor.destroy();
				}

				conf._initSetVal = null;
				conf._input.find('textarea').val('');
			} );
 
		return conf._input;
	},
 
	get: function ( conf ) {
		var editor = tinymce.get( conf._safeId );
		if ( ! editor ) {
			return conf._initSetVal;
		}
 
		return editor.getContent();
	},
 
	set: function ( conf, val ) {
		var editor = tinymce.get( conf._safeId );
 
		// If not ready, then store the value to use when the `open` event fires
		conf._initSetVal = val;
		if ( ! editor ) {
			return;
		}
		editor.setContent( val );
	},
 
	enable: function ( conf ) {}, // not supported in TinyMCE
 
	disable: function ( conf ) {}, // not supported in TinyMCE
 
	// Get the TinyMCE instance - note that this is only available after the
	// first onOpen event occurs
	tinymce: function ( conf ) {
		return tinymce.get( conf._safeId );
	}
};


}));
