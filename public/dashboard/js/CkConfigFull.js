CKEDITOR.editorConfig = function (config) {
    config.extraPlugins = 'filebrowser,uikit,short_code';
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
            items: ['Image', 'Table', '-', 'CreateDiv', 'ShortCode']
        },
        {
            name: 'styles',
            items: ['Format', 'Font', 'FontSize']
        }
    ];
    config.removeButtons = 'about';
    config.format_tags = 'p;h1;h2;h3;h4;pre;div';
    config.allowedContent = true;
    config.removeDialogTabs = 'image:advanced';
    config.filebrowserBrowseUrl = '/dashboard/library/ckeditor_sdk/kcfinder/browse.php?type=files';
    config.filebrowserImageBrowseUrl = '/dashboard/library/ckeditor_sdk/kcfinder/browse.php?type=images';
    config.filebrowserFlashBrowseUrl = '/dashboard/library/ckeditor_sdk/kcfinder/browse.php?type=flash';
    config.filebrowserUploadUrl = '/dashboard/library/ckeditor_sdk/kcfinder/upload.php?type=files';
    config.filebrowserImageUploadUrl = '/dashboard/library/ckeditor_sdk/kcfinder/upload.php?type=images';
    config.filebrowserFlashUploadUrl = '/dashboard/library/ckeditor_sdk/kcfinder/upload.php?type=flash';
};
