var CategoryManager = function () {};

CategoryManager.prototype.delete = function (catID, callback) {
    jQuery.ajax({
        type: "post",
        url: 'admin-category-manager/delete/' + catID,
        success: function (result) {
            result = JSON.parse(result);
            if (result.success) {
                callback();
            }
            showUserMessages(result.messages);
        }
    });
};

CategoryManager.prototype.update = function (options) {
    jQuery.ajax({
        type: "post",
        url: 'admin-category-manager/update',
        data: {
            data: JSON.stringify(options)
        },
        success: function (result) {
            result = JSON.parse(result);
            showUserMessages(result.messages);
        }
    });
};