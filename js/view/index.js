var socket = io('127.0.0.1:8080/index');

var downloading = false;
var forumManager = new ForumManager();

jQuery(document).ready(function () {

    jQuery(".feeds-container").scroll(function(e) {
        var target = e.target;
        var cHeight = target.clientHeight;
        var scrollHeight = target.scrollHeight;
        var scrollPos = target.scrollTop;

        if(scrollPos >= scrollHeight - cHeight - 100) {
            forumManager.getNext(function (data) {
                var postsGroup = jQuery(".feeds");
                for (var i = 0; i < data.length; i++) {
                    postsGroup.isotope("insert", $(data[i]));
                }
            });
        }
    });

    socket.on('render-index-post', function (data) {
        var container = jQuery(".feeds");
        data = jQuery(data);
        container.prepend(data)
            .isotope('prepended', data);
    })
});