jQuery(document).ready(function($) {

	function updateTranslation (el, value){
		var $el = $(el);
		var $form = $('form[name="translation"]');
		var id = $el.data('id');
		var sourceFile = $el.data('source-file');
		var targetLanguage = $el.data('target-language');
		var source = $el.data('source');
		var target = value;

		$('[name$="[id]"]', $form).val(id);
		$('[name$="[sourceFile]"]', $form).val(sourceFile);
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
			TYPO3.Flashmessage.display(TYPO3.Severity.ok, '', data, 15);
		})
		.fail(function(jqXHR, textStatus, errorThrown) {
			TYPO3.Flashmessage.display(TYPO3.Severity.error, errorThrown, jqXHR.responseText, 5);
		});
	}

	$('.results').editable()
	.on('init.editable', '.editable', function (event, $editor){
		//var source = $(event.target).data('source');
		//$editor.after('<div class="translation-source">' + source + '</div>');
	})
	.on('change.editable', '.editable', function (event, value){
		if (value){
			updateTranslation(event.target, value);
		}
	});

	$('.btn-export-csv').on('click', function (event){
		event.preventDefault();
		var data = $('form[name="demand"]').serialize();
		var uri = $(this).attr('href') + '&' + data;
		window.location = uri;
	});

	var $importForm = $('form[name="import"]');
	var $inputFile = $('input[name$="[file]"]', $importForm);

	$('.btn-import-csv').on('click', function (event){
		event.preventDefault();
		$inputFile.trigger('click');
	});

	$inputFile.on('change', function (){
		if (this.value != ''){
			$importForm.trigger('submit');
		}
	});

});

