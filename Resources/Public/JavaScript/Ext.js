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