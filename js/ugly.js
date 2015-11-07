var uglyURL = true;
jQuery(function () {
    $("a").each(function(index) {$(this).attr("href", "index.php?c=" + $(this).attr("href"));});

    jQuery.ajaxPrefilter(function (options) {
        if (uglyURL)
            options.url = "index.php?c=" + options.url;
    });
});