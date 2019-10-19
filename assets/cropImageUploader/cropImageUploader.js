(function($) {
    $.fn.cropImageUploader = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        }
        else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        }
        else {
            $.error('Method ' + method + ' does not exist on cropImageUploader');
            return false;
        }
    };

    var defaults = {
        value:'',
        uploadTmpl:'upload,url',
        callback:function(file, cropUploader){return true},
        returnPath: false,
        filename: '',
        connector: '',
        alias: '',
        tempAlias: '',
        folder: '',
        cropWidth: 100,
        cropHeight: 100,
        template:'<div class="row" id="{id}"><div class="col-xs-12 col-md-4">{image}</div><div class="col-xs-12 col-md-8"><div>{input}</div></div></div>'
    };//<img id="' + id + 'image" style="max-width:100%" src="'+ $this.settings.value +'" />
    //                  '<input type="hidden" id="' + id + 'temp" name="' + $this.attr('name') + 'temp" />'+

    var globalObjects = {};
    var csrfToken = $('meta[name="csrf-token"]').attr("content");
    var csrfParam = $('meta[name="csrf-param"]').attr("content");

    var methods = {
        init: function (options) {

            return this.each(function()
            {
                var $this = $(this);
                if (options.filename=='time') {
                    options.filename = '{{time}}';
                }
                $this.settings = $.extend({}, defaults, options || {});
                $this.id = $this.attr('id');

                var id = $this.id+'_uploader';

                // HTML
                //основа-панель
                $this.after( $this.settings.template
                    .replace('{id}', id)
                    .replace('{image}', '<img id="' + id + 'image" style="max-width:100%" src="'+ $this.settings.value +'" />')
                    .replace('{input}', '<input type="hidden" id="' + id + 'temp" name="' + $this.attr('name') + 'temp" />')
                );

                $this.saveButton = false;
                $this.jcrop = null;
                $this.image = $('#' + id + 'image');

                $('#' + id + 'temp').fileUploader({
                    tmpl: $this.settings.uploadTmpl,
                    callback: function(result, uploader){
                        let img = $('img', uploader.img);
                        img.removeAttr('width').css('max-width', '100%');
                        img.Jcrop({
                            aspectRatio: $this.settings.cropWidth/$this.settings.cropHeight,
                            onSelect: function () {
                                if (!$this.saveButton) {
                                    uploader.menu.append(' <a id="' + id + 'save" class="btn btn-danger">Сохранить</a>')
                                    $this.saveButton = $('#' + id + 'save');
                                    $this.saveButton.click(function () {
                                        let selected = $this.jcrop.tellSelect();
                                        // Create new offscreen image to test
                                        let copyImage = new Image();
                                        copyImage.src = img.attr("src");
                                        // Get accurate measurements from that.
                                        let realWidth = copyImage.width;
                                        copyImage.remove();
                                        let screenWidth = img.width();
                                        let ratio = screenWidth / realWidth;
                                        let x = Math.round(selected.x / ratio);
                                        let y = Math.round(selected.y / ratio);
                                        let x2 = Math.round(selected.x2 / ratio);
                                        let y2 = Math.round(selected.y2 / ratio);
                                        let data = {
                                            action: 'cropResize',
                                            alias: $this.settings.alias,
                                            folder: $this.settings.folder,
                                            width: $this.settings.cropWidth,
                                            height: $this.settings.cropHeight,
                                            x: x,
                                            y: y,
                                            x2: x2,
                                            y2: y2
                                        };
                                        data[csrfParam] = csrfToken;
                                        $.ajax({
                                            url: $this.settings.connector + '?action=image&options[alias]=' +
                                                encodeURIComponent($this.settings.tempAlias) +
                                                '&options[path]=' + encodeURIComponent(result.path),
                                            method: 'POST',
                                            data: data,
                                            dataType: 'json',
                                            success: function (json) {
                                                if (json.status == 'success') {
                                                    $this.jcrop.destroy();
                                                    uploader.clearImage();
                                                    var url = document.createElement('a');
                                                    url.href = $this.image.attr('src');
                                                    $this.saveButton.remove();
                                                    $this.saveButton = false;
                                                    $('#' + id + 'image').attr('src', json.file.href + '?t=' + (new Date()).getMilliseconds());
                                                    $this.settings.callback(json.file, $this);
                                                }
                                            }
                                        });
                                    });
                                }
                            }
                        }, function(){
                            $this.jcrop = this;
                        });
                    },
                    returnPath: true,
                    isImage:true,
                    filename: $this.settings.filename,
                    connector: $this.settings.connector,
                    alias: $this.settings.tempAlias
                });

                globalObjects[$this.attr('id')] = {obj: $this};
            });
        }
    }
})(jQuery);

function cropImageUploader() {
    this.instance = function($id) {
        return $('#'+$id).cropImageUploader('instance');
    }
}

var cropImageUploader = new cropImageUploader();