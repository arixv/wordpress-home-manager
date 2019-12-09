function load_posts_items( options ) {
	var paging_opts = {
		page:1,
		cat:false,
		s:'',
		taxonomy:''
	};

	if ( options.cat) {paging_opts.cat = options.cat;}
	if ( options.s) {paging_opts.s = options.s;}
	if ( options.page) {paging_opts.page = options.page;}
	if ( options.taxonomy != '' ) {paging_opts.taxonomy = options.taxonomy;}
	
	jQuery('#posts-items').html('');
	jQuery('.tablenav-pages').html('');

	var data = {
		action: 'load_posts_items_action',
		paged: paging_opts.page,
		cat: options.cat,
		s: options.s,
		taxonomy: options.taxonomy
	};

	jQuery.post(ajaxurl, data, function(response) {
		
		
		jQuery.each( eval( response ), function(key,element) {
			
			var list_item = jQuery('<li/>');
			var post_item = jQuery('<div />');

			post_item.attr('class','post-item clearfix');
			post_item.attr('post_id',element.post_id);
			post_item.attr('post_type',element.post_type);
			post_item.attr('post_permalink',element.post_permalink);
			post_item.attr('cat_id',element.category_id);
			post_item.attr('cat_name',element.category_name);
			post_item.attr('cat_link',element.category_link);
			post_item.attr('post_media_type',element.post_media_type);

			// Imagen Principal.
			if ( element.post_image != '' ) {
				var post_img = jQuery('<img />');
				post_img.attr('src',element.post_image);
				post_item.append( post_img );
			} else {
				var no_image = jQuery('<div />');
				no_image.text('Sin Foto Principal');
				no_image.attr('class','no-image');
				post_item.append( post_img );
			}

			// Titulo Category y Fecha
			var info_container = jQuery('<div />');
			var category_name = jQuery('<h5 />');
			var post_title = jQuery('<h4 />');
			var post_date = jQuery('<time />');
			
			info_container.attr('css','info_container');
			info_container.attr('style','float:left;width:50%;');

			category_name.text(element.category_name);
			post_title.text(element.post_title);
			post_date.text(element.post_date);

			info_container.append(category_name);
			info_container.append(post_title);
			info_container.append(post_date);

			post_item.append ( info_container );
			list_item.append(post_item);

			jQuery('#posts-items' ).append( list_item );

		});
		
		// Paging.
		var tablenav_pages = jQuery('.tablenav-pages');
		var paging = jQuery('<div/>');
		paging.attr('class','paging');

		var pagination_links = jQuery('<span/>');
		paging.attr('class','pagination-links');

		var btn_prev = jQuery('<a/>');
		btn_prev.attr('class','button prev-page');
		btn_prev.attr('title','Go to the previous page');
		btn_prev.attr('href','#');
		btn_prev.text('‹');
		btn_prev.click(function(e){
			e.preventDefault();
			load_posts_items({
				page: (options.page - 1 ),
				cat: paging_opts.cat,
				s: paging_opts.s,
				taxonomy: paging_opts.taxonomy 
			});
		});

		var btn_next = jQuery('<a/>');
		btn_next.attr('class','button next-page');
		btn_next.attr('title','Go to the next page');
		btn_next.attr('href','#');
		btn_next.text('›');
		btn_next.click(function(e){
			e.preventDefault();
			load_posts_items({
				page: (paging_opts.page+1),
				cat: paging_opts.cat,
				s: paging_opts.s,
				taxonomy: paging_opts.taxonomy 
			});
		});

		pagination_links.append(btn_prev);
		pagination_links.append(btn_next);
		paging.append(pagination_links);
		tablenav_pages.append(paging);
		
		jQuery(".post-item" ).draggable({
			cursor:"crosshair", 
			opacity: 0.9, 
			helper: "clone"
		});
		
	});	
}