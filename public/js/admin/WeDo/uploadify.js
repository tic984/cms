WeDo.Uploadify = (function () {
    // Initialize the application
    var items = {};
    
    uploadifier = function(itemref)
    {
        //this.itemRef = '#uploader_'+itemref;
        this.itemRef = '.uploadify#'+itemref;
        this.classSelector = '.'+itemref;
        this.classUri = $(this.classSelector +'[name="classUri"]').val();
        this.fieldName = $(this.classSelector +'[name="fieldName"]').val();
        this.contentId = $(this.classSelector +'[name="contentId"]').val();
        
        this.formData = {"fileDataName" : this.fieldName, "classUri": this.classUri, "contentId":this.contentId ,"field": this.itemRef};
        
        this.options = {
            'swf' : '/js/admin/uploadify/uploadify.swf',
            'uploader' : '/admin/default/uploader/upload',
            'auto' : true,
            'buttonClass' : null,
            'buttonCursor' : null,
            'buttonText' : null,
            'checkExisting' : null,
            'debug' : null,
            'fileObjName' : 'uploaded',
            'fileSizeLimit' : null,
            'fileTypeDesc' : null,
            'fileTypeExts' : null,
            'formData' : this.formData,
            'folder' : null,
            'height' : null,
            'method' : null,
            'multi' : false,
            'overrideEvents' : null,
            'preventCaching' : null,
            'progressData' : null,
            'queueID' : null,
            'queueSizeLimit' : null,
            'removeCompleted' : null,
            'removeTimeout' : null,
            'requeueErrors' : null,
            'successTimeout' : null,
            'uploadLimit' : null,
            'width' : null,
            'onCancel' : null,
            'onClearQueue' : null,
            'onDestroy' : null,
            'onDialogClose' : null,
            'onDialogOpen' : null,
            'onDisable' : null,
            'onEnable' : null,
            'onFallback' : null,
            'onInit' : null,
            'onQueueCompleted' : null,
            'onSelect' : null,
            'onSelectError' : null,
            'onSWFReady' : null,
            'onUploadCompleted' : null,
            'onUploadError' : null,
            'onUploadProgress' : null,
            'onUploadStart' : null ,
            'onUploadSuccess' : function(file, data, response)
                                {
                                    console.log("onUploadSuccess");
                                    console.log(data);
                                    data = jQuery.parseJSON(data);
                                    
                                    switch(data.outcome)
                                    {

                                        case 'success':
                                            var fName = data.payload.fName;
                                            var fUrl = '/libs/timthumb/tt.php?src=' + data.payload.fUrl + '&width=500&zc=1';
                                            var previewId = "#preview_" + data.payload.extraparams.field;
                                            $(previewId).attr("src", fUrl);
                                            break;
                                        case 'error':
                                            break;
                                        default:
                                            break;
                                    }
                                },
            'cancel' : null,
            'destroy' : null,
            'disable' : null,
            'settings' : null,
            'stop' : null,
            'upload' : null
        };
    
    }
    
    uploadifier.prototype = {
        setOptions : function(options)
        {
            this.options = options;
        },
        setItemRef : function(itemRef)
        {
            this.itemRef = itemRef;
        },
        uploadifyItem : function()
        {
            //clean empty properties;
            for(var key in this.options)
                if(this.options[key] == undefined) delete(this.options[key]);
            $(this.itemRef).uploadify(this.options);
            //console.log(this.options);
        }
    }
    
    
    // Return the public facing methods for the App
    return {
        add: function (paramId) {
            items[paramId] = new uploadifier(paramId);
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
        uploadifyItem: function(paramId)
        {
            items[paramId].uploadifyItem();
        }
    };
}());


var WeDo_Uploadify = WeDo.Uploadify;