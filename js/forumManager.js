var isDownloading = false;

var ForumManager = function () {};

ForumManager.prototype.getNext = function(callback) {
    if(isDownloading)
        return false;

    isDownloading = true;

    jQuery.ajax({
        url: 'index/get/posts',
        success: function (result) {
            result = JSON.parse(result);
            if(result.success) {
                var data = result.data;
                callback(data);
                isDownloading = false;
            }
        }
    });
};

ForumManager.prototype.deletePost = function(id, callback) {
    jQuery.ajax({
        url: 'forum/delete-post/' + id,
        success: function (result) {
            result = JSON.parse(result);
            if (result.success) {
                callback();
            }

            showUserMessages(result.messages);
        }
    });
};

ForumManager.prototype.deleteTopic = function(id, callback) {
    jQuery.ajax({
        url: 'forum/delete-topic/' + id,
        success: function (result) {
            result = JSON.parse(result);
            if (result.success) {
                callback();
            }

            showUserMessages(result.messages);
        }
    });
};

ForumManager.prototype.deleteCategory = function(id, callback) {
    jQuery.ajax({
        url: 'forum/delete-category/' + id,
        success: function (result) {
            result = JSON.parse(result);
            if (result.success) {
                callback();
            }

            showUserMessages(result.messages);
        }
    });
};