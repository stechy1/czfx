var CZFXuploader = function(settings) {
    this.settings = settings || {};

    this.settings.url = this.settings.url || "error";
    this.settings.fileName = this.settings.fileName || "file";
    this.settings.callback = this.settings.callback || function () {};
};

CZFXuploader.prototype.canUpload = function (file) {
    var enabled = ["png", "jpg", "jpeg", "gif"];
    var extension = file["name"].substr((file["name"].lastIndexOf('.') + 1));
    return jQuery.inArray(extension, enabled) != -1;
};

CZFXuploader.prototype.uploadFile = function (fd) {
    var self = this;
    jQuery(".dropzone").addClass("uploading");
    jQuery.ajax({
        url: self.settings.url,
        type: "POST",
        contentType: false,
        processData: false,
        cache: false,
        data: fd,
        success: function (data) {
            data = JSON.parse(data);
            if (data.success) {
                self.settings.callback(data.data);
                //jQuery(".profile-image img").attr("src", data.data['imgSrc']);
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
};

CZFXuploader.prototype.handleFiles = function (files) {
    if (this.canUpload(files[0])) {
        var fd = new FormData();
        fd.append(this.settings.fileName, files[0]);
        this.uploadFile(fd);
    } else {
        jQuery(".dropzone").effect("shake");
        jQuery(this).removeClass("dragged");
    }
};

CZFXuploader.prototype.init = function () {
    var self = this;
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
            self.handleFiles(e.originalEvent.dataTransfer.files);
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
        dragleave: function () {
            jQuery("body").removeClass("dragging");
        }
    });
};