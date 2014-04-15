(function($) {

	var container;

	$(document).ready(function() {

		container = $('#artwork_links_box');

		if(!container.length)
			return false;

		var list = container.find('.link-list');
		var template = container.find('.template');

		list.find('.remove').click(function() {
			$(this).parents('li').remove();
			updateAttrs();
			return false;
		});

		container.find('.new-link').click(function() {

			var item = template.clone().removeClass('template');

			item.find('.remove').click(function() {
				$(this).parents('li').remove();
				updateAttrs();
				return false;
			});

			list.append(item);

			updateAttrs();

			return false;

		});

		function updateAttrs() {
			list.find('li').each(function(i) {
				var id = 'link-' + i;
				$(this).find('#link-title-en').attr('name', 'artwork_links[' + id + '][title][en]');
				$(this).find('#link-title-es').attr('name', 'artwork_links[' + id + '][title][es]');
				$(this).find('#link-title-pt').attr('name', 'artwork_links[' + id + '][title][pt]');
				$(this).find('.link-url').attr('name', 'artwork_links[' + id + '][url]');
				$(this).find('#link-description-en').attr('name', 'artwork_links[' + id + '][description][en]');
				$(this).find('#link-description-es').attr('name', 'artwork_links[' + id + '][description][es]');
				$(this).find('#link-description-pt').attr('name', 'artwork_links[' + id + '][description][pt]');
				$(this).find('.link-id').attr('name', 'artwork_links[' + id + '][id]');
				$(this).find('.link-id').val(id);
				$(this).find('.featured-input').val(id);
				$(this).find('.featured-input').attr('id', 'featured_link_' + id);
				$(this).find('.featured-label').attr('for', 'featured_link_' + id);
			});
		}

	});

})(jQuery);