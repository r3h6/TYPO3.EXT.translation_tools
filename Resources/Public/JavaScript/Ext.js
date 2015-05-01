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

		$('[name$="[id]"]', $form).val(id);
		$('[name$="[file]"]', $form).val(file);
		$('[name$="[targetLanguage]"]', $form).val(targetLanguage);
		$('[name$="[source]"]', $form).val(source);
		$('[name$="[target]"]', $form).val(target);

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

	$('.results').editable()
	.on('init.editable', '.editable', function (event, $editor){
		var source = $(event.target).data('source');
		$editor.after('<div class="translation-source">' + source + '</div>');
	})
	.on('change.editable', '.editable', function (event, value){
		if (value){
			updateTranslation(event.target, value);
		}
	});

	// $('form[data-plugin~="ajaxForm"]').on('complete.ajaxForm', function (event){
	// 	// $('.translations')
	// 	// 	.editable()
	// 	//
	// });

	$('.btn-csv').on('click', function (event){
		event.preventDefault();
		var data = $('form[name="demand"]').serialize();
		console.log(data);

		var uri = $(this).attr('href') + '&' + data;

		window.location = uri;
	});

});

