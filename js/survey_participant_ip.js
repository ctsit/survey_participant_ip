$(function() {
    var url = 'https://api.ipify.org/?format=json';
    function storeIP(url) {
        var bool = false;
        $.get(url
              ,function(data) {
                  if (typeof data.ip === 'undefined' || !data.ip) {
                      return;
                  }

                  SurveyParticipantIP.fields.forEach(function(name) {
                      $field = $('[name="' + name + '"]');

                      if ($field.length > 0) {
                          $field.val(data.ip);
                      }
                  });
              },
              function(success) { bool = success; },
              'json');
        return bool;
    }

    if (!storeIP(url)) {
        storeIP(`https://cors-anywhere.herokuapp.com/${url}`);
    }
});
