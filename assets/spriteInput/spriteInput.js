(function($) {
	$.fn.spriteInput = function(options) {
		//var IMAGE_WIDTH = 150;		
		
		var settings = {
			x: 0,
			y: 0,
			width: 0,
			height: 0,
			sprite: ''
		/*	name : 'imageUpload', //имя инпута для загрузки рисунка	
			uploadUrl : 'upload',
			progressUrl: 'progress',
			urlUrl: 'url',
			csrfToken : '',
			value: '',
			tmpl: 'upload,url,server,clear',
			callback: false,
			isImage: false,
			clearAfterUpload: false,
			returnPath: false*/
		};
		if (options) {
			$.extend(settings, options);
		}
		
		return this.each(function()
		{
			var $this = $(this);
			var id = $this.attr('id');
			var wrapId = 'jcrop_'+id;
			
			
			//HTML
			$this.wrap( '<div class="row" id="'+wrapId+'"><section class="col col-xs-12 col-sm-12 jcrop-inputs"></section></div>');		
			
			//Весь блок
			$this.content = $('#'+wrapId);
			$this.inputsDiv = $('.jcrop-inputs', $this.content);
			$this.inputsDiv.append('<div class="input-group">\
					<span class="input-group-addon">x:</span>\
					<span class="input"><input type="text" id="x'+id+'" value="" class="form-control" /></span>\
					<span class="input-group-addon">y:</span>\
					<span class="input"><input type="text" id="y'+id+'" value="" class="form-control" /></span>\
				</div>\
				<div style="padding-top:10px"><div class="jcrop-preview-pane"></div></div>');
			
			$this.content.append('<section class="col col-xs-12 col-sm-12"><div class="jcrop-sprite"><img src="'+settings.sprite+'" /></div></section>');
			
			//Инпуты
			$this.xInput = $('#x'+id);
			$this.yInput = $('#y'+id);
			
			//Cпрайт
			$this.sprite = $('.jcrop-sprite IMG', $this.content);
			
			//Pane
			$this.pane = $('.jcrop-preview-pane', $this.content);
			$this.pane.css({
				'width': settings.width, 
				'height': settings.height,
				'background-image': 'url('+settings.sprite+')',
				'background-position' : $this.val()
			});
			
			$this.setXY = function(x,y){
				$this.xInput.val(x);
				$this.yInput.val(y);
				$this.x = x;
				$this.y = y;
				if (x===null && y===null) {
					$this.val('');
					return ;
				}
				x = x>0 ? -x : 0;
				y = y>0 ? -y : 0;
				var val = x+'px '+y+'px';
				$this.val(val);
				$this.pane.css({'background-position': val});
			}
						
			$this.setXY(settings.x, settings.y);
			
			$this.jcrop_api = null;
			
			$this.updateInputs = function(c) {
				$this.setXY(c.x,c.y);
			};
			
			$this.sprite.Jcrop({
				minSize : [settings.width, settings.height],
				maxSize : [settings.width, settings.height],
				setSelect: [$this.x, $this.y, $this.x + settings.width, $this.y + settings.height],
				bgColor: 'white',
				bgOpacity: 0.5,
				onChange : function(c){
					$this.updateInputs(c);
				},
				onSelect : function(c){
					$this.updateInputs(c);
				},
				onRelease: function() {
					$this.setXY(null, null);
					$this.pane.css({'background-position': '-10000px -10000px'});
				}
			}, function(){
				$this.jcrop_api = this
			});
			
			$this.onChangeInput = function(obj){
				if (obj.val().match(/[^0-9]/g)) {
			        obj.val( obj.val().replace(/[^0-9]/g, ''));
			    }
				$this.setXY($this.xInput.val(),$this.yInput.val());
				$this.jcrop_api.setSelect([$this.x,$this.y,$this.x + settings.width, $this.y + settings.height]);
			};
			
			$this.xInput.bind("change keyup input click", function() {
				$this.onChangeInput($(this));
			});
			
			$this.yInput.bind("change keyup input click", function() {
				$this.onChangeInput($(this));
			});
		});
	}
})(jQuery);