var downloading = false;
var articleManager = new ArticleManager();

jQuery(document).ready(function () {

    jQuery(".load-next").click(function () {
        var self = $(this);
        var id = self.data("page");
        articleManager.getNext(id, function (data) {
            var articles = jQuery(".articles");
            for (var i = 0; i < data.length; i++) {
                articles.isotope("insert", $(data[i]));
            }
            self.data("page", ++id);
        });
    });
});