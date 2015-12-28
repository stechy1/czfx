var articleManager = new ArticleManager();

jQuery(document).ready(function () {
    jQuery(".delete-favorite").click(function (e) {

        var id = jQuery(e.target).data("articleid");
        var url= jQuery(e.target).data("articleurl");

        articleManager.deleteFromFavorite(url, function (data) {
            jQuery("#article_" + id).fadeOut().remove();
        });

        e.preventDefault();
        e.stopPropagation();

    });
});