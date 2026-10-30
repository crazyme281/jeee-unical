window.error = function (msg) {
    $.notify({
        // options
        message: msg
    },{
        // settings
        type: 'danger'
    });
};
window.success = function (msg) {
    $.notify({
        // options
        message: msg
    },{
        // settings
        type: 'success'
    });
};

$('form.subscribe').submit(function (e) {
    e.preventDefault();
    var $this = $(this);
    var button = $this.find('button');
    button.prop('disabled', true);
    $.post('/api/subscribe', $this.serialize()).success(function (res) {
        success(res.msg);
        button.text('Subscribed!');
    }).fail(function (res) {
        var key = Object.keys(res.responseJSON.errors)[0];
        var msg = res.responseJSON.errors[key][0];
        error(msg);
        button.prop('disabled', false);
    });
}).find('input').change(function (e) { // reset button
    var button = $(this).parents('form').find('button');
    button.prop('disabled', false);
    button.text('Subscribe');
});