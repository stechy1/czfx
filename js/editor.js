HTMLElement.prototype.wrap = function(elms) {
    // Convert `elms` to an array, if necessary.
    if (!elms.length) elms = [elms];

    // Loops backwards to prevent having to clone the wrapper on the
    // first element (see `child` below).
    for (var i = elms.length - 1; i >= 0; i--) {
        var child = (i > 0) ? this.cloneNode(true) : this;
        var el    = elms[i];

        // Cache the current parent and sibling.
        var parent  = el.parentNode;
        var sibling = el.nextSibling;

        // Wrap the element (is automatically removed from its current
        // parent).
        child.appendChild(el);

        // If the element had a sibling, insert the wrapper before
        // the sibling to maintain the HTML structure; otherwise, just
        // append it to the parent.
        if (sibling) {
            parent.insertBefore(child, sibling);
        } else {
            parent.appendChild(child);
        }
    }
};
HTMLElement.prototype.appendFirst = function(childNode){
    if(this.firstChild)this.insertBefore(childNode,this.firstChild);
    else this.appendChild(childNode);
};

var Editor = function(textarea, settings) {
    this.textArea = document.getElementById(textarea);
    this.settings = settings;
    this.previewContainer = null;
};

Editor.prototype.setCaretPosition = function(pos) {
    var ctrl = this.textArea;

    if(ctrl.setSelectionRange)
    {
        ctrl.focus();
        ctrl.setSelectionRange(pos, pos);
    }
    else if (ctrl.createTextRange) {
        var range = ctrl.createTextRange();
        range.collapse(true);
        range.moveEnd('character', pos);
        range.moveStart('character', pos);
        range.select();
    }
};

Editor.prototype.getCaretPosition = function() {
    if (this.textArea.selectionStart) {
        return this.textArea.selectionStart;
    } else if (!document.selection) {
        return 0;
    }

    var c = "\001",
        sel = document.selection.createRange(),
        dul = sel.duplicate(),
        len = 0;

    dul.moveToElementText(this.textArea);
    sel.text = c;
    len = dul.text.indexOf(c);
    sel.moveStart('character',-1);
    sel.text = "";
    return len;
};

Editor.prototype.init = function() {

    var textArea = this.textArea;
    var self = this;

    var getContainer = function() {
        var div = document.createElement('div');
        div.setAttribute('class', 'panel editorContainer');
        return div;
    };
    var getToolBar = function() {
        var toolbar = document.createElement('div');
        toolbar.setAttribute('class', 'panel-heading editorToolbar');

        return toolbar;
    };
    var getBodyContainer = function() {
        var div = document.createElement('div');
        div.setAttribute('class', 'panel-body');

        return div;
    };
    var getPreviewContainer = function() {
        var div = document.createElement('div');
        div.setAttribute('class', 'editorPreview hidden');

        return div;
    };
    var createButton = function(options) {
        var btn = document.createElement('a');
        //btn.innerHTML = options.text;
        btn.setAttribute('type', 'button');
        btn.setAttribute('class', 'btn btn-default btn-xs editorToolBarElement ' + options.class);
        btn.setAttribute('data-toggle', 'tooltip');
        btn.setAttribute('data-placement', 'top');
        btn.setAttribute('title', options.tooltip || '');
        btn.onclick = function(e) {
            var area = textArea;
            var text = area.value;
            var caretPosition = self.getCaretPosition();
            var textBefore = text.substring(0, caretPosition);
            var textAfter = text.substring(caretPosition, text.length);

            options.after = options.after || '';

            textBefore += options.before;
            textAfter = options.after + textAfter;

            area.value = textBefore + textAfter;
            self.setCaretPosition(caretPosition + options.before.length + ((typeof options.space !== 'undefined') ? options.space : 0));

            e.preventDefault();
            area.focus();
            return false;
        };

        return btn;
    };
    var getViewButton = function() {
        var btn = document.createElement('a');
        //btn.innerHTML = 'Náhled';
        btn.setAttribute('data-toggle', 'tooltip');
        btn.setAttribute('data-placement', 'top');
        btn.setAttribute('title', 'Zobrazit náhled');
        btn.setAttribute('class', 'btn btn-success btn-sm editorToolBarElement preview');
        btn.onclick = function(e) {
            $.ajax({
                type: 'post',
                data: {
                    controller: 'convert',
                    action: '',
                    data: self.textArea.value
                },
                success: function(result) {
                    result = JSON.parse(result);
                    var text = JSON.parse(result.data.text);
                    if (result.success) {
                        console.log(result);
                        self.previewContainer.innerHTML = text;
                        self.previewContainer.className = 'editorPreview';
                    }
                }
            });

            return false;
        };

        return btn;
    };

    var container = getContainer();
    var toolBar = getToolBar();
    var bodyContainer = getBodyContainer();
    var previewContainer = getPreviewContainer();
    this.previewContainer = previewContainer;

    textArea.setAttribute('class', 'markupEditor');
    textArea.setAttribute('style', 'width: 100%; padding: 5px');
    if (this.settings.buttons !== null) {
        for (var i = 0; i < this.settings.buttons.length; i++) {
            var btn = this.settings.buttons[i];
            toolBar.appendChild(createButton(btn));
        }
    }
    toolBar.appendChild(getViewButton());

    bodyContainer.wrap(this.textArea);
    bodyContainer.appendChild(previewContainer);
    container.wrap(bodyContainer);
    container.appendFirst(toolBar);
};

Editor.prototype.getText = function() {
    return this.textArea.value;
};