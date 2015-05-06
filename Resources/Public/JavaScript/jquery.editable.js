(function ($){

	/**
	 * @see https://developer.mozilla.org/en/docs/Web/API/HTMLTextAreaElement#Autogrowing_textarea_example
	 */
	function autoGrow (oField) {
		if (oField.scrollHeight > oField.clientHeight) {
			oField.style.height = oField.scrollHeight + "px";
		}
	}

	var Editable = function (el, options){
		this.el = el;
		this.$el = $(el);
		this.options = $.extend(true, {}, $.fn.ajaxForm.defaultOptions, options);

		this.$selectedCell = null;
		this.disableArrows = false;

		// var self = this;
		this.$el.on('dblclick', '.editable', $.proxy(this.dblclickHandler, this));
		this.$el.on('click', '.editable', $.proxy(this.clickHandler, this));
		this.$el.on('keydown', '.editable', $.proxy(this.keypressHandler, this));
	}

	Editable.VERSION  = '1.0.0';
	Editable.KEY_LEFT = 37;
	Editable.KEY_UP = 38;
	Editable.KEY_RIGHT = 39;
	Editable.KEY_DOWN = 40;
	Editable.KEY_TAB = 9;
	Editable.KEY_ENTER = 13;


	Editable.prototype.moveLeft = function (){
		return this.editCell(this.$selectedCell.prev('.editable'));
	}

	Editable.prototype.moveRight = function (){
		return this.editCell(this.$selectedCell.next('.editable'));
	}

	Editable.prototype.moveUp = function (){
		var index = this.$selectedCell.parent().children().index(this.$selectedCell);
		return this.editCell(this.$selectedCell.closest('tr').prev().children(':eq(' + index + ')'));
	}

	Editable.prototype.moveDown = function (){
		var index = this.$selectedCell.parent().children().index(this.$selectedCell);
		return this.editCell(this.$selectedCell.closest('tr').next().children(':eq(' + index + ')'));
	}

	Editable.prototype.next = function (){
		if (this.moveRight()){
			return true;
		}
		return this.editCell(this.$selectedCell.closest('tr').next().children().filter('.editable').first());
	}

	Editable.prototype.editCell = function ($cell){
		if (!$cell.length || !$cell.is('.editable') || $cell.hasClass('edit')) return false;

		this.$selectedCell = $cell;
		var self = this;


		$cell.addClass('edit');

		var content = $cell.html();

		var $editor = $('<textarea class="editable-editor" />')
			.val(content)
			.css({width: '100%', height: $cell.innerHeight() - 16})
			.on('blur', function (event){
				self.disableArrows = false;
				var value = $(this).val();
				$cell.removeClass('edit').html(value);
				if (value != content){
					$cell.trigger('change.editable', [value]);
				}
			})
			.on('focus keyup', function (event){
				autoGrow(this);
			});

		$cell.html($editor);
		$editor.focus();

		this.$selectedCell.trigger('init.editable', [$editor]);
		return true;
	}

	Editable.prototype.clickHandler = function (event){
		this.editCell($(event.target));
	}

	Editable.prototype.dblclickHandler = function (event){
		this.disableArrows = true;
		this.editCell($(event.target));
	}

	Editable.prototype.keypressHandler = function (event){

		switch (event.which){
			case Editable.KEY_ENTER:
				event.preventDefault();
				this.moveDown();
				break;
			case Editable.KEY_TAB:
				event.preventDefault();
				this.next();
				break;
			case Editable.KEY_DOWN:
				if (!this.disableArrows){
					this.moveDown();
				}
				break;
			case Editable.KEY_UP:
				if (!this.disableArrows){
					this.moveUp();
				}
				break;
			case Editable.KEY_RIGHT:
				if (!this.disableArrows){
					this.moveRight();
				}
				break;
			case Editable.KEY_LEFT:
				if (!this.disableArrows){
					this.moveLeft();
				}
				break;
		}
	}

	var Plugin = function (options) {
		return this.each(function () {
			var $this = $(this);

			var data  = $this.data('editable');

			if (!data) {
				$this.data('editable', (data = new Editable(this, options)));
			}
			if (typeof options == 'string'){
				data[options]();
			}

		});
	}
	Plugin.defaultOptions = {
	};

	$.fn.editable             = Plugin
	$.fn.editable.Constructor = Editable

	$(document).ready(function() {
		$('form[data-editable]').editable();
	});
}(jQuery));