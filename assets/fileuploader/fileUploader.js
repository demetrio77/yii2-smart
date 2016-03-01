(function($) {
	$.fn.fileUploader = function(options) {
		var settings = {
			alias:'',
			folder:'',
			filename: '',
			isImage: false,
			value: '',
			tmpl: 'upload,url,server,clear',
			callback: false,
			returnPath: false
		};
		
		if (options) {
			$.extend(settings, options);
		}
		
		var csrfToken = $('meta[name="csrf-token"]').attr("content");
		
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
						(hasUpload ? '<div id="simple-btn" class="btn btn-success js-fileapi-wrapper js-browse">\
							<span class="btn-txt">Загрузить</span>\
							<input type="file" name="file">\
						</div>':'') +
	                	(hasUrl ? ' <button type="button" class="menu-url btn btn-info">По ссылке</button>' : '') +
	                	((hasServer && !settings.filename) ? ' <button type="button" class="menu-server btn btn-warning">Выбрать</button>' : '') +
	                	(hasClear ? ' <button type="button" class="menu-clear  btn btn-danger">Очистить</button>' : '') +
	            '</section>');
				$this.menu = $('.image-uploader-menu', $this.content);
			
			//панель загрузки файла
				$this.content.append('<div class="image-uploader-upload-panel" style="display:none;margin-top:5px;">\
			      <section>\
	                 <label class="label">Имя файла</label>\
	                 <div class="input-group">\
						  <span class="input"><input type="text" class="file-url-name form-control" name="link_filename"></span>\
						  <div class="input-group-addon link-ext"></div>\
				     </div>\
	               </section>\
	               <section>\
	                    <button type="button" id="upload-start" class="btn btn-primary">Скопировать</button>\
	            		<button type="button" class="upload-close btn btn-danger">Cкрыть</button>\
	               </section>\
				</div>');
			
				$this.upload = $('.image-uploader-upload-panel', $this.content);
				$this.fileName = $('.file-url-name', $this.upload);
				$this.fileExt = $('.link-ext', $this.upload);
				
			//панель ввода ссылки
				$this.content.append('<div class="image-uploader-url-panel" style="display:none;margin-top:5px;">\
	               <section>\
	                    <label class="label">Url</label>\
	                    <div class="input"><input type="text" class="file-url-link form-control" maxlength="255"></div>\
	               </section>\
	               <section'+(settings.filename?' style="display:none;"':'')+'>\
	                    <label class="label">Имя файла</label>\
	                    <div class="input-group">\
							<span class="input"><input type="text" class="file-url-name form-control" name="link_filename"></span>\
							<div class="input-group-addon link-ext"></div>\
						</div>\
	               </section>\
	               <section>\
	                    <button type="button" class="url-start btn btn-primary">Скопировать</button>\
	            		<button type="button" class="url-close btn btn-danger">Cкрыть</button>\
	               </section></div>'
				);
				$this.url = $('.image-uploader-url-panel', $this.content);
				$this.linkInput = $('.file-url-link', $this.url);
				$this.linkName = $('.file-url-name', $this.url);
				$this.linkExt = $('.link-ext', $this.url);
				
				function processLinkText(v) {
					var expl = v.split(/[\?\#\=\+\&\,]{1}/);
					v = expl[0];
					expl = v.split('/');
					var t = expl.pop();
					if (t!='') {
						expl = t.split('.');
						if (expl.length>1) {
							return {ext: expl.pop(), name: expl.join('.')};
						}
						return {ext:'', name:t};
					}
					return {ext:'',name:''};
				};
				
				$this.linkInput.bind('paste keyup change input', function(e){
					setTimeout( function() {
						var res = processLinkText($this.linkInput.val());
						$this.linkExt.text(res.ext);
						$this.linkName.val(res.name);
			        }, 100);                					
				});
				
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
			//появление панели загрузки
				$this.uploadShow = function() { $this.upload.slideDown (300); };
			//скрытие панели загрузки
				$this.uploadHide = function() { $this.upload.slideUp (300); };
			
			//публикация рисунка
				$this.apply = function ( result ) 
				{
					if (settings.callback!==false) {
						settings.callback( result );
					}
					
					var value = settings.returnPath ? result.path : result.url;
					$this.val(value);
					
					if (settings.isImage) {
						$this.img.html( '<img src="'+value+'?'+Math.random()+'" width="120" />' );
					}
					else {
						$this.img.html( '<span class="fa fa-lg fa-fw fa-file-o"></span>'+value);
					}
										
					$this.urlHide();
					$this.uploadHide();
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
				$('.menu-url', $this.menu).click( function() { 
					$this.uploadHide();
					if ($this.url.css('display')=='none') {
						$this.urlShow();
					}
					else {
						$this.urlHide(); 
					}
				);
			
			//скрыть панель ссылок
				$('.url-close', $this.url).click( function() { $this.urlHide(); } );
			//скрыть панель загрузки
				$('.upload-close', $this.upload).click( function() { $this.uploadHide(); } );
			// с сервера
				$('.menu-server', $this.menu).click( function() {
					$this.urlHide();
					$this.uploadHide();
					//TODO
				});
			
			// скопировать с сервера
				$('.url-start', $this.url).click( function() 
				{
	            	var link = $this.linkInput.val().trim();
	            	$this.onProgress(0);
	            	
	                if (link!='') {
	    	           $this.urlHide();
	    	           $this.progressShow();
	    	           
	    	           var filename = settings.filename ? settings.filename : ($this.linkName.val().trim() + ($this.linkExt.text().trim()!='' ? "." + $this.linkExt.text().trim() : ''));
	    	           var tmp = Math.floor(Math.random() * 998999)+1000;
	    	           var interval = setInterval(function()
	                   {
	                      $.ajax({
	                        async:true, 
	                        dataType:'json',
	                        url:settings.connector + '?action=progress&options[tmp]='+tmp,
	                        success:function(result){
	                        	if (result.total) {
	                        		var percent = Math.round(result.get*100/result.total);
	                            	$this.onProgress(percent, result.total);
	                        	}
	                        }
	                      });
	                   }, 100);
	                   
	                   $.ajax({
	                       data: {link:link,filename:filename,_csrf: csrfToken},
	                       async:true, dataType:'json',method:'POST',
	                       url: settings.connector + '?action=link&options[force]=1&options[tmp]='+tmp+'&options[alias]='+settings.alias+'&options[path]='+settings.folder,
	                       success:function(result){
	                           clearInterval(interval);
	                           $this.onProgress(100);
	                           $this.progressHide();
	                       	   $this.onProgress(0);
	                           switch (result.status) {
	                            case 'error':
	                                alert(result.message);
	                            break;                          
	                            case 'success':
	                                $this.apply(result);
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
				$this.content.fileapi({
					url: settings.connector + '?action=upload&options[force]=1&options[alias]='+settings.alias+'&options[path]='+settings.folder,
					multiple: false,
					maxSize: 20 * FileAPI.MB,
					autoUpload: settings.filname!='',
				    data: { '_csrf' : csrfToken },
					elements: {
						ctrl: { upload: '#upload-start'},
						size: $this.progressSize,
						active:  { 
							show: $this.progress 
						},
						progress: $this.progressBar,
					},
				    onComplete: function(evt, uiEvt) {
				        $this.uploadHide();
				    	switch (uiEvt.result.status) {
	                        case 'error':
	                            alert(uiEvt.result.message);
	                        break;                          
	                        case 'success':
	                            $this.apply( uiEvt.result );
	                        break;
				        }
				    },
				    onBeforeUpload: function (evt, uiEvt) {
				    	uiEvt.widget.options.data.filename = settings.filename ? settings.filename : ($this.fileName.val().trim() + ($this.fileExt.text().trim()!='' ? "." + $this.fileExt.text().trim() : ''));
				    },
				    onSelect: function (evt, ui){
				    	if (settings.filename) return ;
				    	var file = ui.files[0];
				    	$this.urlHide();
				    	$this.uploadShow();
				    	var res = processLinkText(file.name);
						$this.fileName.val(res.name);
						$this.fileExt.text(res.ext);
				    },
				   	onProgress: function (evt, uiEvt){
				   		$this.progressUploaded.text( Math.round(100*uiEvt.loaded / uiEvt.total) + '%');
					}
				});

			//если не пусто, то нарисовать рисунок
				if (settings.value !='' ) {
					$this.apply( {url: settings.value, path: settings.value} );
				}
		});
	}
})(jQuery);