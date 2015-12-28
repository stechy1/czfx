var m = new ArticleManager();

jQuery(document).ready(function () {
    jQuery('.cat-remove').click(function () {
        if (confirm("Opravdu si přejete smazat článek?")) {
            var id = $(this).data("articleid");

            m.delete(id, function() {
                jQuery('#article_' + id).remove();
            });
        }
    });

    jQuery('.boot-switch').on('switchChange.bootstrapSwitch', function(event, state) {

        var data = {
            id: $(this).data("articleid"),
            validated: state
        };

        m.validate(data);
    });

});