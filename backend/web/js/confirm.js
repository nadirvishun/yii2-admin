/**
 * 已经没用了，krajeeDialog自带了这个功能
 * @param message
 * @param ok
 * @param cancel
 * @returns {boolean}
 */
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