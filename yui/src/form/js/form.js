/**
 * JavaScript for form editing date conditions.
 *
 * @module moodle-availability_timesinceenrol-form
 */
M.availability_timesinceenrol = M.availability_timesinceenrol || {};

/**
 * @class M.availability_timesinceenrol.form
 * @extends M.core_availability.plugin
 */
M.availability_timesinceenrol.form = Y.Object(M.core_availability.plugin);

/**
 * Initialises this plugin.
 *
 * Because the date fields are complex depending on Moodle calendar settings,
 * we create the HTML for these fields in PHP and pass it to this method.
 *
 * @method initInner
 * @param {String} html HTML to use for date fields
 * @param {Number} defaultTime Time value that corresponds to initial fields
 *M.availability_timesinceenrol.form.initInner = function(name) {
 *    this.name = name;
 *};
 */

M.availability_timesinceenrol.form.getNode = function(json) {

    // Example controls contain only one tickbox.
    var html = '<label>';
    html += M.util.get_string('mintimesinceenrol', 'availability_timesinceenrol');
    html += ' <input name="mintimesinceenrol" type="number"/></label>';
    var node = Y.Node.create('<span>' + html + '</span>');

    // Set initial values based on the value from the JSON data in Moodle
    // database. This will have values undefined if creating a new one.
    if (json.mintimesinceenrol) {
        node.one('input[name=mintimesinceenrol]').set('value', json.mintimesinceenrol / (3600 * 24));
    }

    // Add event handlers (first time only). You can do this any way you
    // like, but this pattern is used by the existing code.
    if (!M.availability_timesinceenrol.form.addedEvents) {
        M.availability_timesinceenrol.form.addedEvents = true;
        var root = Y.one('#fitem_id_availabilityconditionsjson');
        root.delegate('change', function() {
                // The key point is this update call. This call will update
                // the JSON data in the hidden field in the form, so that it
                // includes the new value of the checkbox.
                M.core_availability.form.update();
                }, '.availability_timesinceenrol input');
    }

    return node;
};

M.availability_timesinceenrol.form.fillValue = function(value, node) {
    var mintime = node.one('input[name=mintimesinceenrol]');
    value.mintimesinceenrol = mintime.get('value') * 3600 * 24;
};

M.availability_completion.form.fillErrors = function(errors, node) {
    var value = {};
    this.fillValue(value, node);

    if (value.mintimesinceenrol === '') {
        // ...but this is how you would add one if required. This is
        // passing your component name (availability_timesinceenrol) and the
        // name of a string within your lang file (error_message)
        // which will be shown if they submit the form.
        errors.push('availability_timesinceenrol:error_mintimesinceenrol');
    }
};
