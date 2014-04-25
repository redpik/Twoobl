<?php get_header(); ?>

<div class="row">

	<div id="main" class="col-sm-9" role="main">

		<div class="entry-header">
			<h1 class="title"><?php _e('Sorry, but the page you were trying to view does not exist.', 'twoobl'); ?></h1>
		</div>

		<div class="entry-content clearfix">
			<?php
			echo apply_filters('the_content', $twoobl->field('content_404'));
			?>
		</div>

	</div>

	<?php get_template_part('templates/sidebar'); ?>

</div>

<?php get_footer(); ?>

