WeDo.AjaxForm = (function(){
    var items = {};
   
    ajaxform = function(itemref)
    {
        this.itemRef = 'form#' + itemref;
        
        this.options = {
            beforeSerialize: null,
            beforeSubmit: function(arr, $form, options) { console.log($(this.target)); $(this.target).addClass("loading");},
            clearForm: false,        // clear all form fields after successful submit 
            data: null,
            dataType:  null,        // 'xml', 'script', or 'json' (expected server response type)
            error: null,
            forceSync: false,
            iframe: false,
            iframeSrc: 'about:blank',
            iframeTarget: null,           
            replaceTarget: null,
            resetForm: false,        // reset the form after successful submit 
            semantic: false,
            success: function(responseText, statusText, xhr, $form) { $(".loading").toggleClass("loading"); },
            target: null,
            type: null,
            url:  null,        // override for form's 'action' attribute 
            uploadProgress: null,
            type: null       // 'get' or 'post', override for form's 'method' attribute
            
        };
    }
   
    ajaxform.prototype = {
        setOptions : function(options)
        {
            this.options = options;
        },
        setItemRef : function(itemRef)
        {
            this.itemRef = itemRef;
        },
        ajaxify: function()
        {
            //clean empty properties;
            for(var key in this.options)
                if(this.options[key] == undefined) delete(this.options[key]);
            $(this.itemRef).ajaxForm(this.options);
            console.log(this);
        }
    } // Return the public facing methods for the App
   
    return {
        add: function (paramId) {
            items[paramId] = new ajaxform(paramId);
           // console.log('added item with param' + paramId);
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
        ajaxify: function(paramId)
        {
            items[paramId].ajaxify();
        }
    };
}());


var WeDo_AjaxForm = WeDo.AjaxForm;