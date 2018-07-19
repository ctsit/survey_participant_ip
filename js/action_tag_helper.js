$(function() {
    $('body').on('dialogopen', function(event, ui) {
        var $popup = $(event.target);
        if ($popup.prop('id') !== 'action_tag_explain_popup') {
            // That's not the popup we are looking for...
            return false;
        }

        // Aux function that checks if text matches the @READONLY-SURVEY string.
        var isReadOnlySurveyLabelColumn = function() {
            return $(this).text() === '@READONLY-SURVEY';
        }

        // Getting @READONLY-SURVEY row from action tags help table.
        var $default_action_tag = $popup.find('td').filter(isReadOnlySurveyLabelColumn).parent();
        if ($default_action_tag.length !== 1) {
            return false;
        }

        var tag_name = '@SURVEY-PARTICIPANT-IP';
        var descr = 'Hides the field on the survey page and automatically collects the IP address of the survey participant.';

        // Creating a new action tag row.
        var $new_action_tag = $default_action_tag.clone();
        var $cols = $new_action_tag.children('td');
        var $button = $cols.find('button');

        if ($button.length !== 0) {
            // Column 1: updating button behavior.
            $button.attr('onclick', $button.attr('onclick').replace('@READONLY-SURVEY', tag_name));
        }

        // Columns 2: updating action tag label.
        $cols.filter(isReadOnlySurveyLabelColumn).text(tag_name);

        // Column 3: updating action tag description.
        $cols.last().html(descr);

        // Placing new action tag.
        $new_action_tag.insertAfter($default_action_tag);
    });
});
