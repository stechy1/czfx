function readURL(input) {

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $("#category-image").attr('src', e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
    }
}

$("#imgInp").change(function(){
    readURL(this);
});

var category = new CategoryManager();

jQuery(document).ready(function () {

    jQuery('#category-name').keyup(function () {
        var nameBox = jQuery('#category-name');
        var urlBox = jQuery('#category-url');
        urlBox.val(prettyURL(nameBox.val()));
    });

    jQuery("#category-icon-input").change(function () {
        var file = this.files[0];
        jQuery("#category-image-button").html((file.name));
        readURL(this);
    });
});