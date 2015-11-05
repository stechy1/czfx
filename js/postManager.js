/**
 * Created by Petr on 10. 6. 2015.
 */

var isDownloading = false;

var PostManager = function () {};

PostManager.prototype.getNext = function() {
    if(isDownloading)
        return false;

    isDownloading = true;

    jQuery.ajax({
        url: 'index/get/posts',
        //type: "post",
        /*data: {
            controller: "Index",
            action: "getNext"
        },*/
        success: function (result) {
            result = JSON.parse(result);
            if(result.success) {
                console.log(result);
                var data = result.data;
                var postsGroup = jQuery("#posts-group");
                for (var i = 0; i < data.length; i++) {
                    postsGroup.append(data[i]);
                }
                isDownloading = false;
            }
        }
    });
};