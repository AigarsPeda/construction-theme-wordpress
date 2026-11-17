<?php
/**
 * Cost-estimating (Tāmēšana) page as editable Gutenberg blocks.
 *
 * One LV / EN / RU page group, linked through Polylang. Page content in the
 * database is the source of truth after the first seed.
 *
 * @package Construction
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Copy, routes, menu labels and SEO fields per language.
 *
 * @return array<string, array<string, mixed>>
 */
function construction_estimate_page_definitions(): array {
	return array(
		'lv' => array(
			'title'      => 'Tāmēšana un tāmju sagatavošana',
			'heading'    => 'Tāmēšana un<br>tāmju sagatavošana',
			'slug'       => 'tamesana',
			'menu_label' => 'Tāmēšana',
			'lead'       => 'Sagatavojam būvniecības izmaksu tāmes privātmājām un izvērtējam nekustamā īpašuma attīstības projektu ekonomisko potenciālu.',
			'hero_alt'   => 'Tāmēšana pēc būvprojekta rasējumiem',
			'seo'        => array(
				'title'       => 'Tāmēšanas pakalpojumi un tāmju sastādīšana %sep% %sitename%',
				'description' => 'Tāmēšana un būvniecības tāmju sastādīšana privātmājām: darbu un materiālu izmaksas pirms būvniecības. Izvērtējam attīstības projektu rentabilitāti.',
				'keywords'    => 'tāmēšana,tāmēšanas pakalpojumi,tāmju sastādīšana,tāmju sagatavošana,mājas būvniecības tāme',
			),
			'private'    => array(
				'question'   => 'Plānojat būvēt māju, bet nezināt, cik tas izmaksās?',
				'intro'      => 'Vai jau esat tikušies ar arhitektu, apsprieduši savas ieceres un uzsākuši mājas projektēšanu, bet joprojām nezināt, cik lielas investīcijas būs nepieciešamas būvniecībai?',
				'promise'    => 'Mēs palīdzēsim rast skaidrību!',
				'details'    => 'Izstrādāsim detalizētu būvniecības izmaksu tāmi, izvērtēsim darbu un materiālu izmaksas, lai jau savlaicīgi varētu plānot budžetu un pieņemt pārdomātus lēmumus.',
				'closing'    => 'Ziniet savas būvniecības izmaksas vēl pirms būvniecības uzsākšanas!',
				'cta'        => 'Pieteikt tāmes izstrādi',
			),
			'business'   => array(
				'question'   => 'Vēlaties iegādāties nekustamā īpašuma attīstības projektu, bet nezināt, vai tas būs finansiāli izdevīgs?',
				'intro'      => 'Vai esat atraduši zemesgabalu, ēku vai attīstības projektu ar investīciju potenciālu, bet pirms lēmuma pieņemšanas vēlaties saprast, cik izmaksās projekta realizācija un kāda varētu būt tā rentabilitāte?',
				'promise'    => 'Mēs palīdzēsim izvērtēt projekta ekonomisko potenciālu!',
				'items'      => array(
					'Aprēķināsim plānotās būvniecības un realizācijas izmaksas.',
					'Izvērtēsim iespējamos būvniecības un tehniskos risinājumus.',
					'Salīdzināsim alternatīvus risinājumus izmaksu optimizācijai.',
					'Palīdzēsim noteikt kopējās nepieciešamās investīcijas.',
					'Aprēķināsim projekta potenciālo rentabilitāti, balstoties uz pieejamajiem datiem un pieņēmumiem.',
				),
				'closing'    => 'Pieņemiet investīciju lēmumus, balstoties uz aprēķiniem, nevis minējumiem!',
				'cta'        => 'Pieteikt projekta izvērtēšanu',
			),
		),
		'en' => array(
			'title'      => 'Construction cost estimating',
			'heading'    => 'Construction<br>cost estimating',
			'slug'       => 'cost-estimating',
			'menu_label' => 'Cost estimating',
			'lead'       => 'We prepare construction cost estimates for private homes and assess the economic potential of property development projects.',
			'hero_alt'   => 'Construction cost estimating from architectural drawings',
			'seo'        => array(
				'title'       => 'Construction Cost Estimating in Latvia %sep% %sitename%',
				'description' => 'Construction cost estimates for private homes in Latvia. Know your building costs and the profitability of development projects before work begins.',
				'keywords'    => 'construction cost estimating,construction cost estimate,building cost estimate,cost estimate latvia',
			),
			'private'    => array(
				'question'   => 'Planning to build a house but unsure what it will cost?',
				'intro'      => 'Have you already met an architect, discussed your ideas and started designing your home, yet still don’t know how much investment the construction will require?',
				'promise'    => 'We will help you get clarity!',
				'details'    => 'We will prepare a detailed construction cost estimate and assess labour and material costs, so you can plan your budget early and make well-considered decisions.',
				'closing'    => 'Know your construction costs before construction begins!',
				'cta'        => 'Request a cost estimate',
			),
			'business'   => array(
				'question'   => 'Looking to acquire a property development project but unsure whether it will pay off?',
				'intro'      => 'Have you found a plot, a building or a development project with investment potential, and want to understand what delivering it will cost and how profitable it could be before you decide?',
				'promise'    => 'We will help you assess the project’s economic potential!',
				'items'      => array(
					'We calculate the planned construction and delivery costs.',
					'We assess possible construction and technical solutions.',
					'We compare alternative solutions to optimise costs.',
					'We help determine the total investment required.',
					'We calculate the project’s potential profitability based on the available data and assumptions.',
				),
				'closing'    => 'Make investment decisions based on calculations, not guesswork!',
				'cta'        => 'Request a project assessment',
			),
		),
		'ru' => array(
			'title'      => 'Составление смет на строительство',
			'heading'    => 'Составление смет<br>на строительство',
			'slug'       => 'sostavlenie-smet',
			'menu_label' => 'Сметы',
			'lead'       => 'Составляем сметы на строительство частных домов и оцениваем экономический потенциал девелоперских проектов.',
			'hero_alt'   => 'Составление смет по строительным чертежам',
			'seo'        => array(
				'title'       => 'Составление смет на строительство в Латвии %sep% %sitename%',
				'description' => 'Составляем сметы на строительство частных домов в Латвии. Оцениваем затраты и рентабельность девелоперских проектов до начала строительства.',
				'keywords'    => 'составление смет,смета на строительство дома,сметы на строительство,составление сметы,услуги сметчика',
			),
			'private'    => array(
				'question'   => 'Планируете строить дом, но не знаете, во сколько он обойдется?',
				'intro'      => 'Вы уже встретились с архитектором, обсудили свои идеи и начали проектирование дома, но до сих пор не знаете, какие инвестиции потребуются для строительства?',
				'promise'    => 'Мы поможем внести ясность!',
				'details'    => 'Составим подробную смету строительных затрат, оценим стоимость работ и материалов, чтобы вы могли заранее спланировать бюджет и принимать взвешенные решения.',
				'closing'    => 'Узнайте стоимость строительства еще до его начала!',
				'cta'        => 'Заказать смету',
			),
			'business'   => array(
				'question'   => 'Хотите приобрести девелоперский проект, но не уверены, что он будет финансово выгодным?',
				'intro'      => 'Вы нашли земельный участок, здание или девелоперский проект с инвестиционным потенциалом и до принятия решения хотите понять, сколько будет стоить реализация проекта и какой может быть его рентабельность?',
				'promise'    => 'Мы поможем оценить экономический потенциал проекта!',
				'items'      => array(
					'Рассчитаем планируемые затраты на строительство и реализацию.',
					'Оценим возможные строительные и технические решения.',
					'Сравним альтернативные решения для оптимизации затрат.',
					'Поможем определить общий объем необходимых инвестиций.',
					'Рассчитаем потенциальную рентабельность проекта на основе имеющихся данных и допущений.',
				),
				'closing'    => 'Принимайте инвестиционные решения на основе расчетов, а не догадок!',
				'cta'        => 'Заказать оценку проекта',
			),
		),
	);
}

