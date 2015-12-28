jQuery(document).ready(function () {
    var getArticles = function (catID) {
        var showOptions = function (select, articles) {
            select = $(select);
            select.empty();

            var emptyOption = $('<option/>', {value: -1, html: "Vyberte..."});
            select.append(emptyOption);
            for (var i = 0; i < articles.length; i++) {
                var article = articles[i];
                var option = $('<option/>', {value: article['article_id'], html: article['article_title']});
                select.append(option);
            }
        };

        jQuery.ajax({
            type: 'post',
            url: 'article-manager/get/articles/' + catID,
            success: function (result) {
                result = JSON.parse(result);
                if (result.success) {
                    var articles = JSON.parse(result.data.articles);
                    showOptions("#article-previous", articles);
                    showOptions("#article-next", articles);
                }
            }
        });
    };

    jQuery('#article-category').change(function () {
        var opt = jQuery('#article-category').find('option:selected').val();
        if (opt > 0)
            getArticles(opt);
    });

    var buttons = [
        {class: 'h1', before: '\# ', tooltip: 'Nadpis 1'},
        {class: 'h2', before: '\## ', tooltip: 'Nadpis 2'},
        {class: 'h3', before: '\### ', tooltip: 'Nadpis 3'},
        {class: 'h4', before: '\#### ', tooltip: 'Nadpis 4'},
        {class: 'h5', before: '\##### ', tooltip: 'Nadpis 5'},
        {class: 'h6', before: '\###### ', tooltip: 'Nadpis 6'},
        {class: 'bold', before: '**', after: '**', tooltip: 'Tučný'},
        {class: 'italic', before: '*', after: '*', tooltip: 'Kurzíva'},
        {class: 'strikethrough', before: '~~', after: '~~', tooltip: 'Přeškrtnutý'},
        {class: 'code', before: '```\n', after: '\n```', tooltip: 'Vložit kód'},
        {class: 'inline-code', before: '`', after: '`', tooltip: 'Vložit kód do řádku'},
        {class: 'quotes', before: '> ', tooltip: 'Citace'},
        {class: 'ordered-list', before: '1. \n2. \n3. ', after: '', space: -8, tooltip: 'Uspořádaný seznam'},
        {class: 'unordered-list', before: '- \n- \n- ', after: '', space: -6, tooltip: 'Neuspořádaný seznam'},
        {class: 'link', before: '[text]', after: '(odkaz)', space: -5, tooltip: 'Odkaz'}
    ];
    var editor = new EditorJQ(
        '#mainEditorContainer',
        {
            controller: 'upload',
            action: 'store',
            buttons: buttons,
            attachment: true
        });
    editor.init();

    jQuery.ajax({
        type: 'post',
        url: 'article-manager/get/article-content',
        success: function (result) {
            result = JSON.parse(result);
            if (result.success)
                editor.setContent(JSON.parse(result.data.article));

            showUserMessages(result.messages);
        }
    });
});