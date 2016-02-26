(function($) {
	$.fn.fileUploader = function(options) {
		var settings = {
			alias:'',
			fileName: '',
			image: false,
			
			//name : 'imageUpload', //имя инпута для загрузки рисунка	
			value: '',
			tmpl: 'upload,url,server,clear',
			callback: false,
			clearAfterUpload: false,
			returnPath: false
		};
		
		if (options) {
			$.extend(settings, options);
		}
		
		return this.each(function()
		{
			var $this = $(this);
			var id = $this.attr('id') + '-uploader';

			// HTML
			//основа-панель
				$this.after('<div id="'+id+'" class="panel panel-default"><div class="fileapi panel-body"></div></div>');
				$this.content = $('.panel-body', $('#'+id));
			
			//меню
				
				var hasUpload = settings.tmpl.indexOf('upload')>-1;
				var hasUrl    = settings.tmpl.indexOf('url')>-1;
				var hasServer = settings.tmpl.indexOf('s')>-1;
				var hasClear    = settings.tmpl.indexOf('clear')>-1;				
				
				$this.content.append('<section class="image-uploader-menu">'+
						(hasUpload ? '<div id="simple-btn" class="btn btn-success js-fileapi-wrapper js-browse">'+
							'<span class="btn-txt">Загрузить</span>'+
							'<input type="file" name="'+settings.name+ '">'+
						'</div>':'')+
	                	(hasUrl ? ' <button type="button" class="menu-url btn btn-info">По ссылке</button>' : '') +
	                	(hasServer ? ' <button type="button" class="menu-server btn btn-warning">Выбрать</button>' : '') +
	                	(hasClear ? ' <button type="button" class="menu-clear  btn btn-danger">Очистить</button>' : '') +
	            '</section>');
				$this.menu = $('.image-uploader-menu', $this.content);
			
			//панель ввода ссылки
				$this.content.append('<div class="image-uploader-url-panel" style="display:none;margin-top:5px;">'+
	                '<section>'+
	                    '<label class="label">Url</label>'+
	                    '<div class="input"><input type="text" class="file-url-link form-control" maxlength="255"></div>'+
	               '</section>'+
	               '<section>'+
	                    '<label class="label">Имя файла</label>'+
	                    '<div class="input"><input type="text" class="file-url-name form-control" maxlength="255"></div>'+
	               '</section>'+
	               '<section>'+
	                    '<button type="button" class="url-start btn btn-primary">Скопировать</button>'+
	            		' <button type="button" class="url-close btn btn-danger">Cкрыть</button>'+
	               '</section></div>'
				);
				$this.url = $('.image-uploader-url-panel', $this.content);
			
			//прогресс-бар
				$this.content.append('<div class="image-uploader-progress" style="display:none;">'+
	                '<span class="text"> Загрузка <span class="pull-right"><span class="file-upload-progress"></span> из <span class="file-upload-size"></span></span></span>'+
					'<div class="progress"><div class="progress-bar bg-color-blueDark file-upload-pbar" style="width: 0%;"></div></div> '+
	            '</div>');
				$this.progress = $('.image-uploader-progress', $this.content);
				$this.progressBar = $('.file-upload-pbar', $this.progress);
				$this.progressSize = $('.file-upload-size', $this.progress);
				$this.progressUploaded = $('.file-upload-progress', $this.progress);
			
			//рисунок
				$this.content.append('<div class="image-uploader-image" style="margin-top:5px;"></div>');
				$this.img = $('.image-uploader-image', $this.content);
			
			// ФУНКЦИИ
				
			//появление панели ссылки
				$this.urlShow = function() { $this.url.slideDown (300); };
			
			//скрытие панели ссылки
				$this.urlHide = function() { $this.url.slideUp (300); };
				
			//публикация рисунка
				$this.apply = function ( url, result ) 
				{
					if (settings.callback!==false) {
						settings.callback( url, result );
					}
					$this.val( url );
					
					if (settings.returnPath) {
						$this.val(result.fileName);
						$this.img.html('<span class="fa fa-lg fa-fw fa-file-o"></span>'+result.fileName);
					} 
					else if (!settings.clearAfterUpload) {
						if (settings.isImage) {
							$this.img.html( '<img src="'+url+'" width=' + IMAGE_WIDTH + ' />' );
						}
						else {
							$this.img.html( '<a target=_blank href="'+url+'"><span class="fa fa-lg fa-fw fa-file-o"></span>'+url+'</a>');
						}
					}
					else {
						$this.urlHide();
					}
				};
			
			//удаление рисунка
				$this.clearImage = function ( url ) 
				{
					$this.val( '' );
					$this.img.html( '' );
				};
			//прогресс бар изменение длины
				$this.onProgress = function (percent, total)
				{
					$this.progressBar.css('width', percent + '%');
					if (total) {
						$this.progressUploaded.text(percent+'%');
						$this.progressSize.text( total);
					}
				};			
			//появление прогресс бара
				$this.progressShow = function() { $this.progress.css('display', 'block'); };
			//скрытие прогресс бара
				$this.progressHide = function() { $this.progress.css('display', 'none'); };
				
			//ДЕЙСТВИЯ КНОПОК
			//с диска
				$('input[name="' + settings.name + '"]').click( function() {
					$this.urlHide();
					return true;
				});
			
			//очистить рисунок
				$('.menu-clear', $this.menu).click( function() { $this.clearImage(); } );
			
			//по ссылке
				$('.menu-url', $this.menu).click( function() { if ($this.url.css('display')=='none') $this.urlShow(); else $this.urlHide();} );
			
			//скрыть панель ссылок
				$('.url-close', $this.url).click( function() { $this.urlHide(); } );
			
			// с сервера
				$('.menu-server', $this.menu).click( function() {
					$this.urlHide();
					var finder = new CKFinder();
	                finder.selectActionFunction=function(fileUrl){
	                    $this.apply( fileUrl );
	                };
	                finder.popup();
				});
			
			// скопировать с сервера
				$('.url-start', $this.url).click( function() 
				{
	            	var link = $( '.file-url-link', $this.url ).val().trim();
	            	$this.onProgress(0);
	            	
	                if (link!='') {
	    	           $this.urlHide();
	    	           $this.progressShow();
	    	           
	    	           var filename =  $('.file-url-name', $this.url ).val().trim();
	                   var interval = setInterval(function()
	                   {
	                      $.ajax({
	                        async:true, dataType:'json',url:settings.progressUrl,
	                        success:function(result){
	                        	if (result.total) {
	                        		var percent = Math.round(result.get*100/result.total);
	                            	$this.onProgress(percent, result.total);
	                        	}
	                        }
	                      });
	                   }, 100);
	                   
	                   $.ajax({
	                       data: {link:link,filename:filename},
	                       async:true, dataType:'json',method:'POST',
	                       url: settings.urlUrl,
	                       success:function(result){
	                           clearInterval(interval);
	                           $this.onProgress(100);
	                           $this.progressHide();
	                       	   $this.onProgress(0);
	                           switch (result.status) {
	                            case 0:
	                                alert(result.message);
	                            break;                          
	                            case 1:
	                                $this.apply( result.Url, result );
	                            break;
	    			          }
	                       },
	                       error:function() {
	                    	    $this.progressHide();
	                       	    $this.onProgress(0);                            
	                            clearInterval(interval);
	                            alert('Загрузка закончилась ошибкой');
	                       }
	                   });      
	                }
				});
			// FILE API
			/*	$this.content.fileapi({
					url:  settings.uploadUrl,
					multiple: false,
					maxSize: 20 * FileAPI.MB,
					autoUpload: true,
				    data: { '_csrf' : settings.csrfToken },
					elements: {
						size: $this.progressSize,
						active:  { 
							show: $this.progress 
						},
						progress: $this.progressBar,
					},
				    onComplete: function(evt, uiEvt) {
				        switch (uiEvt.result.status) {
	                        case 0:
	                            alert(uiEvt.result.message);
	                        break;                          
	                        case 1:
	                            $this.apply( uiEvt.result.Url, uiEvt.result );
	                        break;
				        }
				    },
				   	onProgress: function (evt, uiEvt){
				   		$this.progressUploaded.text( Math.round(100*uiEvt.loaded / uiEvt.total) + '%');
					}
				});
			*/
			//если не пусто, то нарисовать рисунок
				if (settings.value !='' ) {
					$this.apply( settings.value, {'fileName' : settings.value } );
				}
		});
	}
})(jQuery);