/**
 * Catalog image keys used by the page (the hero photo).
 *
 * @return array{hero: string}
 */
function construction_estimate_page_image_keys(): array {
	return array(
		'hero' => 'estimate_hero',
	);
}

/**
 * Hero: a core Cover block with the blueprint photo, a "Mist veil" overlay
 * and the "Greyscale" filter (both theme.json presets), so the photo, focal
 * point, overlay, height and text all stay editable in the block editor.
 *
 * Markup mirrors the Cover block's own save output so the editor validates it.
 *
 * @param string $inner Serialized inner blocks (H1 and lead).
 * @param string $alt   Photo alt text.
 */
function construction_estimate_hero_markup( string $inner, string $alt ): string {
	$id    = construction_media_id( 'estimate_hero' );
	$url   = $id > 0 ? (string) wp_get_attachment_url( $id ) : '';
	$attrs = array();
	$image = '';

	if ( '' !== $url ) {
		$attrs = array(
			'url'        => $url,
			'id'         => $id,
			'alt'        => $alt,
			'focalPoint' => array(
				'x' => 0.5,
				'y' => 0.55,
			),
		);
		$image = sprintf(
			'<img class="wp-block-cover__image-background wp-image-%1$d size-full" alt="%2$s" src="%3$s" style="object-position:50%% 55%%" data-object-fit="cover" data-object-position="50%% 55%%"/>',
			$id,
			esc_attr( $alt ),
			esc_url( $url )
		);
	}

	$attrs['minHeight']     = 35;
	$attrs['minHeightUnit'] = 'rem';
	$attrs['gradient']      = 'mist-veil';
	$attrs['isDark']        = false;
	if ( '' !== $url ) {
		$attrs['sizeSlug'] = 'full';
	}
	$attrs['className'] = 'construction-estimate-page__intro';
	$attrs['style']     = array( 'color' => array( 'duotone' => 'var:preset|duotone|greyscale' ) );
	$attrs['layout']    = array( 'type' => 'default' );
	// CLI PHP often encodes 0.55 as 0.55000000000000004; keep the stored attributes clean.
	$precision = ini_get( 'serialize_precision' );
	ini_set( 'serialize_precision', '-1' ); // phpcs:ignore WordPress.PHP.IniSet.Risky
	$json = serialize_block_attributes( $attrs );
	ini_set( 'serialize_precision', (string) $precision ); // phpcs:ignore WordPress.PHP.IniSet.Risky

	return <<<HTML
	<!-- wp:cover {$json} -->
	<div class="wp-block-cover is-light construction-estimate-page__intro" style="min-height:35rem">{$image}<span aria-hidden="true" class="wp-block-cover__background has-background-dim-100 has-background-dim wp-block-cover__gradient-background has-background-gradient has-mist-veil-gradient-background"></span><div class="wp-block-cover__inner-container">
{$inner}
	</div></div>
	<!-- /wp:cover -->
HTML;
}

