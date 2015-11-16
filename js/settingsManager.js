var SettingsManager = function () {
};

SettingsManager.prototype.update = function (k, v) {
    jQuery.ajax({
        "method": "post",
        "url": "admin-settings/update",
        "data": {
            "key": k,
            "value": v
        },
        success: function (result) {
            result = JSON.parse(result);
            showUserMessages(result.messages);
        }
    });
};