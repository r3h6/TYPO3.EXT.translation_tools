(function ($){
	/**
	 * @see https://developer.mozilla.org/en/docs/Web/API/HTMLTextAreaElement#Autogrowing_textarea_example
	 */
	function autoGrow (oField) {
		if (oField.scrollHeight > oField.clientHeight) {
			oField.style.height = oField.scrollHeight + "px";
		}
	}

	$.fn.editable = function (){
		return $(this).on('click', '.editable', function (event){
			var $el = $(this);
			if ($el.hasClass('edit')) return null;
			$el.addClass('edit');

			var content = $el.html();

			var $editor = $('<textarea class="editable-editor" />')
				.val(content)
				.css({width: '100%', height: $el.innerHeight() - 16})
				.on('blur', function (event){
					var value = $(this).val();
					$el.removeClass('edit').html(value);
					if (value != content){
						$el.trigger('change.editable', [value]);
					}
				})
				.on('focus keyup', function (event){
					autoGrow(this);
				})
				.on('keyup', function (event){
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

			$el.trigger('init.editable', [$editor]);
		});
	};
}(jQuery));