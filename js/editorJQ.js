var EditorJQ = function (mainContainer, settings) {
    this.mainContainer = $(mainContainer);
    this.settings = settings || {};
    this.settings.attachment = settings.attachment || false;
};

EditorJQ.prototype.init = function () {

    var self = this;

    var createViewButton = function () {
        return self.createButton({
            class: 'preview',
            buttonType: 'btn-success',
            tooltip: 'Náhled',
            click: function () {
                this.toggled = !this.toggled || false;

                if (!this.toggled) {
                    self.showArea();
                    return;
                }

                self.showPreview();

                $.ajax({
                    type: 'post',
                    url: 'editor/convert',
                    data: {
                        text: self.textarea.val()
                    },
                    success: function (result) {
                        result = JSON.parse(result);
                        if (result.success) {
                            var text = JSON.parse(result.data.text);
                            self.previewContainer.html(text);
                        }
                    }
                });
            }
        });
    };
    var createAddAttachmentButton = function () {
        var elm = $('<a/>', {class: 'editorAttachmentElemetn'/*, style: 'width: 128px; height: 128px;'*/});
        var addCnt = $('<span/>', {class: 'btn btn-default editorFileinput'});
        var addBtn = $('<span/>', {class: 'add'});
        var inputFile = $('<input/>', {type: 'file', name: 'files[]'});

        inputFile.change(function () {
            var files = this.files;
            var length = files.length;

            if (length == 0)
                return;

            self.uploadAttachment(files[0]);
        });

        addCnt.append(addBtn).append(inputFile);
        elm.append(addCnt);
        self.attachmentContainer.append(elm);

        return elm;
    };
    var createAttachmentButton = function () {
        self.attachmentContainer = $('<div/>', {class: 'editorAttachment', style: 'display: none'});
        self.attachmentContainer.append(createAddAttachmentButton());
        self.editorBody.append(self.attachmentContainer);

        return self.createButton({
            class: 'attachment',
            tooltip: 'Přidat soubor',
            click: function () {
                this.toggled = !this.toggled || false;

                if (!this.toggled) {
                    self.showArea();
                    return;
                }

                self.showAttachment();
                self.getMyFiles();
            }
        });
    };
    var createContextMenu = function () {
        var options = [
            {text: "Vybrat"},
            {text: "Odebrat"}
        ];
        var createContextMenuItem = function (options) {
            var li = $('<li/>');
            var a = $('<a/>', {href: "#", text: options.text || "Popisek", tabindex: -1});

            li.append(a);

            return li;
        };

        var div = $('<div/>', {class: "context-menu"});
        var menu = $('<ul/>', {class: "dropdown-menu", role: "menu"});

        for(var i = 0; i < options.length; i++) {
            menu.append(createContextMenuItem(options[i]));
        }

        div.append(menu);

        return div;
    };


    this.textarea = $('<textarea/>', {name: self.settings.areaName || 'content', class: 'editorTextarea', cols: 30, rows: 10, text: self.settings.text || ''});
    this.editorContainer = $('<div/>', {class: 'panel editorContainer'});
    this.editorHeader = $('<div/>', {class: 'panel-heading'});
    this.editorBody = $('<div/>', {class: 'panel-body'});
    this.editorToolbar = $('<div/>', {class: 'editorToolBar'});
    this.previewContainer = $('<div/>', {class: 'editorPreview', style: 'display: none'});
    this.contextMenu = createContextMenu();

    if (this.settings.buttons !== null) {
        for (var i = 0; i < this.settings.buttons.length; i++) {
            var btn = this.settings.buttons[i];
            this.editorToolbar.append(self.createButton(btn));
        }
    }

    if (this.settings.attachment)
        this.editorToolbar.append(createAttachmentButton());
    this.editorToolbar.append(createViewButton());

    this.editorHeader.append(this.editorToolbar);
    this.editorBody.append(this.textarea);
    this.editorBody.append(this.previewContainer);

    this.editorContainer.append(this.editorHeader);
    this.editorContainer.append(this.editorBody);

    this.mainContainer.append(this.editorContainer);
    this.mainContainer.append(this.contextMenu);

};
EditorJQ.prototype.appendContent = function (options) {
    options.before = options.before || '';
    options.after = options.after || '';
    options.space = options.space || 0;
    options.selection = options.selection || 0;

    var self = this;

    var area = self.textarea;
    var text = area.val();
    var selectedText = area.getSelection();
    area.val(
        text.substring(
            0, selectedText.start) +
        options.before +
        selectedText.text +
        (options.after || '') +
        text.substring(selectedText.end, text.length));
    var newCursorPos = selectedText.end + (options.before.length) + (options.space);
    area.selectRange(newCursorPos, newCursorPos + options.selection);
    area.focus();
};
EditorJQ.prototype.setContent = function (content) {
    this.textarea.val(content);
};

