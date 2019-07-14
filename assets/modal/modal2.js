function dkmodal( options )
{
	var self = this;
	this.id = '';
	this.div = undefined;
	this.title = '';
	this.body = undefined;
	this.footer = undefined;
	this.buttons = [];
	this.onClose = function() {};
	this.options = {
		width: '600px'	
	}
	
	$.extend(this.options, options);
	
	this.init = function() {
		var id = Math.round(Math.random()*100000);
		do {
			this.id = 'modal'+id;
			id++;
		}
		while($('#'+this.id).length>0);
		
		$('body').append('<div id="'+this.id+'"></div>');
		this.div = $('#'+this.id);
		this.div.addClass('modal')
				.addClass('fade')
				.attr('role','dialog')
				.attr('aria-labelledby','myLargeModalLabel')
				.attr('aria-hidden',true)
				.html('<div class="modal-dialog"' + (this.options.width ?'style="width:'+this.options.width+';"': '') + '><div class="modal-content"><div class="modal-header">'+
			       		'<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title">'+
			       		'</h4></div><div class="modal-body"></div><div class="modal-footer"></div></div></div>');
		
		this.div.on('hidden.bs.modal', function (e) {
			self.onClose();
			self.div.remove();
			delete self;
		});
		this.title  = $('.modal-title', this.div);
		this.body   = $('.modal-body' , this.div);
		this.footer = $('.modal-footer', this.div);		
	}
	
	this.init();
	
	this.close = function() {
		this.div.modal('hide');
	};
	
	this.open = function() {
		console.log( $('#'+this.id).attr('id') );
		this.div.modal('show');
	};
	

	function buttons(buttons)
	{
		this.items = [];
		
		this.add = function(elem) {
			var button = {
				type: 'submit',
				caption: '',
				hidden: false,
				url: '',
				className:'',
				action: function(){},
				beforeSave: function(){return true;},
				afterSave: function(h){},
				afterValidate: function(h){return true;},
				target: undefined,
				loading: false,
				id:'',
				left: false,
				disabled:false
			};
			
			$.extend(button, elem);
			
			switch (button.type) {
				case 'dismiss':
					self.footer.append('<button '+(button.id ? 'data-button-id="'+button.id+'"' : '')+'type="button" class="btn btn-default" data-dismiss="modal"'+(button.disabled?' disabled="disabled"':'')+'>'+button.caption+'</button>');
				break;
			
				case 'function':
					var b = new $('<button>');
					$(b).attr('type','button').addClass('btn');
					if (button.className) {
						$(b).addClass(button.className);
					}
					else {
						$(b).addClass('btn-primary');
					}
					if (button.left) {
						$(b).addClass('pull-left');
					}
					$(b).text(button.caption);
					if (button.id) {
						$(b).attr('data-button-id', button.id);
					}
					if (button.hidden!=undefined && button.hidden) {
						$(b).css('display','none');
					}
					if (button.disabled){
						$(b).attr('disabled','disabled');
					}
					var action = button.action;
					$(b).click(function(){
						if (button.beforeSave()) {
							action();
							button.afterSave();
						}
					});
					self.footer.append(b);
				break;
				
				case 'submit':
				default:
					var b = new $('<button>');
					$(b).attr('type','button').addClass('btn');
					if (button.className) {
						$(b).addClass(button.className);
					}
					else {
						$(b).addClass('btn-primary');
					}
					if (button.left) {
						$(b).addClass('pull-left');
					}
					$(b).text(button.caption);
					if (button.loading!==false) {
						$(b).attr('data-loading-text', button.loading);
					}
					if (button.id) {
						$(b).attr('data-button-id', button.id);
					}
					if (button.hidden!=undefined && button.hidden) {
						$(b).css('display','none');
					}
					if (button.disabled){
						$(b).attr('disabled','disabled');
					}
					$(b).click(function(){
						$('.errorMessage', self.body).hide();
						$('.errorSummary', self.body).hide();
						if (button.beforeSave()) {
							var btn = $(this);
							if (button.loading!==false) {
							    btn.button('loading');
							}
							$.ajax({
								data: $('form', self.body ).serialize(),
								dataType:'json',
								type:'POST',
								url: button.url,
								success: function(h) {
									switch(h.status) {
										//все хорошо, все сделано, больше ничего не надо, закрываем окно
										case 'success' : button.afterSave(h);  self.close(); break;
										//форма не прошла валидацию, она пришла в поле html
										case 'validate': 
											self.body.html(h.html); 
											button.afterValidate(h);
										break;
										//обновим страницу
										case 'refresh': location.reload(); break;
										//перейдем по ссылке в href
										case 'redirect': location.href = h.href; break;
										//обновим страницу, код в html, что обновляем в target ответа, или в target buttona
										case 'update': $(button.target?button.target:h.target).html(h.html); button.afterSave(h); self.close(); break;
									}
								}
							}).always(function () {
								btn.button('reset');
						    });
						}
					});
					self.footer.append(b);
				break;
			}
			this.items[elem.id] = elem;
		};
		
		this.get = function(id) {
			return $('button[data-button-id="'+id+'"]');
		}
		
		this.remove = function(id) {
			var button = $('button[data-button-id="'+id+'"]');
			if (button) {
				delete this.items[id];
				button.remove();
			}
		};
		this.hide = function(id){
			var button = $('button[data-button-id="'+id+'"]');
			if (button) {
				button.css('display', 'none');
				this.items[id].hidden = true;
			}
		};
		this.show = function(id){
			var button = $('button[data-button-id="'+id+'"]');
			if (button) {
				button.css('display', 'inline-block');
				this.items[id].hidden = false;
			}
		};
		this.disable = function(id){
			var button = $('button[data-button-id="'+id+'"]');
			if (button) {
				button.attr('disabled', 'disabled');
				this.items[id].disabled = true;
			}
		}
		this.enable = function(id){
			var button = $('button[data-button-id="'+id+'"]');
			if (button) {
				button.attr('disabled', false);
				this.items[id].disabled = false;
			}
		}
		
		self.footer.html('');
		for (i in buttons) {
			this.add(buttons[i]);
		}
	};
	
	this.form = function(options) {
		var settings = {
			title : '',
			url   : '',
			buttons    : {},
			afterLoad  : function() {},
			onClose : function() {}
		};
		
		if (options) {
			$.extend(settings, options);
		}
		
		this.body.load(
			settings.url, function(){
				self.title.text(settings.title);
				self.buttons = new buttons(settings.buttons);
				settings.afterLoad(self);
				self.onClose = function() { settings.onClose()};
				self.open();
				$('form', self.body).submit( function(){
					return false;
				});
			}
		);
	}
	
	this.message = function(options){
		var settings = {
			title: '',
			buttons: {},
			afterLoad  : function() {},
			onClose : function() {},
			html: ''
		};
		
		if (options) {
			$.extend(settings, options);
		}
		
		self.body.html(settings.html);
		self.title.text(settings.title);
		self.buttons = new buttons(settings.buttons);
		settings.afterLoad();
		self.onClose = function() { settings.onClose()};
		self.open();
		$('form', self.body).submit( function(){
			return false;
		});
	}
}