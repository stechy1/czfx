var m = new CategoryManager();

jQuery(document).ready(function() {

    jQuery('.cat-remove').click(function() {
        if (confirm("Opravdu chcete smazat kategorii?")) {
            var id = $(this).data("categoryid");
            m.delete(id, function () {
                jQuery('#category_' + id).remove();
            });
        }
    });
})