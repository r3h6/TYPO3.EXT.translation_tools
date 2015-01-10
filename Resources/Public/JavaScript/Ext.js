(function ($){
	$.fn.editable = function (){
		var $el = $(this);
		var text = $el.text();
		$el.html(
			'<div class="form">' +
			'<div class="form-group">' +
			'<textarea class="form-control">' + text + '</textarea>' +
			'</div>' +
			'<input class="btn btn-default" type="submit" value="ok">' +
			'<input class="btn btn-default" type="submit" value="nok">' +
			'</div>'
		);
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