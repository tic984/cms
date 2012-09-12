WeDo.CkEditor = (function () {
    // Initialize the application
    var items = {};
    
    editor = function(itemref)
    {
        //this.itemRef = '#uploader_'+itemref;
        this.itemRef = 'textarea#'+itemref;
        
        this.options = {
            customConfig : 'config.js',
            autoUpdateElement : true,
            baseHref : '',
            contentsCss : CKEDITOR.basePath + 'contents.css',
            contentsLangDirection : 'ui',
            contentsLanguage : '',
            language : '',
            defaultLanguage : 'en',
            enterMode : CKEDITOR.ENTER_P,
            forceEnterMode : false,
            shiftEnterMode : CKEDITOR.ENTER_BR,
            corePlugins : '',
            docType : '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
            bodyId : '',
            bodyClass : '',
            fullPage : false,
            height : 200,
            plugins :
 		'about,' +
 		'a11yhelp,' +
		'basicstyles,' +
 		'bidi,' +
 		'blockquote,' +
 		'button,' +
 		'clipboard,' +
 		'colorbutton,' +
 		'colordialog,' +
 		'contextmenu,' +
		'dialogadvtab,' +
		'div,' +
		'elementspath,' +
		'enterkey,' +
		'entities,' +
		'filebrowser,' +
		'find,' +
		'flash,' +
		'font,' +
		'format,' +
		'forms,' +
		'horizontalrule,' +
		'htmldataprocessor,' +
		'iframe,' +
		'image,' +
		'indent,' +
		'justify,' +
		'keystrokes,' +
		'link,' +
		'list,' +
		'liststyle,' +
		'maximize,' +
		'newpage,' +
		'pagebreak,' +
		'pastefromword,' +
		'pastetext,' +
		'popup,' +
		'preview,' +
		'print,' +
		'removeformat,' +
		'resize,' +
		'save,' +
		'scayt,' +
		'showblocks,' +
		'showborders,' +
		'smiley,' +
		'sourcearea,' +
		'specialchar,' +
		'stylescombo,' +
		'tab,' +
		'table,' +
		'tabletools,' +
		'templates,' +
		'toolbar,' +
		'undo,' +
		'wsc,' +
		'wysiwygarea',
            extraPlugins : '',
            removePlugins : '',
            protectedSource : [],
            tabIndex : 0,
            theme : 'default',
            skin : 'kama',
            width : '',
            baseFloatZIndex : 10000
        };
    
    }
    
    editor.prototype = {
        setOptions : function(options)
        {
            this.options = options;
        },
        setItemRef : function(itemRef)
        {
            this.itemRef = itemRef;
        },
        ckEditorizeItem : function()
        {
            //clean empty properties;
            for(var key in this.options)
                if(this.options[key] == undefined) delete(this.options[key]);
            $(this.itemRef).ckeditor(this.options);
        }
    }
    
    
    // Return the public facing methods for the App
    return {
        add: function (paramId) {
            items[paramId] = new editor(paramId);
        },
        setOption: function (paramId, property, value) {
            items[paramId].options[property] = value;
        },
        setOptions: function (paramId, properties) {
            items[paramId].options = properties;
        },
        debug: function()
        {
            console.log(items)
        },
        debugItem: function(paramId)
        {
            console.log(items[paramId]);
        },
        ckEditorizeItem: function(paramId)
        {
            items[paramId].ckEditorizeItem();
        }
    };
}());


var WeDo_CkEditor = WeDo.CkEditor;
var CKEDITOR_BASEPATH = '/js/admin/ckeditor/';