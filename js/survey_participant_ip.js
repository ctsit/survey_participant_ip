$(function() {
    $.get('https://api.ipify.org/?format=json', function(data) {
        if (typeof data.ip === 'undefined' || !data.ip) {
            return;
        }

        SurveyParticipantIP.fields.forEach(function(name) {
            $field = $('[name="' + name + '"]');

            if ($field.length > 0) {
                $field.val(data.ip);
            }
        });
    }, 'json');
});
