function canUpload(file) {
    var enabled = ["png", "jpg", "jpeg", "gif"];
    var extension = file["name"].substr((file["name"].lastIndexOf('.') + 1));
    return jQuery.inArray(extension, enabled) != -1;
}

function uploadFile(fd) {
    jQuery(".dropzone").addClass("uploading");
    //fd.append("controller", "upload");
    //fd.append("uploadType", "avatar");
    jQuery.ajax({
        url: "profile/upload/avatar",
        type: "POST",
        contentType: false,
        processData: false,
        cache: false,
        data: fd,
        success: function (data) {
            data = JSON.parse(data);
            if (data.success == 1) {

                jQuery(".profile-image img").attr("src", data.data['imgSrc']);
            } else {
                jQuery(".dropzone").removeClass("uploading").effect("shake");
                showUserMessages(data.messages);
            }
        },
        complete: function () {
            jQuery(".dropzone").removeClass("uploading");
            jQuery(this).removeClass("dragged");
        }
    });
}

function handleFiles(files) {
    if (canUpload(files[0])) {
        var fd = new FormData();
        fd.append('avatar', files[0]);
        uploadFile(fd);
    } else {
        jQuery(".dropzone").effect("shake");
        jQuery(this).removeClass("dragged");
    }
}

jQuery(function () {
    jQuery("body").append('<div class="dropoverride"></div>');

    jQuery(".dropzone").on({
        dragenter: function (e) {
            e.preventDefault();
            e.stopPropagation();
            jQuery(this).addClass("dragged");
        },
        dragover: function (e) {
            e.preventDefault();
            e.stopPropagation();
            jQuery("body").addClass("dragging");
        },
        dragleave: function (e) {
            e.preventDefault();
            e.stopPropagation();
            jQuery(this).removeClass("dragged");
        },
        drop: function (e) {
            e.preventDefault();
            e.stopPropagation();
            jQuery(this).removeClass("dragged");
            jQuery("body").removeClass("dragging");
            handleFiles(e.originalEvent.dataTransfer.files);
        }
    });

    jQuery(document).on({
        dragenter: function (e) {
            e.preventDefault();
            e.stopPropagation();
            jQuery("body").addClass("dragging");
        },
        dragover: function (e) {
            e.preventDefault();
            e.stopPropagation();
        },
        drop: function (e) {
            e.preventDefault();
        }
    });

    jQuery(".dropoverride").on({
        dragleave: function (e) {
            jQuery("body").removeClass("dragging");
        }
    });

    jQuery("#avatar-upload").change(function (e) {
        handleFiles(this.files);
    })
});
