/*

Create a Parallax effect for DOM elements

Dependencies:

jQuery 1.7++
Modernizer - if you don't like or haven't included Modernizr, you can check for csstransforms yourself. Google it :)


Usage:
var options, parallax;
options = {
	container: '#promo',
	layers: [
	  {
	    selector: 'img.fore',
	    strength: .06
	  }, {
	    selector: 'img.mid',
	    strength: .045
	  }, {
	    selector: 'section.gallery-content > nav',
	    strength: .045
	  }, {
	    selector: 'img.back',
	    strength: .005
	  }
	]
};
parallax = new Parallax(options);

*/

var Parallax = (function($) {

	var Parallax = function(data) {

			this.$container = null;
			this.$layers = $();
			this.canCSS3 = (Modernizr !== null) ? Modernizr.csstransforms : false;

			if (typeof data === 'object') {
				this.$container = data.container ? $(data.container) : null;
				if ( typeof data.layers === 'object' && data.layers.constructor === Array ) {
					for ( var i = 0, l = data.layers.length; i < l; ++i ) {
						var layer = data.layers[i];
						this.$layers = this.$layers.add(this.$container.find(layer['selector']).data('strength', layer['strength']));
					}
				}
			}
			
			if (this.$container.length) this.init();
			return this;
	};

	Parallax.prototype = {
		'init' : function(){
			var self = this,
			pos = this.$container.css('position'),
			w = this.$container.width()/2,
			h = this.$container.height()/2;
			
			if ( pos !== 'relative' && pos !== 'absolute' ){
				this.$container.css('position', 'relative');
			}

			function onMousemove(e) {
				self.$layers.each(function(){
					var $this = $(this),
					x = ~~((w - e.pageX) * $this.data('strength')),
					y = ~~((h - e.pageY) * $this.data('strength'));


					if (self.canCSS3) {
						$this.css('-webkit-transform', 'translate3d(' + x + 'px,' + y + 'px,0)');
						$this.css('-moz-transform', 'translate(' + x + 'px,' + y + 'px)');
						$this.css('-o-transform', 'translate(' + x + 'px,' + y + 'px)');
						$this.css('-ms-transform', 'translate(' + x + 'px,' + y + 'px)');
					} else {
						$this.css({
							'top' :  y + 'px',
							'left' : x + 'px'
						});
					}	
				});
			};
			this.$container.on('mousemove', onMousemove);
		},

		'destroy' : function(){
			this.$container.off('mousemove');
			this.$layers.each(function(){
				$(this).removeData();
			});
		}
	};
	return Parallax;
}(jQuery));




