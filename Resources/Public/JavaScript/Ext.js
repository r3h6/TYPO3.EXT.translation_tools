(function ($){
	$.fn.editable = function (){
		var $el = $(this);
		if ($el.hasClass('edit')) return null;

		var targetLanguage = $el.attr('data-targetLanguage');
		var id = $el.closest('[data-id]').attr('data-id');
		var file = $el.closest('[data-file]').attr('data-file');

		var text = $el.text();
		var $tpl = $($('#EditableTemplate').html());
		// console.log($tpl);
		$el.addClass('edit').html($tpl);

		$tpl.find('.js-target').val(text).focus();
		$tpl.find('.js-id').val(id);
		$tpl.find('.js-file').val(file);
		$tpl.find('.js-target-language').val(targetLanguage);


	};
}(jQuery));



(function ($){
	$(document).ready(function() {

		$('.translations').on('click', '.editable', function (event){
			console.log(event);
			$(this).editable();
		});


		var visualSearch = VS.init({
			container : $('.visualsearch'),
			query     : '',
			callbacks : {
				search       : function(query, searchCollection) {
					console.log('search');
					console.log(query);
					console.log(searchCollection);
				},
				facetMatches : function(callback) {
					console.log('facetMatches');
					callback(['file', 'label', 'language', 'id']);

				},
				valueMatches : function(facet, searchTerm, callback) {
					console.log('valueMatches');
					console.log(facet);
					console.log(searchTerm);
					console.log(callback);
					switch (facet){
						case 'file':
							callback(files);
							break;
					}
				}
			}
		});
	});
}(jQuery));