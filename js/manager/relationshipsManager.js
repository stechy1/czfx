var RelationshipManager = function () {};

/*
Relationships.prototype.addFriend = function (friendID, callback) {
    callback = callback || function () {};

    jQuery.ajax({
        type: "post",
        url: "profile/add-to-friend/" + friendID,
        success: function (result) {
            result = JSON.parse(result);
            if (result.success) {
                callback(result.data);
            }

            showUserMessages(result.messages);
        }
    });
};

Relationships.prototype.removeFriend = function (friendID, callback) {
    callback = callback || function () {};

    jQuery.ajax({
        type: "post",
        url: "profile/remove-from-friend/" + friendID,
        success: function (result) {
            result = JSON.parse(result);
            if (result.success) {
                callback(result.data);
            }

            showUserMessages(result.messages);
        }
    });
};
*/

RelationshipManager.prototype.request = function (url, callback) {
    callback = callback || function () {};

    jQuery.ajax({
        type: "post",
        url: "friends/" + url,
        success: function (result) {
            result = JSON.parse(result);
            if (result.success) {
                callback(result.data);
            }

            showUserMessages(result.messages);
        }
    });
};

RelationshipManager.prototype.addFriend = function (friendID, callback) {
    var url = "add/" + friendID;
    this.request(url, function (data) {
        callback(data);
    });
};

RelationshipManager.prototype.acceptFriendRequest = function (friendID, callback) {
    var url = "accept/" + friendID;
    this.request(url, function (data) {
        callback(data);
    });

};

RelationshipManager.prototype.declineFriendRequest = function (friendID, callback) {
    var url = "decline/" + friendID;
    this.request(url, function (data) {
        callback(data);
    });

};

RelationshipManager.prototype.cancelFriendRequest = function (friendID, callback) {
    var url = "cancel/" + friendID;
    this.request(url, function (data) {
        callback(data);
    });

};

RelationshipManager.prototype.unfriend = function (friendID, callback) {
    var url = "unfriend/" + friendID;
    this.request(url, function (data) {
        callback(data);
    });

};

RelationshipManager.prototype.blockFriend = function (friendID, callback) {
    var url = "block/" + friendID;
    this.request(url, function (data) {
        callback(data);
    });

};

RelationshipManager.prototype.unblock = function (friendID, callback) {
    var url = "unblock/" + friendID;
    this.request(url, function (data) {
        callback(data);
    });

};