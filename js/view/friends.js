var relationships = new RelationshipsManager();
jQuery(document).ready(function () {
    jQuery(".accept-friend-request").click(function () {
        var friendID = jQuery(this).data("friendid");

        relationships.acceptFriendRequest(friendID);
    });

    jQuery(".cancel-friend-request").click(function () {
        var friendID = jQuery(this).data("friendid");

        relationships.cancelFriendRequest(friendID);
    });

    jQuery(".decline-friend-request").click(function () {
        var friendID = jQuery(this).data("friendid");

        relationships.declineFriendRequest(friendID);
    });

    jQuery(".remove-friend").click(function () {
        var friendID = jQuery(this).data("friendid");

        relationships.unfriend(friendID);
    });

    jQuery(".block-friend").click(function () {
        var friendID = jQuery(this).data("friendid");

        relationships.blockFriend(friendID);
    });

    jQuery(".unblock-friend").click(function () {
        var friendID = jQuery(this).data("friendid");

        relationships.unblock(friendID);
    });
});