/**
 * Markup for one half of the split (private or business).
 *
 * @param array<string, mixed> $panel       Panel copy.
 * @param string               $variant     private|business.
 * @param string               $contact_url Escaped contacts URL.
 */
function construction_estimate_panel_markup( array $panel, string $variant, string $contact_url ): string {
	$question = esc_html( (string) $panel['question'] );
	$intro    = esc_html( (string) $panel['intro'] );
	$promise  = esc_html( (string) $panel['promise'] );
	$closing  = esc_html( (string) $panel['closing'] );
	$cta      = esc_html( (string) $panel['cta'] );
	// The button opens the request form (inc/request-form.php) with the matching type.
	$request  = 'private' === $variant ? 'construction-request--estimate' : 'construction-request--assessment';

	$body = '';
	if ( ! empty( $panel['details'] ) ) {
		$details = esc_html( (string) $panel['details'] );
		$body    = <<<HTML
				<!-- wp:paragraph -->
				<p>{$details}</p>
				<!-- /wp:paragraph -->

HTML;
	}
	if ( ! empty( $panel['items'] ) && is_array( $panel['items'] ) ) {
		$items = '';
		foreach ( $panel['items'] as $item ) {
			$item   = esc_html( (string) $item );
			$items .= "<!-- wp:list-item -->\n<li>{$item}</li>\n<!-- /wp:list-item -->";
		}
		$body = <<<HTML
				<!-- wp:list {"className":"construction-estimate-page__list"} -->
				<ul class="wp-block-list construction-estimate-page__list">{$items}</ul>
				<!-- /wp:list -->

HTML;
	}

	return <<<HTML
		<!-- wp:group {"className":"construction-estimate-page__panel construction-estimate-page__panel--{$variant}","layout":{"type":"default"}} -->
		<div class="wp-block-group construction-estimate-page__panel construction-estimate-page__panel--{$variant}">
			<!-- wp:group {"className":"construction-estimate-page__copy","layout":{"type":"default"}} -->
			<div class="wp-block-group construction-estimate-page__copy">
				<!-- wp:heading {"className":"construction-estimate-page__question"} -->
				<h2 class="wp-block-heading construction-estimate-page__question">{$question}</h2>
				<!-- /wp:heading -->

				<!-- wp:paragraph -->
				<p>{$intro}</p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph {"className":"construction-estimate-page__promise"} -->
				<p class="construction-estimate-page__promise">{$promise}</p>
				<!-- /wp:paragraph -->

{$body}				<!-- wp:paragraph {"className":"construction-estimate-page__closing"} -->
				<p class="construction-estimate-page__closing">{$closing}</p>
				<!-- /wp:paragraph -->

				<!-- wp:buttons {"className":"construction-estimate-page__actions"} -->
				<div class="wp-block-buttons construction-estimate-page__actions"><!-- wp:button {"className":"{$request}"} -->
				<div class="wp-block-button {$request}"><a class="wp-block-button__link wp-element-button" href="{$contact_url}">{$cta}</a></div>
				<!-- /wp:button --></div>
				<!-- /wp:buttons -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->

HTML;
}

