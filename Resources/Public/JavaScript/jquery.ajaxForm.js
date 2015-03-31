(function ($){
	var AjaxForm = function (el, options){
		this.el = el;
		this.$el = $(el);
		this.options = $.extend(true, {}, $.fn.ajaxForm.defaultOptions, options);

		if (this.$el.data('target')){
			this.options.target = this.$el.data('target');
		}

		this.$el.on('submit', {ajaxForm: this}, function(event){
			event.preventDefault();
			console.log(event);
			$(this).ajaxForm('submit');
		});

		this.$buttons = $('input[data-loading-text]', this.$el).on('click', function (event){
			var $el = $(this);
			$el.data('ajaxForm.dataLoadingText', $el.val());
			$el.val($el.attr('data-loading-text'));
		});

		//$('input[type="submit"]')
		console.log('new AjaxForm');
		console.log(this);
	}

	AjaxForm.prototype.submit = function (request){
		console.log('submit');

		var $loader = $('<div class="ajax-loader" />');


		var request = {
			url: this.$el.attr('data-action'),
			type: this.$el.attr('method'),
			dataType: this.options.format || 'html',
			data: this.$el.serialize(),
			context: this,
			complete: function(xhr, textStatus){
				this.unfreeze();
				this.$buttons.each(function (){
					var $el = $(this);
					$el.val($el.data('ajaxForm.dataLoadingText'));
				});
				this.$el.removeClass('loading');
				this.$el.trigger('complete.ajaxForm');
				$loader.remove();
				$(this.options.target).removeClass('loading');
			},
			success: function(data, textStatus, xhr) {
				console.log('success');
				$(this.options.target).html(data);
				this.$el.trigger('success.ajaxForm');
			},
			error: function(xhr, textStatus, errorThrown) {
				console.log('error');
				console.log(errorThrown);
				$(this.options.target).html(xhr.responseText);
				this.$el.trigger('error.ajaxForm');
			}
		};

		if (this.options.freeze){
			this.freeze();
		}

		$(this.options.target).addClass('loading').append($loader);

		this.$el.addClass('loading');
		this.xhr = jQuery.ajax(request);

		//this.$el.trigger('submit.ajaxForm');
	}

	AjaxForm.prototype.freeze = function (){
		$(this.options.inputSelector, this.$el).prop('disabled', true);
	}

	AjaxForm.prototype.unfreeze = function (){
		$(this.options.inputSelector, this.$el).prop('disabled', false);
	}


	AjaxForm.VERSION  = '1.0.0';

	var Plugin = function (options) {
		return this.each(function () {
			var $this = $(this);
			var data  = $this.data('ajaxForm');

			if (!data) {
				$this.data('ajaxForm', (data = new AjaxForm(this, options)));
			}
			if (typeof options == 'string'){
				data[options]();
			}
		});
	}
	Plugin.defaultOptions = {
		format: 'html',
		inputSelector: 'input, textarea, select',
		freeze: true
	};

	$.fn.ajaxForm             = Plugin
	$.fn.ajaxForm.Constructor = AjaxForm

	$(document).ready(function() {
		$('form[data-plugin~="ajaxForm"]').ajaxForm();
	});

}(jQuery));