var CZFXUploader = function (options) {
    var opt = {
        controller: "",
        action: ""
    };

    this.input = $('input[type="file"]');
    this.options = options || opt;

};

CZFXUploader.prototype.init = function () {
    var self = this;
    var input = this.input;
    input.change(function () {
        var files = this.files;
        var length = files.length;

        if (length == 0)
            return;

        self.upload(files[0]);
    });
};
CZFXUploader.prototype.upload = function (fileset) {
    var self = this;
    var formData = new FormData();
    formData.append("file", fileset);
    formData.append("controller", self.options.controller);
    formData.append("action", self.options.action);

    $.ajax({
        type: "post",
        contentType: false,
        processData: false,
        cache: false,
        data: formData,
        success: function (result) {
            result = JSON.parse(result);
            console.log(result);
        }
    });
};