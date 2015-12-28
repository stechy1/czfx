jQuery(document).ready(function () {
    var buttons = [
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
            buttons: buttons,
            areaName: 'post_content'
        });
    editor.init();
});