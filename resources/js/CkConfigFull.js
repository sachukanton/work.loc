CKEDITOR.editorConfig = function (config) {
    config.extraPlugins = 'filebrowser,uikit,shortcut';
    config.toolbar = [
        {
            name: 'tools',
            items: ['Maximize', 'ShowBlocks', 'Source', '-', 'CopyFormatting', 'RemoveFormat', 'NewPage']
        },
        {
            name: 'basicstyles',
            groups: ['basicstyles', 'cleanup'],
            items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript']
        },
        {
            name: 'colors',
            items: ['TextColor', 'BGColor']
        },
        {
            name: 'paragraph',
            groups: ['list', 'indent', 'blocks', 'align', 'bidi'],
            items: ['NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Blockquote']
        },
        {
            name: 'links',
            items: ['Link', 'Unlink', 'Anchor']
        },
        {
            name: 'insert',
            items: ['Image', 'Table', '-', 'CreateDiv', 'Shortcut']
        },
        {
            name: 'styles',
            items: ['Format', 'Font', 'FontSize']
        }
    ];
    config.toolbarCanCollapse  = true;
    config.removeButtons = 'about';
    config.format_tags = 'p;h1;h2;h3;pre;div';
    config.allowedContent = true;
    config.removeDialogTabs = 'image:advanced';
    config.filebrowserBrowseUrl = '/library/ckeditor_sdk/ckfinder/ckfinder.html';
    config.filebrowserUploadUrl = '/library/ckeditor_sdk/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files';
    config.authentication = true;
};
