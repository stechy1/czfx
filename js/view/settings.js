jQuery(document).ready(function () {
    jQuery("#show-delete-form").click(function () {
        jQuery("#form-user-delete").fadeToggle("slow", "linear");
    });

    jQuery("#delete-account").click(function (e) {
        var srsly = confirm("Opravdu si přejete smazat účet?");
        if (!srsly) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    });
});