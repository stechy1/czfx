var ArticleManager = function (){};

ArticleManager.prototype.delete = function(artID, callback) {
    jQuery.ajax({
        type: "post",
        url: 'admin-article-manager/delete/' + artID,
        success: function (result) {
            result = JSON.parse(result);
            if(result.success) {
                callback();
            }
            showUserMessages(result.messages);
        }
    });
};

ArticleManager.prototype.validate = function(validationData) {
    jQuery.ajax({
        type: "post",
        url: 'admin-article-manager/validate',
        data: {
            id: validationData.id,
            validated: validationData.validated
        },
        success: function (result) {
            result = JSON.parse(result);
            showUserMessages(result.messages);
        }
    });
};

ArticleManager.prototype.getNext = function(from, callback) {
    jQuery.ajax({
        type: "post",
        url: "archive/getNext/" + from,
        success: function (result) {
            result = JSON.parse(result);
            if(result.success) {
                callback(result.data);
            }
            showUserMessages(result.messages)
        }
    });
};