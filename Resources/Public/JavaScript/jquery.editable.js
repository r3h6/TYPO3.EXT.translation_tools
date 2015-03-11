(function ($){
	var Editable = function (el, options){
		this.el = el;
		this.$el = $(el);
		this.options = $.extend(true, {}, $.fn.editable.defaultOptions, options);

		//$('input[type="submit"]')
		// console.log('new Editable');
		// console.log(this);

		// if (this.options.selector){
			this.$el.on('click', this.options.selector, this.create);
		// } else {
		// 	this.$el.on('click', this.create);
		// }
	}

	Editable.prototype.create = function (){
		// var w = this.$el.width();
		// var h = this.$el.height();

		// this.value = this.$el.text();
		var $el = $(this);
		var content = $el.text();

		var $editor = $('<textarea />').val(content).css({width: '100%', height: '100%'});
		$el.html($editor);


		//this.$el.trigger('submit.editable');
	}

	Editable.prototype.destroy = function (request){

		//this.$el.trigger('submit.editable');
	}



	Editable.VERSION  = '1.0.0';

	var Plugin = function (options) {
		return this.each(function () {
			var $this = $(this);
			var data  = $this.data('editable');

			if (!data) {
				$this.data('editable', (data = new Editable(this, options)));
			}
			if (typeof options == 'string'){
				data[options]();
			}
		});
	}
	Plugin.defaultOptions = {
		selector: '.editable'
	};

	$.fn.editable             = Plugin
	$.fn.editable.Constructor = Editable

	// $(document).ready(function() {
	// 	$('form[data-plugin~="editable"]').editable();
	// });

}(jQuery));