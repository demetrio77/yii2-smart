;(function($) {
	$.fn.dateDropDown = function(options) {
		var today = new Date();
		
		//настройки по умолчанию
		var settings = {};
		settings.maxYear = today.getFullYear();
		settings.minYear = settings.maxYear  - 10;
		settings.defaultDate = '0000-00-00';
		
		//пришедшие настройки
		if (options) {
			$.extend(settings, options);
		}
		
		return this.each( function()
		{
			//объект
			var $this = $(this);
			
			//input
			$this.input = $('input:hidden', $this);
			
			//Инициализация значения даты
			$this.value = '';
			if ($this.input.val()) {
				$this.value = $this.input.val() ;
			}
			else {
				$this.value = settings.defaultDate;
				$this.input.val($this.value);
			}
			
			//переменные для хранения даты
			$this.year = '';
			$this.month = '';
			$this.day = '';
			
			//селекты
			$this.daySelect = null;
			$this.monthSelect = null;
			$this.yearSelect = null;
			
			//установка значения по значениям селектов
			$this.setValue = function() {
				$this.value =   ($this.year      ? $this.year  : '0000') + '-' +
								($this.month <10 ? '0' :'') + $this.month + '-' +
								($this.day   <10 ? '0' :'') + $this.day;
				if ($this.value === '0000-00-00' ) $this.value = '';
				$this.input.val($this.value);
			};
			
			$this.init = function() {
				//exploding value...
				var x = $this.value.split('-');
				//собираем значения переменных даты
				$this.year = parseInt(x[0]);
				$this.month = parseInt(x[1]);
				$this.day = parseInt(x[2]);
				
				//creating selects...
				$this.daySelect = new $('<select>');
				$this.monthSelect = new $('<select>').append('<option value=0></option><option value=1>Январь</option><option value=2>Февраль</option><option value=3>Март</option><option value=4>Апрель</option><option value=5>Май</option><option value=6>Июнь</option><option value=7>Июль</option><option value=8>Август</option><option value=9>Сентябрь</option><option value=10>Октябрь</option><option value=11>Ноябрь</option><option value=12>Декабрь</option>');
				$this.yearSelect = new $('<select>');
				
				//adding styles
				$this.daySelect.addClass('form-control');
				$this.monthSelect.addClass('form-control');
				$this.yearSelect.addClass('form-control');				
				
				//filling and initializing selects
				$this.yearSelect.append('<option value="0"'+($this.year==0?' selected':'')+'></option>');
				for (var i=settings.minYear; i<=settings.maxYear; i++) {
					$this.yearSelect.append('<option value='+i+(i==$this.year?' selected':'')+'>'+i+'</option>');
				}
				$this.daySelect.fill = function() {
					var daysInMonth = ($this.year && $this.month) ? (new Date($this.year,$this.month,0).getDate()) : 31;
					$this.daySelect.html('');
					for (var i=0; i<=daysInMonth; i++) {
						$this.daySelect.append('<option value="' + i +'"'+(i==$this.day?' selected':'')+'>' + (i?i:'') +'</option>');
					}
				};
				$this.daySelect.fill();
				$('option[value="' + $this.month+'"]', $this.monthSelect).prop('selected', true);
								
				//onchange selects
				$this.daySelect.change( function() {
					$this.day = parseInt( $('option:selected', $this.daySelect).val() );
					$this.setValue();
				});
				
				$this.monthSelect.change( function(){
					$this.month = parseInt( $('option:selected', $this.monthSelect).val() );
					$this.daySelect.fill();
					$this.day = $('option:selected', $this.daySelect).val();
					$this.setValue();
				});
				
				$this.yearSelect.change( function(){
					$this.year = parseInt( $('option:selected', $this.yearSelect).val() );
					$this.daySelect.fill();
					$this.day = $('option:selected', $this.daySelect).val();
					$this.setValue();
				});
				//appending selects to div
				$this.addClass('input-group')
					 .append($this.daySelect)
					 .append('<div class="input-group-addon">-</div>')
					 .append($this.monthSelect) 
					 .append('<div class="input-group-addon">-</div>')
					 .append($this.yearSelect) ;
			}			
			
			$this.init();
		});
	}
})(jQuery);