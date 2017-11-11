<?php
/**
 * Businesses's gallery add image form.
 *
 * @package wyz
 */

/**
 * Display the form
 *
 * @param boolean $has_images tells wheather the business already has images or not.
 */
function wyz_add_images( $has_images ) {
	ob_start(); ?>

	<div class="head fix">
		<div class="col-lg-6 col-md-6 col-xs-12">
			<h3 class="row"><?php echo sprintf( esc_html__( "%s photos", 'wyzi-business-finder' ), get_the_title() );?></h3>
		</div>
		<form method="post" enctype="multipart/form-data">
			<input id="upload-button" type="button" value="<?php esc_html_e( 'add/remove images', 'wyzi-business-finder' );?>" />
			<input name="bus-img-id" type="hidden" value="<?php echo esc_attr( get_the_ID() ); ?>"/>
			<?php if ( $has_images ) {?>
				<input type="submit" name="delete-bus-gal" value="<?php esc_html_e( 'Remove All Images', 'wyzi-business-finder' );?>" onclick="return confirm('<?php esc_html_e( 'are you sure you want to delete all your images?', 'wyzi-business-finder' );?>');"/>
			<?php }?>
		</form>
	</div>
	<?php return ob_get_clean();
}
?>
