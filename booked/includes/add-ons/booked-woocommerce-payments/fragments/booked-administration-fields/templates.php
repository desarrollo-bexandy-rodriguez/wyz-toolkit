<?php
$products = Booked_WC_Functions::get_products();
?>
<li id="bookedCFTemplate-paid-service-label" class="ui-state-default"><i class="main-handle booked-icon booked-icon-bars"></i>
	<small><?php _e('Paid Service Selector', 'wyzi-business-finder'); ?></small>
	<p><input class="cf-required-checkbox" type="checkbox" name="required" id="required"> <label for="required"><?php _e('Required Field', 'wyzi-business-finder'); ?></label></p>
	<input type="text" name="paid-service-label" value="" placeholder="Enter a label for this drop-down group..." />
	<ul id="booked-cf-paid-service"></ul>
	<button class="cfButton button" data-type="single-paid-service"><i class="booked-icon booked-icon-plus"></i>&nbsp;&nbsp;<?php _e('WooCommerce Product', 'wyzi-business-finder'); ?></button>
	<span class="cf-delete"><i class="booked-icon booked-icon-close"></i></span>
</li>
<li id="bookedCFTemplate-single-paid-service" class="ui-state-default"><i class="sub-handle booked-icon booked-icon-bars"></i>
	<select name="single-paid-service" >
		<option value=""><?php _e('Select a Product', 'wyzi-business-finder'); ?></option>
		<?php foreach ($products['options'] as $product_id => $product_title): ?>
			<?php $product = Booked_WC_Product::get( intval($product_id) ); ?>
			<option value="<?php echo $product_id ?>"><?php echo esc_html($product->title); ?></option>
		<?php endforeach ?>
	</select>
	<span class="cf-delete"><i class="booked-icon booked-icon-close"></i></span>
</li>
