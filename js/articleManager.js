var ArticleManager = function (){
    this.hasNext = true;
};

ArticleManager.prototype.delete = function(artID, callback) {
    callback = callback || function() {};

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
    if(!this.hasNext)
        return;

    callback = callback || function() {};

    var self = this;
    jQuery.ajax({
        type: "post",
        url: "archive/getNext/" + from,
        success: function (result) {
            result = JSON.parse(result);
            if(result.success) {
                callback(result.data);
            }
            showUserMessages(result.messages);
            self.hasNext = result.success;
        }
    });
};

ArticleManager.prototype.addToFavorite = function (articleURL, callback) {
    callback = callback || function() {};

    jQuery.ajax({
        type: "post",
        url: "article/add-to-favorite/" + articleURL,
        success: function (result) {
            result = JSON.parse(result);
            if(result.success) {
                callback(result.data);
            }
            showUserMessages(result.messages);
        }
    });
};

ArticleManager.prototype.deleteFromFavorite = function (articleURL, callback) {
    callback = callback || function() {};

    jQuery.ajax({
        type: "post",
        url: "article/delete-from-favorite/" + articleURL,
        success: function (result) {
            result = JSON.parse(result);
            if(result.success) {
                callback(result.data);
            }
            showUserMessages(result.messages);
        }
    });
};