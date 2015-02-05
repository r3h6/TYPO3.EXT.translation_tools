(function ($){
	$.fn.editable = function (){
		var $el = $(this);
		if ($el.hasClass('edit')) return null;

		var targetLanguage = $el.data('target-language');
		var id = $el.closest('[data-id]').data('id');
		var file = $el.closest('[data-file]').data('file');
		var source = $el.data('source');
		var text = $el.text();
		var $tpl = $($('#EditableTemplate').html());
		// console.log($tpl);
		$el.addClass('edit').html($tpl);

		$tpl.find('[data-property="target"]').val(text).focus();
		$tpl.find('[data-property="source"]').val(source);
		$tpl.find('[data-property="id"]').val(id);
		$tpl.find('[data-property="file"]').val(file);
		$tpl.find('[data-property="target-language"]').val(targetLanguage);


	};
}(jQuery));



(function ($){
	$(document).ready(function() {

		$('.translations').on('click', '.editable', function (event){

			$(this).editable();
		});

		$('form[data-plugin~="ajaxForm"]').on('complete.ajaxForm', function (event){
			console.log(event);
		}).on('complete', function (){
			console.log("complete");
		});

	});
}(jQuery));

