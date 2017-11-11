jQuery( document ).ready(function() {
	//jQuery('#ns-wp-editor-div').append(jQuery('#wp-ns-editor-add-prod-short-desc-wrap'));
	
	/*PRODUCT DATA*/
	jQuery("li").on('click', function () {
		jQuery(".ns-prod-data-tab").addClass("ns-hidden");
		jQuery("li").removeClass("ns-active");
		jQuery("." + jQuery(this).attr("id")).removeClass("ns-hidden");
		jQuery(this).addClass("ns-active");	
	});
	
	jQuery("#ns-manage-stock").on('click', function () {
		if(jQuery("#ns-manage-stock").val() == "no"){
			jQuery('#ns-manage-stock-div').css('display','block');
			jQuery("#ns-manage-stock").val("yes");
		}
		else{
			jQuery('#ns-manage-stock-div').css('display','none');
			jQuery("#ns-manage-stock").val("no");
		}
	});
	
	//attributes
	var i = 0;
	jQuery('#ns-add-attribute-btn').on('click', function(event) {     
		if(jQuery('#ns-attribute-taxonomy').val() == 'ns-color-att'){
			jQuery('#ns-inner-attributes').after('<div class="ns-color-attr-class"><h3><label>Color</label></h3><div><label>Add new color:</label><br><input id="ns-color-attr" name="ns-color-attr" class="ns-input-width" type="text"></div><div><label id="ns-existing-colors">Select a color: </label></div><div><label>Visible on product page </label><input class="checkbox" name="ns-attr-visibility-status" id="ns-attr-visibility-status" checked="checked" type="checkbox"></div><button id="ns-attribute-btn-remove-col" type="button" class="button" style="float:left">Remove</button></div>');
			jQuery('#ns-color-id').prop('disabled', true);
			jQuery('#ns-attribute-taxonomy').val(jQuery('#ns-attribute-taxonomy option:first').val());
			//get the color attributes already saved to create the checkboxes and permits the user to choosing them
			var col_attr = jQuery('#ns-color-att-list').val();
			col_attr = col_attr.split(',');
			
			//create checkboxes for each color already inserted
			jQuery('#ns-existing-colors').after('<table id="color-table">');
			jQuery.each(col_attr, function(index, value){
				if(value != "")
					jQuery('#color-table').append('<tr><th>'+value+'</th><th><input class="checkbox checkbox-attr-selectable-color" name="'+value+'" type="checkbox"></th></tr>');
				
			});
			jQuery('#ns-existing-colors').append('</table>');
		}
		else{
			jQuery('#ns-inner-attributes').after('<div><h3><label>Custom product attribute</label></h3><div><label>Name:</label><br><input class="ns-input-width" name="ns-attr-names'+i+'" id="ns-attr-names'+i+'" type="text"/></div><div><label>Value(s)</label><textarea name="ns-attribute-values'+i+'"placeholder="Enter some text, or some attributes by &quot;|&quot; separating values."></textarea></div><div><label>Visible on product page </label><input class="checkbox" name="ns-attr-visibility-status'+i+'" id="ns-attr-visibility-status'+i+'" checked="checked" type="checkbox"/></div><button id="ns-attribute-btn-remove" type="button" class="button" style="float:left">Remove</button></div>');
			i++;
		}
		jQuery('#ns-attribute-list').val(i);
		
	});
	
	//removing attribute
	jQuery(document).on('click', '#ns-attribute-btn-remove, #ns-attribute-btn-remove-col', function(event){
		if(jQuery(this).parent().hasClass('ns-color-attr-class')){
			jQuery('#ns-color-id').prop('disabled', false);
		}
		jQuery(this).parent().remove();
		if(jQuery(this).attr('id') == 'ns-attribute-btn-remove')	// check if theres a need to decrement the counter -- only in case im removing a custom attributes --
			i--;
		jQuery('#ns-attribute-list').val(i);
	});
	
	//saving into hidden input selectable color
	jQuery(document).on('click', '.checkbox-attr-selectable-color', function(event){
		if(jQuery(this).is(':checked')){
			jQuery('#ns-attr-from-list').val(jQuery('#ns-attr-from-list').val()+jQuery(this).attr('name')+',');
		}
		else{
			var new_string = "";
		    new_string = jQuery('#ns-attr-from-list').val();
			new_string = new_string.replace(jQuery(this).attr('name')+',', "");
			console.log(new_string);
			jQuery('#ns-attr-from-list').val(new_string);
		}		
		
	});
	
		
	/*PRODUCT IMAGE*/
	/*This is used to create a temporary url (objectURL) to update the thumbnail image after user insert one*/
	jQuery('#ns-thumbnail').change( function(event) {
		jQuery("#ns-img-thumbnail").fadeIn("fast").attr('src',URL.createObjectURL(event.target.files[0]));
	});
	
	/*GALLERY AND MODAL*/
	/* When the user clicks on the button, open the gallery modal*/
	jQuery("#ns-myBtn").on('click', function() {
		jQuery('#ns-myModal').css("display","block");
	});

	/* When the user clicks on (x), close the gallery modal*/
	jQuery(".ns-close").on('click', function() {
		jQuery('#ns-myModal').css("display","none");
	});

	/*Used to get the selected image from gallery list*/
	var img_array = [];		//this array will contains all the SELECTED images 
	
	jQuery('.ns-image-container img').on('click', function(){
		//Image clicked for the first time
		if(img_array.indexOf(jQuery(this).attr("id")) < 0){
			img_array.push(jQuery(this).attr("id"));
			//setting the value of the input with the urls of images separated by comma
			jQuery('#ns-image-from-list').val(img_array.toString());
			//jQuery('#ns-image-from-list').val( jQuery(this).attr("src") );
			jQuery(this).css('border','5px solid #bdcfed');
		}
		else{
			//Image already being clicked. Removing border and delete element from img_array
			jQuery(this).css('border', '1px solid gray');
			var elementToRemove = jQuery(this).attr("id");
			img_array = jQuery.grep(img_array, function(value) {
			  return value != elementToRemove;
			});
			jQuery('#ns-image-from-list').val(img_array.toString());
		}
			
	});
	
	/*This one is used to upload into the gallery the image from local path
	jQuery('#ns-image-from-file').change( function(event) {
		jQuery('#ns-image-from-file').attr('src',URL.createObjectURL(event.target.files[0]));
	});*/
	
	
	/*HIDE SHOW DIVS*/
	//product data
	jQuery('#ns-post-prod-data-hide-show').on('click', function(event) {   
	         
			 if(jQuery( '#ns-product-data-inner-container' ).is(':hidden')){ 
				jQuery('#ns-post-prod-data-hide-show').removeClass('dashicons-arrow-down').addClass('dashicons-arrow-up');
				jQuery('#ns-product-data-inner-container').css("display", "block");
			 }
			 else {
				jQuery('#ns-post-prod-data-hide-show').removeClass('dashicons-arrow-up').addClass('dashicons-arrow-down');
				jQuery('#ns-product-data-inner-container').css("display", "none");
				}
			
	});
	
	//short description
	jQuery('#ns-short-desc-hide-show').on('click', function(event) {            
			 if(jQuery( '#ns-wp-editor-div' ).is( ':hidden' )){
				jQuery('#ns-short-desc-hide-show').removeClass('dashicons-arrow-down').addClass('dashicons-arrow-up');
				jQuery('#ns-wp-editor-div').css("display", "block");
			 }
			 else {
				jQuery('#ns-short-desc-hide-show').removeClass('dashicons-arrow-up').addClass('dashicons-arrow-down');
				jQuery('#ns-wp-editor-div').css("display", "none");
			}
			
	});
	
	//post content
	jQuery('#ns-post-content-hide-show').on('click', function(event) {        
             if(jQuery( '#ns-wp-post-content-div' ).is( ':hidden' )){
				jQuery('#ns-post-content-hide-show').removeClass('dashicons-arrow-down').addClass('dashicons-arrow-up');
				jQuery('#ns-wp-post-content-div').css("display", "block");
			 }
			 else {
				jQuery('#ns-post-content-hide-show').removeClass('dashicons-arrow-up').addClass('dashicons-arrow-down');
				jQuery('#ns-wp-post-content-div').css("display", "none");
			}
			
    });
	
	//tags
	jQuery('#ns-prod-tags-hide-show').on('click', function(event) {        
			 if(jQuery( '#ns-prod-tags-div' ).is( ':hidden' )){
				jQuery('#ns-product-tags').css('height', 'auto');
				jQuery('#ns-prod-tags-hide-show').removeClass('dashicons-arrow-down').addClass('dashicons-arrow-up');	
				jQuery('#ns-prod-tags-div').css("display", "block");	
			 } else {
				 jQuery('#ns-product-tags').css('height', '100%');
				 jQuery('#ns-prod-tags-hide-show').removeClass('dashicons-arrow-up').addClass('dashicons-arrow-down');
				 jQuery('#ns-prod-tags-div').css("display", "none");
			 }
    });
	
	//add image
	jQuery('#ns-prod-image-hide-show').on('click', function(event) {        
			 if(jQuery( '#ns-image-container-0' ).is( ':hidden' )){
				jQuery('#ns-image-container').css('height', 'auto');	
				jQuery('#ns-prod-image-hide-show').removeClass('dashicons-arrow-down').addClass('dashicons-arrow-up');
				jQuery('#ns-image-container-0').css("display", "block");
			 } else {
			 	 jQuery('#ns-image-container').css('height', '100%');
				 jQuery('#ns-prod-image-hide-show').removeClass('dashicons-arrow-up').addClass('dashicons-arrow-down');
				 jQuery('#ns-image-container-0').css("display", "none");
			 }
    });
	
	//categories
	jQuery('#ns-prod-categories-hide-show').on('click', function(event) {        
			 if(jQuery( '#ns-prod-cat-inner' ).is( ':hidden' )){
				jQuery('#ns-product-categories').css('height', 'auto');
				jQuery('#ns-prod-categories-hide-show').removeClass('dashicons-arrow-down').addClass('dashicons-arrow-up');
				jQuery('#ns-prod-cat-inner').css("display", "block");
			 } else {
			 	 jQuery('#ns-product-categories').css('height', '100%');
				 jQuery('#ns-prod-categories-hide-show').removeClass('dashicons-arrow-up').addClass('dashicons-arrow-down');
				 jQuery('#ns-prod-cat-inner').css("display", "none");
			 }
			
    });
	
	//gallery
	jQuery('#ns-prod-gallery-hide-show').on('click', function(event) {        
			 if(jQuery( '#ns-prod-gallery-inner' ).is( ':hidden' )){
				jQuery('#ns-product-gallery').css('height', 'auto');
				jQuery('#ns-prod-gallery-hide-show').removeClass('dashicons-arrow-down').addClass('dashicons-arrow-up');
				jQuery('#ns-prod-gallery-inner').css("display", "block");
			 } else {
			 	jQuery('#ns-product-gallery').css('height', '100%');
				jQuery('#ns-prod-gallery-hide-show').removeClass('dashicons-arrow-up').addClass('dashicons-arrow-down');
				jQuery('#ns-prod-gallery-inner').css("display", "none");
			 }
    });
	
	
});