/**
 * Full page markup for one language.
 */
function construction_estimate_page_content( string $lang ): string {
	$definitions = construction_estimate_page_definitions();
	if ( empty( $definitions[ $lang ] ) ) {
		return '';
	}

	$copy        = $definitions[ $lang ];
	// The H1 breaks into two balanced lines; the page title stays plain text.
	$title       = wp_kses( (string) ( $copy['heading'] ?? $copy['title'] ), array( 'br' => array() ) );
	$lead        = esc_html( (string) $copy['lead'] );
	$contact_url = esc_url( construction_contacts_url_for_lang( $lang ) );
	$private     = construction_estimate_panel_markup( $copy['private'], 'private', $contact_url );
	$business    = construction_estimate_panel_markup( $copy['business'], 'business', $contact_url );
	$hero        = construction_estimate_hero_markup(
		<<<HTML
		<!-- wp:heading {"level":1,"className":"construction-estimate-page__title"} -->
		<h1 class="wp-block-heading construction-estimate-page__title">{$title}</h1>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"className":"construction-estimate-page__lead"} -->
		<p class="construction-estimate-page__lead">{$lead}</p>
		<!-- /wp:paragraph -->
HTML,
		(string) $copy['hero_alt']
	);

	return <<<HTML
<!-- wp:group {"align":"full","className":"construction-estimate-page","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull construction-estimate-page">
{$hero}

	<!-- wp:group {"className":"construction-estimate-page__split","layout":{"type":"default"}} -->
	<div class="wp-block-group construction-estimate-page__split">
{$private}{$business}	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
HTML;
}

