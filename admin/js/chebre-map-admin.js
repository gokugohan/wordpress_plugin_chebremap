jQuery(document).ready(function(){

	var map;

	jQuery("#btn-select-coordenate").click(function (){
		jQuery("#chebre-map-modal").css("display","block");
		map = L.map('chebre-map-modal-map').setView([-8.787519, 125.946401], 9);
		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
		}).addTo(map);

		map.on("click", function (ev) {
			jQuery("#chebre-map_latitude").val(ev.latlng.lat);
			jQuery("#chebre-map_longitude").val(ev.latlng.lng);

			jQuery("#chebre-map-modal").fadeOut();

		});
	});



	jQuery(".chebre-map-modal-close").click(function(){
		jQuery("#chebre-map-modal").fadeOut();
	});

	// on upload button click
	jQuery('body').on( 'click', '.chebre-map-sidebar-image-upload-button', function(e){

		e.preventDefault();

		let custom_uploader = wp.media({
				title: 'Insert image',
				library : {
					// uploadedTo : wp.media.view.settings.post.id, // attach to the current post?
					type : 'image'
				},
				button: {
					text: 'Use this image' // button label text
				},
				multiple: false
			}).on('select', function() { // it also has "open" and "close" events
				let attachment = custom_uploader.state().get('selection').first().toJSON();
				jQuery(".result-img").prop('src',attachment.url);
				jQuery("#chebre-map-sidebar-image-url").val(attachment.url);
					// button.html('<img src="' + attachment.url + '">').next().show().next().val(attachment.id);
			}).open();

	});

	jQuery('body').on( 'click', '.chebre-map-sidebar-image-remove-button', function(e) {
		e.preventDefault();
		let default_img = jQuery("#chebre-map-default-img").val();
		jQuery("#chebre-map-sidebar-image-url").val(default_img);
		jQuery(".result-img").prop('src',default_img);
	});

	// on upload button click
	jQuery('body').on( 'click', '.chebre-map-sidebar-avatar-upload-button', function(e){

		e.preventDefault();

		let custom_uploader = wp.media({
			title: 'Insert avatar',
			library : {
				// uploadedTo : wp.media.view.settings.post.id, // attach to the current post?
				type : 'image'
			},
			button: {
				text: 'Use this image' // button label text
			},
			multiple: false
		}).on('select', function() { // it also has "open" and "close" events
			let attachment = custom_uploader.state().get('selection').first().toJSON();
			jQuery(".result-avatar").prop('src',attachment.url);
			jQuery("#chebre-map-sidebar-avatar-url").val(attachment.url);
			// button.html('<img src="' + attachment.url + '">').next().show().next().val(attachment.id);
		}).open();

	});

	jQuery('body').on( 'click', '.chebre-map-sidebar-avatar-remove-button', function(e) {
		e.preventDefault();
		let default_img = jQuery("#chebre-map-default-avatar").val();
		jQuery("#chebre-map-sidebar-avatar-url").val(default_img);
		jQuery(".result-avatar").prop('src',default_img);
	});




});
