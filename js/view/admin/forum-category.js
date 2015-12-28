jQuery(document).ready(function () {

    jQuery('#name').keyup(function () {
        var nameBox = jQuery('#name');
        var urlBox = jQuery('#url');
        urlBox.val(prettyURL(nameBox.val()));
    });
});