/**
 * Current estimate page IDs by language.
 *
 * @return array{lv?:int,en?:int,ru?:int}
 */
function construction_get_estimate_page_ids(): array {
	$stored = get_option( 'construction_estimate_page_ids', array() );
	$ids    = array();

	foreach ( construction_estimate_page_definitions() as $lang => $copy ) {
		if ( is_array( $stored ) && ! empty( $stored[ $lang ] ) && get_post( (int) $stored[ $lang ] ) ) {
			$ids[ $lang ] = (int) $stored[ $lang ];
			continue;
		}

		$found = get_posts(
			array(
				'name'             => (string) $copy['slug'],
				'post_type'        => 'page',
				'post_status'      => 'publish',
				'posts_per_page'   => 1,
				'fields'           => 'ids',
				'suppress_filters' => false,
				'lang'             => '',
			)
		);
		if ( ! empty( $found[0] ) ) {
			$ids[ $lang ] = (int) $found[0];
		}
	}

	return $ids;
}

/**
 * Seed Rank Math fields only where the editor has not filled them yet.
 *
 * @param array<string, string> $seo title/description/keywords.
 */
function construction_estimate_seed_seo( int $page_id, array $seo ): void {
	$fields = array(
		'rank_math_title'         => $seo['title'] ?? '',
		'rank_math_description'   => $seo['description'] ?? '',
		'rank_math_focus_keyword' => $seo['keywords'] ?? '',
	);

	foreach ( $fields as $meta_key => $value ) {
		if ( $value !== '' && (string) get_post_meta( $page_id, $meta_key, true ) === '' ) {
			update_post_meta( $page_id, $meta_key, $value );
		}
	}
}

/**
 * Add the estimate page to each Primary menu, right after the audience pages.
 *
 * Existing items are never removed or renamed.
 *
 * @param array{lv?:int,en?:int,ru?:int} $page_ids Estimate page IDs.
 */
function construction_estimate_add_menu_items( array $page_ids ): void {
	$menus       = array(
		'lv' => 'Primary LV',
		'en' => 'Primary EN',
		'ru' => 'Primary RU',
	);
	$definitions = construction_estimate_page_definitions();
	$audience    = function_exists( 'construction_get_audience_page_ids' ) ? construction_get_audience_page_ids() : array();

	foreach ( $menus as $lang => $menu_name ) {
		if ( empty( $page_ids[ $lang ] ) ) {
			continue;
		}

		$menu = wp_get_nav_menu_object( $menu_name );
		if ( ! $menu ) {
			continue;
		}

		$items = wp_get_nav_menu_items( (int) $menu->term_id );
		$items = is_array( $items ) ? $items : array();

		// Insert after the last audience page item; append when none exist.
		$audience_ids = array_filter(
			array(
				(int) ( $audience['developers'][ $lang ] ?? 0 ),
				(int) ( $audience['homebuilders'][ $lang ] ?? 0 ),
			)
		);
		$position     = count( $items ) + 1;
		$after        = 0;
		foreach ( $items as $item ) {
			if ( 'page' !== $item->object ) {
				continue;
			}
			if ( (int) $item->object_id === (int) $page_ids[ $lang ] ) {
				continue 2; // Already in this menu.
			}
			if ( in_array( (int) $item->object_id, $audience_ids, true ) ) {
				$after = max( $after, (int) $item->menu_order );
			}
		}

		if ( $after > 0 ) {
			$position = $after + 1;
			foreach ( $items as $item ) {
				if ( (int) $item->menu_order >= $position ) {
					wp_update_post(
						array(
							'ID'         => (int) $item->ID,
							'menu_order' => (int) $item->menu_order + 1,
						)
					);
				}
			}
		}

		wp_update_nav_menu_item(
			(int) $menu->term_id,
			0,
			array(
				'menu-item-title'     => (string) $definitions[ $lang ]['menu_label'],
				'menu-item-object'    => 'page',
				'menu-item-object-id' => (int) $page_ids[ $lang ],
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
				'menu-item-position'  => $position,
			)
		);
	}
}

