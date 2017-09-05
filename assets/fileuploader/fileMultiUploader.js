(function($) {
	$.fn.fileMultiUploader = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } 
        else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } 
        else {
            $.error('Method ' + method + ' does not exist on fileMultiUploader');
            return false;
        }
    };
	
	var defaults = {
		alias:'',
		folder:'',
		filename: '',
		callback: false,
		returnPath: false,
		connector:'',
		name:'',
		isImage:false,
		maxFileSize: 0,
		values:[]
	};
	
	var globalObjects = {};
	var csrfToken = $('meta[name="csrf-token"]').attr("content");
	var csrfParam = $('meta[name="csrf-param"]').attr("content");
	
	var methods = {
			
		set: function(result){
			$this = globalObjects[$(this).attr('id')].obj;
	       	$this.apply(result);
	    },
	    
	    clear: function() {
	    	$this = globalObjects[$(this).attr('id')].obj;
	       	$this.toUpload.html("");;
	    },
	    
	    option: function(setting, value) {
	    	$this = globalObjects[$(this).attr('id')].obj;
	    	$this.settings[setting] = value;
	    },
			
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
				
				/* HTML
				   основа-панель*/
					$this.after('<div id="'+id+'" class="file-uploader panel panel-default"><div class="fileapi panel-body"></div></div>');
					$this.content = $('.panel-body', $('#'+id));
				
					$this.content.append('<section class="image-uploader-menu">'+
							'<div id="simple-btn" class="btn btn-success js-fileapi-wrapper js-browse">\
								<span class="btn-txt">Загрузить</span>\
								<input type="file" name="file">\
							</div>' +
		                	//'<button type="button" class="menu-clear  btn btn-danger">Очистить</button>'+
		            '</section>\
				     <section class="upload-progress"></section>');
					
					$this.menu = $('.image-uploader-menu', $this.content);
					$this.toUpload = $('.upload-progress', $this.content);
					
					$this.addFileToUpload = function(File){
						$this.toUpload.append(
							'<div class="file-upload clearfix '+$this.getFileUid(File) +'">\
								<div class="file-upload-preview"></div>\
								<div class="file-upload-name">' + File.name+'</div>\
								<div class="file-upload-progress">\
			                		<div class="progress-bar file-upload-pbar" style="width: 0%;">\
										<span class="progress-text"></span>\
	                				</div>\
			                	</div>\
								<div class="file-upload-status"></div>\
				            </div>'
				        );
					};
					
					$this.addValue = function($filename, $i){
						$this.toUpload.append('<div class="file-upload clearfix api-fileapi'+$i+'">\
								<div class="file-upload-preview"></div>\
								<div class="file-upload-name">'+$filename+'</div>\
								<div class="file-upload-status">Файл успешно загружен на сервер</div>\
								<a data-api="api-fileapi'+$i+'" class="file-upload-delete-button btn btn-danger btn-sm">удалить</a>\
								<input name="' + $this.settings.name+ '[]" type="hidden" value="'+f+'">\
							</div>'
						);
						
						if ($this.settings.isImage){
							if ($this.settings.returnPath){
								$.ajax({
								    async:true, dataType:'json',method:'POST',
				                    url: $this.settings.connector + '?action=item&options[alias]='+$this.settings.alias+'&options[path]='+$filename,
			                        success:function(result){
			                        	if(result.copies.thumb){
			                        		console.log(result.copies.thumb);
			                        		$('.api-fileapi'+$i+' .file-upload-preview', $this.toUpload).html('<img width=100% src="' + result.copies.thumb + '" />').css('display', 'block');
			                        	};
			                        }
				                });
							}
							else {
								$('.api-fileapi'+$i+' .file-upload-preview', $this.toUpload).html('<img width=100% src="' + $filename + '" />').css('display', 'block');
							}
						}
					};
					
					$this.results = [];
					
					$this.getFileUid = function(File){
						return 'api-' + FileAPI.uid(File);
					};
					
					$this.getFileByUid = function(File){
						return $('.' + $this.getFileUid(File), $this.content);
					};
					
					$this.getFileProgress = function(FileUi){
						if (FileUi.complete !==undefined){
							FileUi = $this.getFileByUid(FileUi);
						}
						return $('.file-upload-progress', FileUi);
					}
					
					$this.getFileProgressBar = function(FileUi){
						if (FileUi.complete !==undefined){
							FileUi = $this.getFileByUid(FileUi);
						}
						return $('.progress-bar', FileUi);
					}
					
					$this.getFileProgressText = function(FileUi){
						if (FileUi.complete !==undefined){
							FileUi = $this.getFileByUid(FileUi);
						}
						return $('.progress-text', FileUi);
					}
					
					$this.getFilePreview = function(FileUi){
						if (FileUi.complete !==undefined){
							FileUi = $this.getFileByUid(FileUi);
						}
						return $('.file-upload-preview', FileUi);
					}
					
					$this.getFileStatus = function(FileUi){
						if (FileUi.complete !==undefined){
							FileUi = $this.getFileByUid(FileUi);
						}
						return $('.file-upload-status', FileUi);
					}
					
					$this.getFileByString = function(str){
						return $('.' + str, $this.content);
					}
					
					$this.toUpload.on('click', '.file-upload-delete-button', function(e){
						e.preventDefault();
						var dataApi = $(this).data('api');
						$this.getFileByString(dataApi).remove();						
					});
					
					if ($this.settings.values){
						for (i in $this.settings.values){
							var f = $this.settings.values[i];
							console.log(f);
							$this.addValue(f, i);
						}
					}
					
					var Data = {};
					Data[csrfParam] = csrfToken;
								
				// FILE API
					$this.content.fileapi({
						url: $this.settings.connector + '?action=upload&options[force]=1&options[alias]='+$this.settings.alias+'&options[path]='+$this.settings.folder,
						multiple: true,
						maxSize: ($this.settings.maxFileSize?$this.settings.maxFileSize:100) * FileAPI.MB,
						autoUpload: true,
						duplicate: true,
					    data: Data,
					    onBeforeUpload: function (evt, uiEvt) {
					    	if ($this.settings.filename){
					    		uiEvt.widget.options.data.filename = $this.settings.filename;
					    	}
					    },
					    onSelect: function (evt, ui){
					    	for (i in ui.files) {
					    		$this.addFileToUpload(ui.files[i]);
					    	}
					    },
					    onFileProgress: function (evt, uiEvt){
					   		var width = Math.round(100*uiEvt.loaded / uiEvt.total);
					   		var fileUi = $this.getFileByUid(uiEvt.file);
					   		$this.getFileProgressBar(fileUi).css('width', width + '%');
					   		$this.getFileProgressText(fileUi).text( width + '%');
						},
						onFileComplete: function (evt, uiEvt){
					   		var result = uiEvt.result;
					   		var fileUi = $this.getFileByUid(uiEvt.file);
					   		$this.getFileProgress(fileUi).remove();
					   		
					   		if ($this.settings.isImage && result.copies && result.copies.thumb){
					   			$this.getFilePreview(fileUi).append('<img width=100% src="' + result.copies.thumb + '" />').css('display', 'block')
					   		}
					   		if (result.status=='success') {
					   			$this.getFileStatus(fileUi).text('Файл успешно загружен на сервер');
					   			fileUi.append('<a data-api="' +$this.getFileUid(uiEvt.file)+ '" class="file-upload-delete-button btn btn-danger btn-sm">удалить</a>');
					   			fileUi.append('<input name="' + $this.settings.name +'[]" type="hidden" value="' + ($this.settings.returnPath ? result.path : result.url) +'" />');
					   		}
					   		else {
					   			$this.getFileStatus(fileUi).text(result.message).addClass('has-error');
					   		}
					   		
					   		$this.results.push({
					   			file: uiEvt.file,
					   			result: result
					   		});
						},
						onComplete: function(evt, uiEvt){
							if ($this.settings.callback){
								$this.settings.callback(evt, uiEvt, $this.results);
							}
						}
					});
							
					globalObjects[$this.attr('id')] = {obj: $this};
				});
	    }
	}
})(jQuery);

function fileMultiUploader() {
	this.set = function($id, $value) {
		$('#'+$id).fileMultiUploader('set', $value);
	}
}

var fileMultiUploader = new fileMultiUploader();