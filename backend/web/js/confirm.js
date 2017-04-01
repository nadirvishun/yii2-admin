yii.confirm = function (message, ok, cancel) {
    krajeeDialog.confirm(message, function (data) {
        if (data) {
            !ok || ok();
        } else {
            !cancel || cancel();
        }
    });
    // confirm will always return false on the first call
    // to cancel click handler
    return false;
}