/**
 * Create the linked LV, EN and RU estimate pages.
 *
 * Default mode creates missing pages only. Forced mode deletes and reseeds
 * just the three estimate pages (by stored ID or slug).
 *
 * @return array{lv?:int,en?:int,ru?:int}|WP_Error
 */
function construction_rebuild_polylang_estimate_pages( bool $force = false ) {
	if ( ! function_exists( 'pll_set_post_language' ) || ! function_exists( 'pll_save_post_translations' ) ) {
		return new WP_Error( 'no_polylang', 'Polylang is not active.' );
	}

	$definitions = construction_estimate_page_definitions();
	$keys        = construction_estimate_page_image_keys();
	$media       = construction_import_remote_media(
		array_values( $keys ),
		array( $keys['hero'] => (string) $definitions['lv']['hero_alt'] )
	);
	if ( is_wp_error( $media ) ) {
		return $media;
	}

	$ids = construction_get_estimate_page_ids();
	if ( $force ) {
		foreach ( $ids as $old_id ) {
			wp_delete_post( (int) $old_id, true );
		}
		$ids = array();
	}

	foreach ( $definitions as $lang => $copy ) {
		if ( ! empty( $ids[ $lang ] ) ) {
			continue;
		}

		$id = wp_insert_post(
			array(
				'post_title'   => (string) $copy['title'],
				'post_name'    => (string) $copy['slug'],
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_excerpt' => (string) $copy['lead'],
				'post_content' => construction_estimate_page_content( $lang ),
			),
			true
		);
		if ( is_wp_error( $id ) ) {
			return $id;
		}

		$ids[ $lang ] = (int) $id;
		pll_set_post_language( (int) $id, $lang );
		construction_estimate_seed_seo( (int) $id, $copy['seo'] );
		// Featured image feeds Open Graph / social previews; the page template does not display it.
		set_post_thumbnail( (int) $id, (int) $media[ $keys['hero'] ] );
	}

	if ( count( $ids ) === count( $definitions ) ) {
		pll_save_post_translations( $ids );
	}

	update_option( 'construction_estimate_page_ids', $ids );
	update_option( 'construction_flush_rewrites', '1' );
	construction_estimate_add_menu_items( $ids );

	return $ids;
}

/**
 * The hero photo spans the full viewport; WordPress's default `sizes` caps it
 * at the file width. An image added inside one half spans half the viewport.
 *
 * @param string $filtered_image Image tag.
 * @param string $context        Filter context.
 * @param int    $attachment_id  Attachment ID.
 */
function construction_estimate_image_sizes( string $filtered_image, string $context, int $attachment_id ): string {
	if ( 'the_content' !== $context || $attachment_id <= 0 || ! str_contains( $filtered_image, ' sizes="' ) ) {
		return $filtered_image;
	}

	if ( str_contains( $filtered_image, 'wp-block-cover__image-background' ) && construction_media_id( 'estimate_hero' ) === $attachment_id ) {
		$sizes = '100vw';
	} elseif ( in_array( $attachment_id, array( construction_media_id( 'estimate_private' ), construction_media_id( 'estimate_business' ) ), true ) ) {
		$sizes = '(max-width: 899px) 100vw, 50vw';
	} else {
		return $filtered_image;
	}

	return (string) preg_replace( '/\ssizes="[^"]*"/', ' sizes="' . $sizes . '"', $filtered_image, 1 );
}
add_filter( 'wp_content_img_tag', 'construction_estimate_image_sizes', 10, 3 );
