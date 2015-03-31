(function ($){
	$.fn.editable = function (){
		return $(this).on('click', '.editable', function (event){
			var $el = $(this);
			if ($el.hasClass('edit')) return null;
			$el.addClass('edit');

			var content = $el.html();

			var $editor = $('<textarea />')
				.val(content)
				.css({width: '100%', height: '100%'})
				.on('blur', function (event){
					var value = $(this).val();
					if (value != content){
						$el.trigger('change.editable', [value]);
					}
					$el.removeClass('edit').html(value);
				})
				.on('keydown', function (event){
					var index = $el.parent().children().index($el);
					switch(event.which){
						case 37: // left
							$el.prev().trigger('click');
							break;

						case 38: // up
							$el.closest('tr').prev().children(':eq(' + index + ')').trigger('click');
							break;

						case 39: // right
							$el.next().trigger('click');
							break;

						case 40: // down
							$el.closest('tr').next().children(':eq(' + index + ')').trigger('click');
							break;
					}
				});

			$el.html($editor);
			$editor.focus();
		});
	};
}(jQuery));





jQuery(document).ready(function($) {

	function updateTranslation (el, value){
		var $el = $(el);
		var $form = $('form[name="translation"]');


		var id = $el.data('id');
		var file = $el.data('file');
		var targetLanguage = $el.data('target-language');
		var source = $el.data('source');
		var target = value;

		if (!targetLanguage){
			source = value;
			target = '';
		}

		$('[name*="[id]"]', $form).val(id);
		$('[name*="[file]"]', $form).val(file);
		$('[name*="[targetLanguage]"]', $form).val(targetLanguage);
		$('[name*="[source]"]', $form).val(source);
		$('[name*="[target]"]', $form).val(target);

		$.ajax({
			url: $form.attr('action'),
			type: $form.attr('method'),
			dataType: 'html',
			data: $form.serialize(),
		})
		.done(function(data, textStatus, jqXHR) {
			TYPO3.Flashmessage.display(TYPO3.Severity.ok, 'Update', data, 5);
		})
		.fail(function(jqXHR, textStatus, errorThrown) {
			TYPO3.Flashmessage.display(TYPO3.Severity.error, errorThrown, jqXHR.responseText, 5);
		});

	}

	$('form[data-plugin~="ajaxForm"]').on('complete.ajaxForm', function (event){
		$('.translations')
			.editable()
			.on('change.editable', '.editable', function (event, value){
				if (value){
					updateTranslation(event.target, value);
				}
			});
	});

	$('.btn-csv').on('click', function (event){
		event.preventDefault();
		var data = $('form[name="demand"]').serialize();
		console.log(data);

		var uri = $(this).attr('href') + '&' + data;

		window.location = uri;
	});

});

