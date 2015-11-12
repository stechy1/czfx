var SupportManager = function (){};

SupportManager.prototype.markAsRead = function(id, callback) {
    jQuery.ajax({
        method: 'post',
        url: 'admin-report-manager/mark-as-read/' + id,
        success: function (result) {
            result = JSON.parse(result);
            if (result.success)
                callback();

            showUserMessages(result.messages);
        }
    });
};

SupportManager.prototype.delete = function(id, callback) {
    jQuery.ajax({
        method: 'post',
        url: 'admin-report-manager/delete/' + id,
        success: function (result) {
            result = JSON.parse(result);
            if (result.success)
                callback();

            showUserMessages(result.messages);
        }
    });
};