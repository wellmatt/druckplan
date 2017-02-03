/**
 * Date and time picker in Editor, Bootstrap style. This plug-in provides
 * integration between [Bootstrap DateTimePicker](http://eonasdan.github.io/bootstrap-datetimepicker/)
 * control and Editor. Fields can use this control by
 * specifying `datetime` as the Editor field type.
 *
 * @name Bootstrap DateTimePicker (2)
 * @summary Date and time input selector styled with Bootstrap
 * @requires [Bootstrap DateTimePicker](http://eonasdan.github.io/bootstrap-datetimepicker/)
 * @depjs //cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js
 * @depjs //cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/js/bootstrap-datetimepicker.min.js
 * @depcss //cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/css/bootstrap-datetimepicker.css
 *
 * @opt `e-type object` **`opts`**: DateTimePicker initialisation options
 *     object. Please refer to the Bootstrap DateTimePicker documentation for
 *     the full range of options available.
 * @opt `e-type object` **`attr`**: Attributes that are applied to the `-tag
 *     div` wrapper element used for the date picker. This can be used to set
 *     `data` attributes which the DateTimePicker allows for setting additional
 *     options such as the date format.
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
 *              format: 'DD.MM.YYYY'
 *          }
 *      }, {
 *          "label": "Registered date:",
 *          "name": "registered_date",
 *          "type": "datetime"
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

		conf._input = $(
				'<div class="input-group date" id="'+conf.id+'">'+
					'<input type="text" class="form-control" />'+
					'<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>'+
					'</span>'+
				'</div>'
			)
			.attr( $.extend( {}, conf.attr ) )
			.datetimepicker( $.extend( {}, conf.opts ) );
 
		return conf._input[0];
	},

	get: function ( conf ) {
		return conf._input.children('input').val();
	},

	set: function ( conf, val ) {
		var picker = conf._input.data("DateTimePicker");

		if ( picker.setDate ) {
			picker.setDate( val );
		}
		else {
			picker.date( val );
		}
	},

	disable: function ( conf ) {
		conf._input.data("DateTimePicker").disable();
	},

	enable: function ( conf ) {
		conf._input.data("DateTimePicker").enable();
	},

	// Non-standard Editor methods - custom to this plug-in. Return the jquery
	// object for the datetimepicker instance so methods can be called directly
	inst: function ( conf ) {
		return conf._input.data("DateTimePicker");
	}
};


}));
