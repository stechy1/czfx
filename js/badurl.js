jQuery(function () {
    $("a").each(function(index) {$(this).attr("href", "index.php?c=" + $(this).attr("href"));});
});