EditorJQ.prototype.createButton = function (options) {
    options.buttonType = options.buttonType || 'btn-default';

    var self = this;

    var btn = $('<a/>', {
        type: 'button',
        class: 'btn ' + options.buttonType + ' btn-xs editorToolBarElement ' + options.class,
        'data-toggle': 'tooltip',
        'data-placement': 'top',
        title: (options.tooltip || 'tooltip')
    });
    btn.click(options.click || function () {
            self.appendContent(options);
        });

    return btn;
};
EditorJQ.prototype.createAttachment = function (options) {
    var self = this;
    var url = options.path + options.name;
    var elm = $('<a/>', {class: 'editorAttachmentElemetn', 'data-toggle': 'tooltip context', 'data-placement': 'top', 'data-target': '.context-menu', title: options.name});
    var img = $('<img/>', {src: url});

    elm.click(options.click || function() {
        self.appendContent({
            before: '![text]',
            after: '(' + url + ')',
            space: -5,
            selection: 4
        });
        self.showArea();
    });
    jQuery(elm).contextmenu({
        onItem: function (e) {
            e.preventDefault();
        }
    });

    elm.append(img);

    return elm;
};

EditorJQ.prototype.showAttachment = function () {
    if (this.attachmentContainer.css('display') == 'none')
        this.attachmentContainer.fadeToggle();

    if (this.textarea.css('display') != 'none')
        this.textarea.fadeToggle();

    if (this.previewContainer.css('display') != 'none')
        this.previewContainer.fadeToggle();
};
EditorJQ.prototype.showArea = function () {
    if (this.attachmentContainer.css('display') != 'none')
        this.attachmentContainer.fadeToggle();

    if (this.textarea.css('display') == 'none')
        this.textarea.fadeToggle();

    if (this.previewContainer.css('display') != 'none')
        this.previewContainer.fadeToggle();

    this.textarea.focus();
};
EditorJQ.prototype.showPreview = function () {
    if (this.attachmentContainer.css('display') != 'none')
        this.attachmentContainer.fadeToggle();

    if (this.textarea.css('display') != 'none')
        this.textarea.fadeToggle();

    if (this.previewContainer.css('display') == 'none')
        this.previewContainer.fadeToggle();
};

EditorJQ.prototype.uploadAttachment = function (file) {
    var self = this;
    var formData = new FormData();

    formData.append("attachment", file);
    //formData.append("controller", self.settings.controller);
    //formData.append("action", self.settings.action);

    $.ajax({
        type: "post",
        url: 'editor/upload/attachment',
        contentType: false,
        processData: false,
        cache: false,
        data: formData,
        success: function (result) {
            result = JSON.parse(result);
            if (result.success) {
                self.getMyFiles();
            }
        }
    });
};

EditorJQ.prototype.getMyFiles = function () {
    var self = this;

    var clearContainer = function () {
        var container = self.attachmentContainer;
        var childrens = container.children();
        if (childrens.length == 1)
            return;

        for (var i = 1; i < childrens.length; i++) {
            var children = childrens[i];
            $(children).remove();
        }
    };

    clearContainer();

    $.ajax({
        type: 'post',
        url: 'editor/get/attachments',
        /*data: {
            controller: 'file-manager',
            action: 'get'
        },*/
        success: function (result) {
            result = JSON.parse(result);
            if (result.success) {
                var files = JSON.parse(result.data.files);
                var path = JSON.parse(result.data.path);

                for (var i = 0; i < files.length; i++) {
                    self.attachmentContainer.append(self.createAttachment({name: files[i], path: path}));
                }

                jQuery('[data-toggle="tooltip"]').tooltip();
            }
        }
    });
};
