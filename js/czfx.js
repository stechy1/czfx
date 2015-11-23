

jQuery(function () {
    getUserMessages();

    hljs.initHighlightingOnLoad();
    jQuery(".minimalization").click(function(e) {
        var target = e.target.parentElement.children[2];
        jQuery(target).fadeToggle();
    });

    jQuery(".fileinput-button").click(function() {
        $(this).next().trigger('click');
    });

    jQuery('[data-toggle="tooltip"]').tooltip();

    jQuery(".boot-switch").bootstrapSwitch({
        size: 'mini',
        onText: 'ANO',
        offText: 'NE',
        onColor: 'success',
        offColor: 'danger'
    });
    jQuery(".isotope-grid").isotope();

});

$(document).on('change', '.btn-file :file', function() {
    var input = $(this),
        numFiles = input.get(0).files ? input.get(0).files.length : 1,
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numFiles, label]);
});

/**
 * Získá ze serveru zprávy pro uživatele.
 */
function getUserMessages() {
    jQuery.ajax({
        type: 'post',
        url: 'user-message/get',
        success: function (result) {
            if (result) {
                result = JSON.parse(result);
                if (result.success) {
                    showUserMessages(result.messages);
                }
            }
        }
    });
}

/**
 * Zobrazí jednu zprávu uživateli.
 * @param m Zpráva
 */
function showUserMessage(m) {
    jQuery.notify({
        message: m.content
    },{
        animate: {
            enter: 'animated bounceInDown',
            exit: 'animated fadeOutRight'
        },
        type: m.type
    });
}

/**
 * Zobrazí uživateli všechny zprávy.
 * @param messages Pole zpráv.
 */
function showUserMessages(messages) {
    for (var i = 0; i < messages.length; i++) {
        var message = JSON.parse(messages[i]);
        showUserMessage(message);
    }
}

/**
 * Odstraní z řetězce nepovolené znaky
 * @param r Špinavý řetězec
 * @returns {string|*} Ošetřený řetězec
 */
function prettyURL(r) {
    r = S(r).latinise().toLowerCase();
    r = r.replace(/[^a-zA-Z0-9]/g, "-");
    r = r.replace(/\-{2,}/g, "-");

    if(r.charAt(0) == '-')
        r = r.replace('-', '');
    return r;
}

$.fn.selectRange = function(start, end) {
    return this.each(function() {
        if (typeof end == "undefined") {
            end = start;
        }
        if (start == -1) {
            start = this.value.length;
        }
        if (end == -1) {
            end = this.value.length;
        }
        if (this.setSelectionRange) {
            this.focus();
            this.setSelectionRange(start, end);
        }
        else if (this.createTextRange) {
            var range = this.createTextRange();
            range.collapse(true);
            range.moveEnd('character', end);
            range.moveStart('character', start);
            range.select();
        }
    });
};