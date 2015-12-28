var relationships = new RelationshipsManager();
var uploader = new CZFXuploader({
    url: "profile/upload/avatar",
    fileName: "avatar",
    callback: function (data) {
        jQuery(".profile-image img").attr("src", data['imgSrc']);
    }
});

function toggleFriendButton (state) {

    var addBtn = jQuery("#add-to-friend");
    var remBtn = jQuery("#remove-from-friend");
    var canBtn = jQuery("#cancel-friend-request");

    switch (state) {
        // add
        case 0:
            addBtn.addClass("hidden");
            remBtn.addClass("hidden");
            canBtn.removeClass("hidden");

            break;
        // remove
        case 1:
            addBtn.addClass("hidden");
            remBtn.removeClass("hidden");
            canBtn.addClass("hidden");

            break;
        // cancel
        case 2:
            addBtn.removeClass("hidden");
            remBtn.addClass("hidden");
            canBtn.addClass("hidden");

            break;
    }

}

jQuery(document).ready(function () {
    uploader.init();

    jQuery("#avatar-upload").change(function () {
        uploader.handleFiles(this.files);
    });

    jQuery("#add-to-friend").click(function () {
        var friendID = jQuery(this).data("friendid");

        relationships.addFriend(friendID, function () {
            toggleFriendButton(0);
        });
    });

    jQuery("#remove-from-friend").click(function () {
        var friendID = jQuery(this).data("friendid");

        relationships.unfriend(friendID, function () {
            toggleFriendButton(1);
         });
    });

    jQuery("#cancel-friend-request").click(function () {
        var friendID = jQuery(this).data("friendid");

        relationships.cancelFriendRequest(friendID, function () {
            toggleFriendButton(2);
        });
    })
});