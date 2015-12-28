var forumManager = new ForumManager();
jQuery(document).ready(function () {
    jQuery(".delete-category").click(function (e) {
        e.preventDefault();
        e.stopPropagation();

        var categoryID = $(this).data("categoryid");

        forumManager.deleteCategory(categoryID, function () {
            $("#category-" + categoryID).remove();
        });
    });
});