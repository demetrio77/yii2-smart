;(function($) {
		
	$.fn.SuperBox = function(options) {
		
		var superbox      = $('<div class="superbox-show"></div>'),
			superboximg   = $('<img src="" class="superbox-current-img"><div id="imgInfoBox" class="superbox-imageinfo inline-block"> <h1>Image Title</h1><span><p><em>http://imagelink.com/thisimage.jpg</em></p><p class="superbox-img-description">Image description</p><p><a href="javascript:void(0);" class="btn btn-primary btn-sm img-edit">Изменить</a> <a href="javascript:void(0);" class="btn btn-danger btn-sm img-del">Удалить</a></p></span> </div><div class="mes"></div>'),
			superboxclose = $('<div class="superbox-close txt-color-white"><i class="fa fa-times fa-lg"></i></div>');
		
		superbox.append(superboximg).append(superboxclose);
		
		var imgInfoBox = $('.superbox-imageinfo');
		
		$(this).sortable({
			items:'.superbox-list',
			placeholder: 'superbox-list',
			update: function( event, ui ) {
				if ( options.onUpdate !==undefined ) {
					var a = $(this).sortable('toArray',{'attribute':'data-id'});
					var imageId = ui.item.attr('data-id');
					var ordinal = a.indexOf( imageId );
					
					options.onUpdate(imageId, ordinal);
				}				
			}
		});
		
		$(this).on('click', '.superbox-close', function() {
			$('.superbox-list').removeClass('active');
			$('.superbox-current-img').animate({opacity: 0}, 200, function() {
				$('.superbox-show').slideUp();
			});
		});
		
		return this.each(function() {
			
			$('.superbox-list').click(function() {

				$this = $(this);
		
				var currentimg = $this.find('.superbox-img'),
					imgData = currentimg.data('img'),
					imgDescription = currentimg.attr('alt') || "",
					imgLink = imgData,
					imgTitle = currentimg.attr('title') || "";
					
				superboximg.attr('src', imgData);
				
				$('.superbox-list').removeClass('active');
				$this.addClass('active');
								
				superboximg.find('em').text(imgLink);
				superboximg.find('>:first-child').text(imgTitle);
				superboximg.find('.superbox-img-description').text(imgDescription);
				
				if($('.superbox-current-img').css('opacity') == 0) {
					$('.superbox-current-img').animate({opacity: 1});
				}
				
				if ($(this).next().hasClass('superbox-show')) {
					$('.superbox-list').removeClass('active');
					superbox.toggle();
				} else {
					superbox.insertAfter(this).css('display', 'block');
					$this.addClass('active');
				}
				
				$('html, body').animate({
					scrollTop:superbox.position().top - currentimg.width()
				}, 'medium');
			
			});			
		});
	};
})(jQuery);