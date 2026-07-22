<?php
/**
 * Title: Process
 * Slug: construction/process
 * Categories: construction
 * Description: Four-step “How we work” section (editable blocks).
 *
 * @package Construction
 */
?>
<!-- wp:group {"align":"full","className":"construction-process","layout":{"type":"default"},"anchor":"process"} -->
<div class="wp-block-group alignfull construction-process" id="process">
	<!-- wp:group {"className":"construction-process__inner","layout":{"type":"default"}} -->
	<div class="wp-block-group construction-process__inner">
		<!-- wp:group {"className":"construction-process__header","layout":{"type":"default"}} -->
		<div class="wp-block-group construction-process__header">
			<!-- wp:paragraph {"className":"construction-eyebrow"} -->
			<p class="construction-eyebrow"><?php echo esc_html( construction_t( 'process.eyebrow' ) ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:heading -->
			<h2 class="wp-block-heading"><?php echo esc_html( construction_t( 'process.title' ) ); ?></h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"className":"construction-process__intro"} -->
			<p class="construction-process__intro"><?php echo esc_html( construction_t( 'process.intro' ) ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<!-- wp:columns {"className":"construction-process__grid"} -->
		<div class="wp-block-columns construction-process__grid">
			<!-- wp:column -->
			<div class="wp-block-column">
				<!-- wp:group {"className":"construction-process-step","layout":{"type":"default"}} -->
				<div class="wp-block-group construction-process-step">
					<!-- wp:paragraph {"className":"construction-process-step__num"} -->
					<p class="construction-process-step__num">01</p>
					<!-- /wp:paragraph -->
					<!-- wp:heading {"level":3} -->
					<h3 class="wp-block-heading"><?php echo esc_html( construction_t( 'process.1.title' ) ); ?></h3>
					<!-- /wp:heading -->
					<!-- wp:paragraph -->
					<p><?php echo esc_html( construction_t( 'process.1.text' ) ); ?></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:column -->

			<!-- wp:column -->
			<div class="wp-block-column">
				<!-- wp:group {"className":"construction-process-step","layout":{"type":"default"}} -->
				<div class="wp-block-group construction-process-step">
					<!-- wp:paragraph {"className":"construction-process-step__num"} -->
					<p class="construction-process-step__num">02</p>
					<!-- /wp:paragraph -->
					<!-- wp:heading {"level":3} -->
					<h3 class="wp-block-heading"><?php echo esc_html( construction_t( 'process.2.title' ) ); ?></h3>
					<!-- /wp:heading -->
					<!-- wp:paragraph -->
					<p><?php echo esc_html( construction_t( 'process.2.text' ) ); ?></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:column -->

			<!-- wp:column -->
			<div class="wp-block-column">
				<!-- wp:group {"className":"construction-process-step","layout":{"type":"default"}} -->
				<div class="wp-block-group construction-process-step">
					<!-- wp:paragraph {"className":"construction-process-step__num"} -->
					<p class="construction-process-step__num">03</p>
					<!-- /wp:paragraph -->
					<!-- wp:heading {"level":3} -->
					<h3 class="wp-block-heading"><?php echo esc_html( construction_t( 'process.3.title' ) ); ?></h3>
					<!-- /wp:heading -->
					<!-- wp:paragraph -->
					<p><?php echo esc_html( construction_t( 'process.3.text' ) ); ?></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:column -->

			<!-- wp:column -->
			<div class="wp-block-column">
				<!-- wp:group {"className":"construction-process-step","layout":{"type":"default"}} -->
				<div class="wp-block-group construction-process-step">
					<!-- wp:paragraph {"className":"construction-process-step__num"} -->
					<p class="construction-process-step__num">04</p>
					<!-- /wp:paragraph -->
					<!-- wp:heading {"level":3} -->
					<h3 class="wp-block-heading"><?php echo esc_html( construction_t( 'process.4.title' ) ); ?></h3>
					<!-- /wp:heading -->
					<!-- wp:paragraph -->
					<p><?php echo esc_html( construction_t( 'process.4.text' ) ); ?></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
