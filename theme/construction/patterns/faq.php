<?php
/**
 * Title: FAQ
 * Slug: construction/faq
 * Categories: construction
 * Description: Editable FAQ accordion (Details blocks).
 *
 * @package Construction
 */
?>
<!-- wp:group {"align":"full","className":"construction-faq","layout":{"type":"default"},"anchor":"faq"} -->
<div class="wp-block-group alignfull construction-faq" id="faq">
	<!-- wp:group {"className":"construction-faq__inner","layout":{"type":"default"}} -->
	<div class="wp-block-group construction-faq__inner">
		<!-- wp:group {"className":"construction-faq__header","layout":{"type":"default"}} -->
		<div class="wp-block-group construction-faq__header">
			<!-- wp:paragraph {"className":"construction-eyebrow"} -->
			<p class="construction-eyebrow"><?php echo esc_html( construction_t( 'faq.eyebrow' ) ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:heading -->
			<h2 class="wp-block-heading"><?php echo esc_html( construction_t( 'faq.title' ) ); ?></h2>
			<!-- /wp:heading -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"construction-faq__list","layout":{"type":"default"}} -->
		<div class="wp-block-group construction-faq__list">
			<!-- wp:details {"className":"construction-faq-item"} -->
			<details class="wp-block-details construction-faq-item">
				<summary><?php echo esc_html( construction_t( 'faq.1.q' ) ); ?></summary>
				<!-- wp:group {"className":"construction-faq-item__panel","layout":{"type":"default"}} -->
				<div class="wp-block-group construction-faq-item__panel">
					<!-- wp:paragraph -->
					<p><?php echo esc_html( construction_t( 'faq.1.a' ) ); ?></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</details>
			<!-- /wp:details -->

			<!-- wp:details {"className":"construction-faq-item"} -->
			<details class="wp-block-details construction-faq-item">
				<summary><?php echo esc_html( construction_t( 'faq.2.q' ) ); ?></summary>
				<!-- wp:group {"className":"construction-faq-item__panel","layout":{"type":"default"}} -->
				<div class="wp-block-group construction-faq-item__panel">
					<!-- wp:paragraph -->
					<p><?php echo esc_html( construction_t( 'faq.2.a' ) ); ?></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</details>
			<!-- /wp:details -->

			<!-- wp:details {"className":"construction-faq-item"} -->
			<details class="wp-block-details construction-faq-item">
				<summary><?php echo esc_html( construction_t( 'faq.3.q' ) ); ?></summary>
				<!-- wp:group {"className":"construction-faq-item__panel","layout":{"type":"default"}} -->
				<div class="wp-block-group construction-faq-item__panel">
					<!-- wp:paragraph -->
					<p><?php echo esc_html( construction_t( 'faq.3.a' ) ); ?></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</details>
			<!-- /wp:details -->

			<!-- wp:details {"className":"construction-faq-item"} -->
			<details class="wp-block-details construction-faq-item">
				<summary><?php echo esc_html( construction_t( 'faq.4.q' ) ); ?></summary>
				<!-- wp:group {"className":"construction-faq-item__panel","layout":{"type":"default"}} -->
				<div class="wp-block-group construction-faq-item__panel">
					<!-- wp:paragraph -->
					<p><?php echo esc_html( construction_t( 'faq.4.a' ) ); ?></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</details>
			<!-- /wp:details -->

			<!-- wp:details {"className":"construction-faq-item"} -->
			<details class="wp-block-details construction-faq-item">
				<summary><?php echo esc_html( construction_t( 'faq.5.q' ) ); ?></summary>
				<!-- wp:group {"className":"construction-faq-item__panel","layout":{"type":"default"}} -->
				<div class="wp-block-group construction-faq-item__panel">
					<!-- wp:paragraph -->
					<p><?php echo esc_html( construction_t( 'faq.5.a' ) ); ?></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</details>
			<!-- /wp:details -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
