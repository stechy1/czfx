var articlaManager = new ArticleManager();

function toggleFavorite() {
    jQuery("#add-to-favorite").toggleClass("hidden");
    jQuery("#delete-from-favorite").toggleClass("hidden");
}

jQuery(document).ready(function () {
    jQuery("#add-to-favorite").click(function (e) {
        var url = jQuery(e.target).data("articleid");

        articlaManager.addToFavorite(url, function() {
            showUserMessage({content: "Článek byl přidán do oblíbených", type: "success"});
            toggleFavorite();
        });

        e.preventDefault();
        e.stopPropagation();
    });

    jQuery("#delete-from-favorite").click(function (e) {
        var url = jQuery(e.target).data("articleid");

        articlaManager.deleteFromFavorite(url, function() {
            showUserMessage({content: "Článek byl odebrán z oblíbených", type: "info"});
            toggleFavorite();
        });

        e.preventDefault();
        e.stopPropagation();
    });
});