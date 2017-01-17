

<?php get_header(); ?>

<div class="container">
	<div class="row">
		<div class="col-md-9">
			<div class="blog-wrap">

				<?php wp_reset_query(); ?>
				<?php $wp_query = new WP_Query('cat=3,4'); ?>
				<?php while ($wp_query->have_posts()) : $wp_query->the_post(); ?>

					<div class="blog-title"><a href="<?php the_permalink();?>"><span><?php the_title();?></span></a></div>
					<div class="blog-date"><span><?php the_time('d.m.Y');?></span></div>
					<div class="clearfix"></div>
					<div class="blog-prev">
						<div class="blog-img">
							<a href="<?php the_permalink();?>"><?php the_post_thumbnail();?></a>
						</div>
						<div class="blog-txt">
							<span><?php the_content();?></span>
						</div>
						<div class="clearfix"></div>
						<div class="blog-button"><a href="<?php the_permalink();?>">читать</a></div>
					</div>

				<?php endwhile; ?>
				

				<div class="blog-pag">
					<?php the_posts_pagination();?>
					<!-- <a href="#">1</a>
					<a href="#">2</a>
					<a href="#">3</a>
					<span>...</span>
					<a href="#">10</a> -->
				</div>
				<?php wp_reset_query(); ?>
			</div>
		</div>
		<div class="col-md-3">
			<div class="side-title">
				<span>Категории</span>
			</div>
			<div class="blog-side-bar">
				<ul>
				<?php wp_list_categories(); ?>
					<!-- <li><a href="#">Новости</a></li>
					<li><a href="#">Статьи</a></li> -->
				</ul>
			</div>
		</div>
	</div>
</div>




<?php get_footer(); ?>