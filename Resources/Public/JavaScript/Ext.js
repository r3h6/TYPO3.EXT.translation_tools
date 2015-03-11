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

		var id = $el.closest('[data-id]').data('id');
		var file = $el.closest('[data-file]').data('file');
		var targetLanguage = $el.data('target-language');
		var source = $el.data('source');

		var $form = $('form[name="translation"]');

		$('[data-property="id"]', $form).val(id);
		$('[data-property="file"]', $form).val(file);
		$('[data-property="target-language"]', $form).val(targetLanguage);
		$('[data-property="source"]', $form).val(source);
		$('[data-property="target"]', $form).val(value);

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

});

