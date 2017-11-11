<div id="booked-welcome-screen">
	<div class="wrap about-wrap">
		<h1><?php echo sprintf(esc_html__('Welcome to %s','wyzi-business-finder'),'Booked '.BOOKED_VERSION); ?></h1>
		<div class="about-text">
			<?php echo sprintf(esc_html__('Thank you for choosing %s! If this is your first time using %s, you will find some helpful "Getting Started" links below. If you just updated the plugin, you can find out what\'s new in the "What\'s New" section below.','wyzi-business-finder'),'Booked','wyzi-business-finder'); ?>
		</div>
		<div class="booked-badge">
			<img src="<?php echo BOOKED_PLUGIN_URL; ?>/templates/images/badge.png">
		</div>
		
		<div id="welcome-panel" class="welcome-panel">
			
			<img src="<?php echo BOOKED_PLUGIN_URL; ?>/templates/images/welcome-banner.jpg" class="booked-welcome-banner">
			
			<div class="welcome-panel-content">
				<div class="welcome-panel-column-container">
					<div class="welcome-panel-column">
					
						<h3><?php esc_html_e('Getting Started','wyzi-business-finder'); ?></h3>
						<ul>
							<li><?php echo booked_get_kb_article(3239); ?></li>
							<li><?php echo booked_get_kb_article(6268); ?></li>
							<li><?php echo booked_get_kb_article(3238); ?></li>
							<li><?php echo booked_get_kb_article(3233); ?></li>
							<li><?php echo booked_get_kb_article(6267); ?></li>
							<li><?php echo booked_get_kb_article(3240); ?></li>
						</ul>
						<a class="button" style="margin-bottom:15px; margin-top:0;" href="https://boxystudio.ticksy.com/articles/7827/" target="_blank"><?php esc_html_e('View all Guides','wyzi-business-finder'); ?>&nbsp;&nbsp;<i class="booked-icon booked-icon-sign-out"></i></a>&nbsp;
						<a class="button button-primary" style="margin-bottom:15px; margin-top:0;" href="<?php echo get_admin_url().'admin.php?page=booked-settings'; ?>"><?php esc_html_e('Get Started','wyzi-business-finder'); ?></a>
						
					</div>
					<div class="welcome-panel-column welcome-panel-last welcome-panel-updates-list">			
						
						<?php echo booked_parse_readme_changelog(); ?>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>