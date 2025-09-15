/**
 * @license Copyright (c) 2003-2023, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */
CKEDITOR.editorConfig = function(config) {
    config.allowedContent = 'p;h1;h2;h3;h4;h5;h6;pre;br;strong;b;em;i;u;span{style};font[size,color,face];div{style};table[border,cellpadding,cellspacing,width,height];tr;td[colspan,rowspan,width,height];th[colspan,rowspan,width,height];tbody;thead;tfoot;ul;ol;li;blockquote;img[src,alt,width,height,title];a[href,title,target];video[src,width,height,controls,poster];source[src,type]';
    
    config.disallowedContent = 'script;iframe;object;embed;form;input;textarea;select;button;meta;link;style;title;head;html;body;base;bgsound;xml;xmp;plaintext;listing;marquee;blink;keygen;isindex;nextid;spacer;wbr;acronym;applet;basefont;big;center;dir;hgroup;kbd;noframes;s;strike;tt;nobr;noembed;noscript;param;q;rb;rbc;rp;rt;rtc;ruby;samp;small;var';
    
    config.protectedSource = [
        /<script[\s\S]*?<\/script>/gi,
        /<iframe[\s\S]*?<\/iframe>/gi,
        /<object[\s\S]*?<\/object>/gi,
        /<embed[\s\S]*?<\/embed>/gi,
        /javascript\s*:/gi,
        /on\w+\s*=/gi
    ];
    
    config.forcePasteAsPlainText = true;
    config.pasteFilter = 'semantic-content';
    
    config.on = {
        instanceReady: function(evt) {
            var editor = evt.editor;
            
            editor.on('paste', function(evt) {
                var data = evt.data.dataValue;
                if (data && (data.indexOf('<script') !== -1 || data.indexOf('javascript:') !== -1)) {
                    evt.data.dataValue = data.replace(/<script[\s\S]*?<\/script>/gi, '').replace(/javascript\s*:/gi, '');
                }
            });
            
            editor.on('key', function(evt) {
                var data = evt.data;
                if (data && (data.indexOf('<script') !== -1 || data.indexOf('javascript:') !== -1)) {
                    evt.data = data.replace(/<script[\s\S]*?<\/script>/gi, '').replace(/javascript\s*:/gi, '');
                }
            });
        }
    };
  
    config.extraPlugins = 'uploadimage,image,video,clipboard,table,justify,codesnippet,font,colorbutton'; 
    config.toolbarGroups = [
        { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
        { name: 'editing', groups: [ 'find', 'selection', 'editing' ] },
        { name: 'tools', groups: [ 'tools' ] },
        { name: 'basicstyles', groups: [ 'basicstyles'] },
        { name: 'paragraph', groups: [ 'align', 'list', 'indent', 'blocks', 'bidi', 'paragraph', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
        { name: 'styles', groups: [ 'styles' ] },
        { name: 'colors', groups: [ 'colors' ] }
    ];

    config.removeButtons = 'Subscript,Superscript';
    config.format_tags = 'p;h1;h2;h3;h4;h5;h6;pre';
    config.removeDialogTabs = 'image:advanced;link:advanced';
    config.height = 400;

    config.attributes = [ 'left', 'center', 'right' ];
    config.font_names = 'Arial;Times New Roman;Verdana;Tahoma;Courier New;Roboto;Georgia;Comic Sans MS;Impact;Lucida Sans Unicode;Palatino Linotype;Trebuchet MS;Helvetica';
    config.fontSize_sizes = '8/8px;10/10px;12/12px;13/13px;14/14px;16/16px;20/20px;24/24px;36/36px';
    config.fontSize_input = true; 

    
    config.colorButton_enableAutomatic = true;
    config.colorButton_enableMore = true;
    config.colorButton_colors = '000,800000,8B4513,2F4F4F,008080,000080,4B0082,696969,' +
                                'B22222,A52A2A,DAA520,006400,40E0D0,0000CD,800080,808080,' +
                                'F00,FF8C00,FFD700,008000,0FF,00F,EE82EE,A9A9A9,' +
                                'FFA07A,FFA500,FFFF00,00FF00,AFEEEE,ADD8E6,DDA0DD,D3D3D3,' +
                                'FFF0F5,FAEBD7,FFFFE0,F0FFF0,F0FFFF,F0F8FF,E6E6FA,FFF';
    config.colorButton_foreStyle = {
        element: 'span',
        styles: { color: '#(color)' },
        overrides: [ { element: 'font', attributes: { 'color': null } } ]
    };
    config.colorButton_backStyle = {
        element: 'span',
        styles: { 'background-color': '#(color)' }
    };
};
