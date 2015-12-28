var forumManager = new ForumManager();
jQuery(document).ready(function () {
    jQuery(".delete-topic").click(function (e) {
        e.preventDefault();
        e.stopPropagation();

        var topicID = $(this).data("topicid");

        forumManager.deleteTopic(topicID, function () {
            $("#topic-" + topicID).remove();
        });
    });
});