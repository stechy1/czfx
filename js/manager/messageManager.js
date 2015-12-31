var MessageManager = function (roomHash) {
    this.roomHash = roomHash;
};

MessageManager.prototype.send = function (content, callback) {
    if (!content) {
        showUserMessage({content: "Musite zadat zprávu", type: "warning"});
        return;
    }

    callback = callback || function () {};
    var formData = new FormData();
    formData.append("message_content", content);

    jQuery.ajax({
        type: "post",
        processData: false,
        contentType: false,
        url: "messages/send/" + this.roomHash,
        data: formData,
        success: function (result) {
            result = JSON.parse(result);

            if (result.success)
                callback(result.data);

            showUserMessages(result.messages);
        }
    });
};