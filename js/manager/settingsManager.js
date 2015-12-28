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


SettingsManager.prototype.setAsDefault = function (data, callback) {
    jQuery.ajax({
        method: "post",
        url: "admin-settings/setAsDefault",
        data: data,
        contentType: false,
        processData: false,
        success: function (result) {
            result = JSON.parse(result);
            if (result.success)
                if (callback) callback();

            showUserMessages(result.messages);
        }
    });
};