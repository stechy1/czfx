/**
 * Created by Petr on 10. 6. 2015.
 */

var isDownloading = false;

var ForumManager = function () {};

ForumManager.prototype.getNext = function() {
    if(isDownloading)
        return false;

    isDownloading = true;

    jQuery.ajax({
        url: 'index/get/posts',
        success: function (result) {
            result = JSON.parse(result);
            if(result.success) {
                var data = result.data;
                var postsGroup = jQuery("#posts-group");
                for (var i = 0; i < data.length; i++) {
                    postsGroup.append(data[i]);
                }
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

ForumManager.prototype.addCategory = function(id, callback) {

};