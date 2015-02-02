(function ($, VS){

	var inputQuery = 'input[type=text], textarea, select';

	function findFacet ($el, $form){
		var label = $('[for="' + $el.attr('name') + '"]', $form).text();
		if (!label){
			label = $el.attr('title');
		}
		return label;
	}

	$.fn.visualSearch = function (){
		return $(this).each(function (){
			var $form = $(this);
			var facetElementMap = {};
			var facetMatches = [];
			var valueMatches = {};

			$(inputQuery, $form).each(function (i, el){
				// var facet = $(el).attr('title');
				var $el = $(el);
				var facet = findFacet($el, $form);
				if (facet){
					facetMatches.push(facet);
					facetElementMap[facet] = el;
				}
			});
			$('select', $form).each(function (i, el){
				var $el = $(el);
				// var facet = $el.attr('title');
				var facet = findFacet($el, $form);
				var options = [];
				$el.find('option').each(function (i, el){
					var $el = $(el);
					options.push({
						value: $el.attr('value'),
						label: $el.text()
					});
				});

				valueMatches[facet] = options;
			});

			$form.after('<div class="visualsearch" />');
			$form.hide();

			var visualSearch = VS.init({
				container : $('.visualsearch'),
				query     : '',
				callbacks : {
					search       : function(query, searchCollection) {
						$(inputQuery, $form).val("");
						var facets = {};
						$(searchCollection.models).each(function (i, model){
							var facet = model.attributes.category;
							var value = model.attributes.value;
							if (!facets[facet]){
								facets[facet] = [];
							}
							facets[facet].push(value);
						});

						for (var facet in facets){
							var values = facets[facet];
							var $el = $(facetElementMap[facet]);
							if ($el.is('select')){
								$el.val(values);
							} else {
								$el.val(values.join(', '));
							}
						}

						$form.submit();
					},
					facetMatches : function(callback) {
						callback(facetMatches);

					},
					valueMatches : function(facet, searchTerm, callback) {
						if (valueMatches[facet]){
							callback(valueMatches[facet]);
						}
					}
				}
			});
		});
	};

	$(document).ready(function() {
		$('form[data-plugin="visualSearch"]').visualSearch();
	});
}(jQuery, VS));