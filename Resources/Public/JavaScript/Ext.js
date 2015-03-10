(function ($){
	$.fn.editable = function (){
		$(this).on('click', '.editable', function (event){
			var $el = $(this);
			if ($el.hasClass('edit')) return null;

			var targetLanguage = $el.data('target-language');
			var id = $el.closest('[data-id]').data('id');
			var file = $el.closest('[data-file]').data('file');
			var source = $el.data('source');
			var text = $el.text();
			var $form = $($('#EditableTemplate').html());
			// console.log($form);
			$el.addClass('edit').html($form);

			//var $form = $form.find('form');
			var $target = $form.find('[data-property="target"]').val(text).focus();
			$form.find('[data-property="source"]').val(source);
			$form.find('[data-property="id"]').val(id);
			$form.find('[data-property="file"]').val(file);
			$form.find('[data-property="target-language"]').val(targetLanguage);

			$target.on('blur', function (){
				var value = $(this).val();
				if (value != text){
					$form.trigger('submit');
				}
				$el.removeClass('edit').html(value);
			});

			$form.on('submit', function (event){
				event.preventDefault();
				console.log(event);
				TYPO3.Flashmessage.display(TYPO3.Severity.ok, 'test', 'my message', 5);
			});
		});

	};
}(jQuery));



(function ($){


	$(document).ready(function() {

		// $('.translations').on('click', '.editable', function (event){

		// 	$(this).editable();
		// });

		$('form[data-plugin~="ajaxForm"]').on('complete.ajaxForm', function (event){
			console.log(event);
			$('.translations').editable();
		});

	});
}(jQuery));

