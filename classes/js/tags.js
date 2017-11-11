(function($) {

	cmb_tags = {

		init: function(cmb2TypeClass) {

			var t = this,
				ajaxtag = $(cmb2TypeClass+' div.ajaxtag, '+cmb2TypeClass+'-sortable div.ajaxtag'),
				start_pos;

			$(cmb2TypeClass+' .hide-if-no-js, '+cmb2TypeClass+'-sortable .hide-if-no-js').removeClass('hide-if-no-js');
			$(cmb2TypeClass+' textarea, '+cmb2TypeClass+'-sortable textarea').hide();

			$(cmb2TypeClass+"-sortable .tagchecklist").sortable({
				start: function(event, ui) {
					start_pos = ui.item.index();
				},
				stop: function(event, ui) {
					cmb_tags.updateTags($(this).closest(cmb2TypeClass+'-sortable'), start_pos, ui.item.index());
				}
			});
			$(cmb2TypeClass+"-sortable .tagchecklist").disableSelection();

			$(cmb2TypeClass+', '+cmb2TypeClass+'-sortable').each(function() {
				cmb_tags.quickClicks(this);
			});

			$('input.button', ajaxtag).click(function() {
				t.flushTags($(this).closest(cmb2TypeClass+', '+cmb2TypeClass+'-sortable'));
			});

			$('input.new', ajaxtag).keyup(function(e) {
				if (13 == e.which) {
					cmb_tags.flushTags($(this).closest(cmb2TypeClass+', '+cmb2TypeClass+'-sortable'));
					return false;
				}
			}).keypress(function(e) {
				if (13 == e.which) {
					e.preventDefault();
					return false;
				}
			});

			// save tags on post save/publish
			/*$('#post').submit(function(){
			$('.cmb-type-tags').each( function() {
	        	cmb_tags.flushTags(this, false, 1);
			});
		});*/

		},

		clean: function(tags) {
			return tags.replace(/\s*,\s*/g, ';').replace(/,+/g, ';').replace(/[,\s]+$/, '').replace(/^[,\s]+/, '');
		},

		parseTags: function(el) {
			var id = el.id,
				num = id.split('-check-num-')[1],
				taxbox = $(el).closest(cmb2TypeClass+', '+cmb2TypeClass+'-sortable'),
				thetags = taxbox.find('textarea'),
				current_tags = thetags.val().split(';'),
				new_tags = [];
			delete current_tags[num];

			$.each(current_tags, function(key, val) {
				val = $.trim(val);
				if (val) {
					new_tags.push(val);
				}
			});

			thetags.val(this.clean(new_tags.join(';')));

			this.quickClicks(taxbox);
			return false;
		},

		updateTags: function(el, start_pos, stop_pos) {
			var thetags = $('textarea', el),
				current_tags, sorted_tags;

			if (!thetags.length)
				return;

			current_tags = thetags.val().split(';');

			current_tags.move(start_pos, stop_pos);

			for (var i = 0; i < current_tags.length; i++) {
				if (i == 0) sorted_tags = '';
				else sorted_tags += ';';
				sorted_tags += current_tags[i];
			}

			thetags.val(sorted_tags);
		},

		quickClicks: function(el) {
			var thetags = $('textarea', el),
				tagchecklist = $('.tagchecklist', el),
				id = $(el).attr('id'),
				current_tags, disabled;

			if (!thetags.length)
				return;

			disabled = thetags.prop('disabled');

			current_tags = thetags.val().split(';');
			tagchecklist.empty();
			$.each(current_tags, function(key, val) {

				var span, xbutton;

				val = $.trim(val);

				if (!val)
					return;

				// Create a new span, and ensure the text is properly escaped.
				span = $('<span />').text(val);

				// If tags editing isn't disabled, create the X button.
				if (!disabled) {
					xbutton = $('<a id="' + id + '-check-num-' + key + '" class="ntdelbutton">X</a>');
					xbutton.click(function() { cmb_tags.parseTags(this); });
					span.prepend('&nbsp;').prepend(xbutton);
				}

				// Append the span to the tag list.
				tagchecklist.append(span);
			});
		},

		//called on add tag, called on save
		flushTags: function(el, a, f) {
			a = a || false;
			var text, tags = $('textarea', el),
				newtag = $('input.new', el),
				newtags;

			text = a ? $(a).text() : newtag.val();

			tagsval = tags.val();
			newtags = tagsval ? tagsval + ';' + text : text;

			newtags = this.clean(newtags);

			newtags = array_unique_noempty(newtags.split(';')).join(';');

			tags.val(newtags);

			this.quickClicks(el);

			if (!a)
				newtag.val('');
			if ('undefined' == typeof(f))
				newtag.focus();

			return false;
		}

	}

	Array.prototype.move = function(old_index, new_index) {
		if (new_index >= this.length) {
			var k = new_index - this.length;
			while ((k--) + 1) {
				this.push(undefined);
			}
		}
		this.splice(new_index, 0, this.splice(old_index, 1)[0]);
	};
})(jQuery);

jQuery(document).ready(function($) {

	if(jQuery('#wyz-tag-select').length||jQuery('#wyz-cat-select').length)
		cmb_tags.init('.cmb-type-tags');

	//business tags select
	if (jQuery('#wyz-tag-select').length) {

		jQuery('#wyz-tag-select').selectize({
			create: true,
			plugins: ['remove_button']
		});
	}

	//business tags select
	if (jQuery('#wyz-cat-select').length) {

		cmb_tags.init('.cmb-type-cats');

		jQuery('#wyz-cat-select').selectize({
			create: false,
			plugins: ['remove_button'],
			render: {
				option: function(data, escape) {
					return '<div class="option' + ( undefined != data.class? (' '+data.class) : '') + '" data-selectable="" data-value="'+escape(data.value)+'" data-id="'+escape(data.id)+'">'+escape(data.text)+'</div>';
				}
			},
			onItemAdd: function(value, item) {
				if(categoriesInfo.catIconExits) {
					var id = (this.options[value]).id;
					if(undefined!=catIconOptions && catIconOptions[id] != undefined)
						catIconCount[id] = parseInt( catIconCount[id] ) + 1;
					addOption(id);
				}
			},
			onItemRemove:function(value){
				var id = (this.options[value]).id;
				if(idExists(id)){
					catIconCount[id] = parseInt( catIconCount[id] )-1;
					if(catIconCount[id]==0)
						removeOption(id);
				}
			}
		});
	

		jQuery('#wyz-cat-icon-select').selectator({
				useSearch: false,
				useDimmer: false
			});

		function addOption(id) {
			if(categoriesInfo.catIconExits) {
				if(undefined!=catIconOptions && catIconOptions[id] != undefined&&!idExists(id)){
					jQuery('#wyz-cat-icon-select').append(catIconOptions[id]);
					jQuery('#wyz-cat-icon-select').selectator('destroy');
					jQuery('#wyz-cat-icon-select').selectator({
						useSearch: false,
						useDimmer: false
					});
				}
			}
		}

		function removeOption(id){
			jQuery("#wyz-cat-icon-select option[value='"+id+"']").remove();
			jQuery('#wyz-cat-icon-select').selectator('destroy');
			jQuery('#wyz-cat-icon-select').selectator({
				useSearch: false,
				useDimmer: false
			});
		}

		function idExists(id){
			var exists = false;
			jQuery('#wyz-cat-icon-select option').each(function(){
				if (this.value == id) {
					exists = true;
					return false;
				}
			});
			return exists;
		}
	}

});
