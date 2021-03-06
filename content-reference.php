<?php namespace DevHub; ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<h1><a href="<?php the_permalink() ?>"><?php echo get_signature(); ?></a></h1>

	<section class="description">
		<?php the_excerpt(); ?>
	</section>

<?php if ( is_single() ) : ?>

	<section class="long-description">
		<?php echo get_long_description(); ?>
	</section>
	<section class="return"><p><strong>Return:</strong> <?php echo get_return(); ?></p></section>

	<?php
	$source_file = get_source_file();
	if ( ! empty( $source_file ) ) :
		?>
		<section class="source">
			<p>
				<strong><?php _e( 'Source file:', 'wporg' ); ?></strong>
				<a href="<?php echo get_source_file_archive_link( $source_file ); ?>"><?php echo esc_html( $source_file ); ?></a>
			</p>
			<?php if ( post_type_has_source_code() ) { ?>
			<p>
				<a href="#source-code"><?php _e( 'View source', 'wporg' ); ?></a>
			</p>
			<?php } else { ?>
			<p>
				<a href="<?php echo get_source_file_link(); ?>"><?php _e( 'View on Trac', 'wporg' ); ?></a>
			</p>
			<?php } ?>
		</section>
	<?php endif; ?>

	<?php /*
	<?php if ( is_archive() ) : ?>
	<section class="meta">Used by TODO | Uses TODO | TODO Examples</section>
	<?php endif; ?>
	*/ ?>

	<?php
	ob_start();
	the_content();
	$explanation = ob_get_clean();
	if ( $explanation ) :
		?>
		<hr/>
		<section class="explanation">
			<h2><?php _e( 'Explanation', 'wporg' ); ?></h2>
			<?php the_content(); ?>
		</section>
	<?php endif; ?>

	<?php if ( $params = get_params() ) : ?>
	<hr/>
	<section class="parameters">
		<h2><?php _e( 'Parameters', 'wporg' ); ?></h2>
		<dl>
			<?php foreach ( $params as $param ) : ?>
			<?php if ( ! empty( $param['variable'] ) ) : ?>
			<dt><?php echo esc_html( $param['variable'] ); ?></dt>
			<?php endif; ?>
			<dd>
				<p class="desc">
					<?php if ( ! empty( $param['types'] ) ) : ?>
					<span class="type">(<?php echo wp_kses_post( $param['types'] ); ?>)</span>
					<?php endif; ?>
					<?php if ( ! empty( $param['required'] ) && 'wp-parser-hook' !== get_post_type() ) : ?>
					<span class="required">(<?php echo esc_html( $param['required'] ); ?>)</span>
					<?php endif; ?>
					<?php if ( ! empty( $param['content'] ) ) : ?>
					<span class="description"><?php echo param_formatting_fixup( wp_kses_post( $param['content'] ) ); ?></span>
					<?php endif; ?>
				</p>
				<?php if ( ! empty( $param['default'] ) ) : ?>
				<p class="default"><?php _e( 'Default value:', 'wporg' );?> <?php echo esc_html( $param['default'] ); ?></p>
				<?php endif; ?>
			</dd>
			<?php endforeach; ?>
		</dl>
	</section>
	<?php endif; ?>

	<?php /*
	<?php if ( $arguments = get_arguments() ) : //todo: output arg data ?>
	<hr/>
	<section class="arguments">
		<h2><?php _e( 'Arguments', 'wporg' ); ?></h2>
	</section>
	<?php endif; ?>
	<hr/>
	<section class="learn-more">
		<h2><?php _e( 'Learn More', 'wporg' ); ?></h2>
	</section>
	*/ ?>

	<?php if ( 'wp-parser-class' === get_post_type() ) :
		if ( $children = get_children( array( 'post_parent' => get_the_ID(), 'post_status' => 'publish' ) ) ) :
			usort( $children, __NAMESPACE__ . '\\compare_objects_by_name' );
	?>
		<hr/>
		<section class="class-methods">
		<h2><?php _e( 'Methods', 'wporg' ); ?></h2>
		<ul>
			<?php foreach ( $children as $child ) : ?>
				<li><a href="<?php echo get_permalink( $child->ID ); ?>">
				<?php
					$title = get_the_title( $child );
					$pos = ( $i = strrpos( $title, ':' ) ) ? $i + 1 : 0;
					echo substr( $title, $pos );
				?></a>
				<?php if ( $excerpt = apply_filters( 'get_the_excerpt', $child->post_excerpt ) ) {
					echo '&mdash; ' . sanitize_text_field( $excerpt );
				} ?>
				</li>
			<?php endforeach; ?>
		</ul>
		</section>
		<?php endif;
	endif; ?>

	<?php if ( post_type_has_source_code() ) : ?>
		<hr />
		<a id="source-code"></a>
		<section class="source-content">
			<h2><?php _e( 'Source', 'wporg' ); ?></h2>
			<div class="source-code-container">
				<pre class="brush: php; toolbar: false; first-line: <?php echo esc_attr( get_post_meta( get_the_ID(), '_wp-parser_line_num', true ) ); ?>"><?php echo htmlentities( get_source_code() ); ?></pre>
			</div>
			<p class="source-code-links">
				<span>
					<a href="#" class="show-complete-source"><?php _e( 'Expand full source code', 'wporg' ); ?></a>
					<a href="#" class="less-complete-source"><?php _e( 'Collapse full source code', 'wporg' ); ?></a>
				</span>
				<span><a href="<?php echo get_source_file_link(); ?>"><?php _e( 'View on Trac', 'wporg' ); ?></a></span>
			</p>
		</section>
	<?php endif; ?>

	<?php
	$changelog_data = get_changelog_data();
	if ( ! empty( $changelog_data ) ) :
		?>
		<hr/>
		<section class="changelog">
			<h2><?php _e( 'Changelog', 'wporg' ); ?></h2>
			<ul>
				<?php foreach ( $changelog_data as $version => $data ) : ?>
					<li><?php _e( '<strong>Since:</strong> WordPress', 'wporg' ); ?> <a href="<?php echo esc_url( $data['since_url'] ); ?>"><?php echo esc_html( $version ); ?></a> <?php echo $data['description']; // escaped in get_changelog_data() ?></li>
				<?php endforeach; ?>
			</ul>
		</section>
	<?php endif; ?>

	<?php if ( comments_open() || '0' != get_comments_number() ) : ?>
	<hr/>
	<section class="user-notes">
		<h2><?php _e( 'User Contributed Notes', 'wporg' ); ?></h2>
		<?php comments_template(); /* TODO: add '/user-notes.php' */ ?>
	</section>
	<?php endif; ?>

<?php endif; ?>

</article>
