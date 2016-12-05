function yandexMapPoints( options )
{
	var self = this;
	
	this.options = {
		points : [],
		accordeon: true,
		center: [59.1120, 37.9585],
		zoom : 12,
		onePointZoom : 15,
		slideSpeed : 300
	};
	
	$.extend(this.options, options);
	
	this.collections = [];
	this.maps = [];
	this.map = null;
	this.cnt = [];
	this.cnt[0] = 0;
	
	this.init = function() {
		for (c in this.options.points) {
			var collection = new ymaps.GeoObjectCollection(null, { preset: 'islands#dotIcon', iconColor: '#0095b6', });
			for (p in this.options.points[c]) {
				var point = this.options.points[c][p];
				collection.add(new ymaps.Placemark([point.latitude,point.longitude],{
		            balloonContent: point.balloonContent,
		            hintContent: point.hintContent
		        }));
			}
			this.collections[c] = collection;
			this.cnt[c] = collection.getLength();
			this.cnt[0] += this.cnt[c];
		}
	}
	
	this.initMap = function(point, mapId) 
	{
		var cnt = point=='all' ? this.cnt[0] : this.cnt[point];

		if (cnt==1) {
			for (i in this.options.points) var p = this.options.points[i][0];
			var center = [p.latitude,p.longitude];
			var zoom = this.options.onePointZoom;
		}
		else {
			var center = this.options.center;
			var zoom = this.options.zoom;
		}
		
		var UserMap = new ymaps.Map( mapId.attr('id'), {
			center: center,
		    zoom: zoom,
	    });
		
		if (point=='all') {
			for (c in this.collections) {
				UserMap.geoObjects.add(this.collections[c]);
			}
		}
		else {
			UserMap.geoObjects.add(this.collections[point]);
		}
		
		if (cnt>1) {
			var b = UserMap.geoObjects.getBounds();
			if (Math.abs(b[1][0]-b[0][0])<0.1 && Math.abs(b[1][1]-b[0][1])<0.1) UserMap.setBounds(b);
		}
		
		return UserMap;
	}
	
	this.slideDownMap = function ( point, mapId ) {
		var visible = mapId.css('display')=='block';
		
		if (visible) {
			mapId.slideUp(300);			
		}
		else {
			if (this.options.accordeon) {
				if (this.map && this.map!=mapId) {
					this.map.slideUp(300);
				}
				this.map = mapId;
			}
			
			mapId.slideDown(300, function(){
				if (self.maps[point]==undefined) {
					self.maps[point] = self.initMap(point, mapId);
				}
			});
		}
	}
	
	this.showMap = function(point, mapId)
	{
		ymaps.ready(function(){
			self.initMap(point, mapId);
		});
	}
	
	ymaps.ready(function(){ self.init(); });
}