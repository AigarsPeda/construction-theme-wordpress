<?php
/**
 * Audience landing pages as editable Gutenberg blocks.
 *
 * @package Construction
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Page copy and routes for each audience and language.
 *
 * WordPress page content remains the source of truth after the initial seed.
 *
 * @return array<string, array<string, mixed>>
 */
function construction_audience_page_definitions(): array {
	return array(
		'developers'   => array(
			'primary_image'   => 'project_1',
			'secondary_image' => 'project_4',
			'lv'              => array(
				'title'       => 'Projektu attīstītājiem',
				'slug'        => 'projektu-attistitajiem',
				'lead'        => 'Palīdzam vadīt būvniecības projektu no sākotnējās ieceres līdz gatava objekta nodošanai.',
				'intro_title' => 'Viens vadības punkts visam projektam',
				'intro_text'  => 'Pārstāvam pasūtītāja intereses, koordinējam iesaistītās puses un sekojam līdzi termiņiem, izmaksām un darbu kvalitātei.',
				'process'     => 'Kā virzām projektu uz priekšu',
				'steps'       => array(
					array( 'Iecere un plāns', 'Precizējam projekta mērķi, darbu apjomu, galvenos posmus un budžeta ietvaru.' ),
					array( 'Projektēšana un iepirkumi', 'Koordinējam projektētājus, sagatavojam darbu iepirkumus un palīdzam salīdzināt piedāvājumus.' ),
					array( 'Būvdarbu vadība', 'Organizējam darbu secību, sapulces un lēmumus, lai projektā nebūtu nevajadzīgu paužu.' ),
					array( 'Kvalitāte un nodošana', 'Kontrolējam izpildi, dokumentāciju un objekta sagatavošanu nodošanai.' ),
				),
				'scope_title' => 'Ko varam uzņemties',
				'scope'       => array(
					'Pasūtītāja interešu pārstāvība',
					'Būvniecības procesa un termiņu koordinēšana',
					'Budžeta un izmaiņu uzskaite',
					'Būvdarbu kvalitātes kontrole',
					'Nodošanas dokumentācijas koordinēšana',
				),
				'cta_title'   => 'Plānojat jaunu projektu?',
				'cta_text'    => 'Izstāstiet par ieceri. Vienosimies par projekta posmu, kurā mūsu iesaiste dos lielāko vērtību.',
				'cta_label'   => 'Pārrunāt projektu',
				'image_alt'   => 'Būvniecības projekta vadība un koordinēšana',
			),
			'en'              => array(
				'title'       => 'For property developers',
				'slug'        => 'for-property-developers',
				'lead'        => 'We help manage construction projects from the first concept through to a completed handover.',
				'intro_title' => 'One management point for the whole project',
				'intro_text'  => 'We represent the client, coordinate the project team, and keep track of schedules, costs, and construction quality.',
				'process'     => 'How we move the project forward',
				'steps'       => array(
					array( 'Brief and plan', 'We define the project goals, scope, main stages, and budget framework.' ),
					array( 'Design and procurement', 'We coordinate designers, prepare work packages, and help compare contractor proposals.' ),
					array( 'Construction management', 'We organise work sequences, meetings, and decisions to keep the project moving.' ),
					array( 'Quality and handover', 'We monitor delivery, documentation, and preparation of the completed property for handover.' ),
				),
				'scope_title' => 'What we can manage',
				'scope'       => array(
					'Representing the client throughout the project',
					'Coordinating the construction process and schedule',
					'Tracking budgets and changes',
					'Monitoring construction quality',
					'Coordinating handover documentation',
				),
				'cta_title'   => 'Planning a new development?',
				'cta_text'    => 'Tell us about the project. We will identify where our involvement can add the most value.',
				'cta_label'   => 'Discuss your project',
				'image_alt'   => 'Construction project management and coordination',
			),
			'ru'              => array(
				'title'       => 'Для девелоперов',
				'slug'        => 'dlya-developerov',
				'lead'        => 'Помогаем управлять строительным проектом от первоначальной идеи до сдачи готового объекта.',
				'intro_title' => 'Единый центр управления проектом',
				'intro_text'  => 'Представляем интересы заказчика, координируем участников и контролируем сроки, затраты и качество работ.',
				'process'     => 'Как мы ведем проект',
				'steps'       => array(
					array( 'Задача и план', 'Уточняем цели проекта, объем работ, основные этапы и рамки бюджета.' ),
					array( 'Проектирование и закупки', 'Координируем проектировщиков, готовим пакеты работ и помогаем сравнивать предложения подрядчиков.' ),
					array( 'Управление строительством', 'Организуем последовательность работ, совещания и принятие решений, чтобы проект двигался без лишних пауз.' ),
					array( 'Качество и сдача', 'Контролируем выполнение работ, документацию и подготовку готового объекта к сдаче.' ),
				),
				'scope_title' => 'Что мы можем взять на себя',
				'scope'       => array(
					'Представление интересов заказчика',
					'Координация строительного процесса и сроков',
					'Учет бюджета и изменений',
					'Контроль качества строительных работ',
					'Координация документации для сдачи объекта',
				),
				'cta_title'   => 'Планируете новый проект?',
				'cta_text'    => 'Расскажите о проекте. Мы определим этап, на котором наше участие принесет наибольшую пользу.',
				'cta_label'   => 'Обсудить проект',
				'image_alt'   => 'Управление и координация строительного проекта',
			),
		),
		'homebuilders' => array(
			'primary_image'   => 'service_2',
			'secondary_image' => 'service_3',
			'lv'              => array(
				'title'       => 'Privātmāju būvētājiem',
				'slug'        => 'privatmaju-buvetajiem',
				'lead'        => 'Palīdzam privātmājas būvniecību pārvērst skaidrā un kontrolējamā procesā.',
				'intro_title' => 'Jūsu intereses būvlaukumā',
				'intro_text'  => 'Izskaidrojam izvēles, pārbaudām darbu kvalitāti un koordinējam būvniekus, lai jums nav jāvada katra detaļa pašiem.',
				'process'     => 'Ceļš līdz gatavai mājai',
				'steps'       => array(
					array( 'Vajadzības un budžets', 'Sakārtojam prioritātes, vēlamo rezultātu un reālistisku darbu apjomu.' ),
					array( 'Projekts un tāme', 'Palīdzam pārskatīt risinājumus, tāmes un piedāvājumus pirms darbu sākuma.' ),
					array( 'Būvniecības uzraudzība', 'Regulāri pārbaudām progresu, kvalitāti un atbilstību projektam.' ),
					array( 'Pieņemšana un nodošana', 'Fiksējam nepabeigtos darbus un palīdzam sagatavot māju lietošanas uzsākšanai.' ),
				),
				'scope_title' => 'Atbalsts svarīgākajos lēmumos',
				'scope'       => array(
					'Projekta un būvniecības risinājumu izvērtēšana',
					'Tāmju un būvnieku piedāvājumu pārbaude',
					'Darbu grafika un izmaiņu koordinēšana',
					'Kvalitātes pārbaudes būvlaukumā',
					'Defektu un nepabeigto darbu fiksēšana',
				),
				'cta_title'   => 'Gatavojaties būvēt privātmāju?',
				'cta_text'    => 'Pārrunāsim jūsu ieceri, būvniecības posmu un praktisko atbalstu, kas vajadzīgs tieši jums.',
				'cta_label'   => 'Pārrunāt ieceri',
				'image_alt'   => 'Privātmājas būvniecības vadība un uzraudzība',
			),
			'en'              => array(
				'title'       => 'For private home builders',
				'slug'        => 'for-private-home-builders',
				'lead'        => 'We turn the construction of a private home into a clear and controlled process.',
				'intro_title' => 'Your interests represented on site',
				'intro_text'  => 'We explain the choices, inspect the work, and coordinate contractors so you do not have to manage every detail yourself.',
				'process'     => 'The path to a completed home',
				'steps'       => array(
					array( 'Needs and budget', 'We organise priorities, define the intended result, and establish a realistic scope of work.' ),
					array( 'Design and estimate', 'We help review proposed solutions, estimates, and contractor offers before work begins.' ),
					array( 'Construction supervision', 'We regularly check progress, quality, and compliance with the project.' ),
					array( 'Inspection and handover', 'We record outstanding work and help prepare the home for occupation.' ),
				),
				'scope_title' => 'Support for the decisions that matter',
				'scope'       => array(
					'Reviewing the design and construction solutions',
					'Checking estimates and contractor proposals',
					'Coordinating the work schedule and changes',
					'Quality inspections on site',
					'Recording defects and outstanding work',
				),
				'cta_title'   => 'Preparing to build a private home?',
				'cta_text'    => 'Let us discuss your plans, the current stage, and the practical support you need.',
				'cta_label'   => 'Discuss your plans',
				'image_alt'   => 'Private home construction management and supervision',
			),
			'ru'              => array(
				'title'       => 'Для застройщиков частных домов',
				'slug'        => 'dlya-zastroyshchikov-chastnyh-domov',
				'lead'        => 'Помогаем сделать строительство частного дома понятным и контролируемым процессом.',
				'intro_title' => 'Ваши интересы на строительной площадке',
				'intro_text'  => 'Объясняем варианты, проверяем качество работ и координируем подрядчиков, чтобы вам не приходилось управлять каждой деталью.',
				'process'     => 'Путь к готовому дому',
				'steps'       => array(
					array( 'Потребности и бюджет', 'Определяем приоритеты, желаемый результат и реалистичный объем работ.' ),
					array( 'Проект и смета', 'Помогаем проверить решения, сметы и предложения подрядчиков до начала работ.' ),
					array( 'Надзор за строительством', 'Регулярно проверяем ход работ, качество и соответствие проекту.' ),
					array( 'Приемка и сдача', 'Фиксируем незавершенные работы и помогаем подготовить дом к эксплуатации.' ),
				),
				'scope_title' => 'Поддержка в важных решениях',
				'scope'       => array(
					'Оценка проектных и строительных решений',
					'Проверка смет и предложений подрядчиков',
					'Координация графика работ и изменений',
					'Проверка качества на строительной площадке',
					'Фиксация дефектов и незавершенных работ',
				),
				'cta_title'   => 'Планируете строить частный дом?',
				'cta_text'    => 'Обсудим вашу идею, текущий этап строительства и практическую помощь, которая нужна именно вам.',
				'cta_label'   => 'Обсудить идею',
				'image_alt'   => 'Управление и надзор за строительством частного дома',
			),
		),
	);
}

/**
 * Build one audience page from native Gutenberg blocks.
 */
function construction_audience_page_content( string $audience, string $lang ): string {
	$definitions = construction_audience_page_definitions();
	if ( empty( $definitions[ $audience ][ $lang ] ) ) {
		return '';
	}

	$definition = $definitions[ $audience ];
	$copy       = $definition[ $lang ];
	$title      = esc_html( (string) $copy['title'] );
	$lead       = esc_html( (string) $copy['lead'] );
	$intro_title = esc_html( (string) $copy['intro_title'] );
	$intro_text = esc_html( (string) $copy['intro_text'] );
	$process    = esc_html( (string) $copy['process'] );
	$scope_title = esc_html( (string) $copy['scope_title'] );
	$cta_title  = esc_html( (string) $copy['cta_title'] );
	$cta_text   = esc_html( (string) $copy['cta_text'] );
	$cta_label  = esc_html( (string) $copy['cta_label'] );
	$image_alt  = (string) $copy['image_alt'];
	$contact_url = esc_url( construction_contacts_url_for_lang( $lang ) );

	$primary_image = construction_media_image_block(
		(string) $definition['primary_image'],
		'construction-audience-page__image construction-audience-page__image--hero',
		$image_alt,
		'large'
	);
	$secondary_image = construction_media_image_block(
		(string) $definition['secondary_image'],
		'construction-audience-page__image construction-audience-page__image--detail',
		$image_alt,
		'large'
	);

	$steps = '';
	foreach ( $copy['steps'] as $index => $step ) {
		$number     = esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) );
		$step_title = esc_html( (string) $step[0] );
		$step_text  = esc_html( (string) $step[1] );
		$steps     .= <<<HTML
			<!-- wp:group {"className":"construction-audience-page__step","layout":{"type":"default"}} -->
			<div class="wp-block-group construction-audience-page__step">
				<!-- wp:paragraph {"className":"construction-audience-page__step-number"} -->
				<p class="construction-audience-page__step-number">{$number}</p>
				<!-- /wp:paragraph -->
				<!-- wp:heading {"level":3,"className":"construction-audience-page__step-title"} -->
				<h3 class="wp-block-heading construction-audience-page__step-title">{$step_title}</h3>
				<!-- /wp:heading -->
				<!-- wp:paragraph {"className":"construction-audience-page__step-text"} -->
				<p class="construction-audience-page__step-text">{$step_text}</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->

HTML;
	}

	$scope_items = '';
	foreach ( $copy['scope'] as $item ) {
		$scope_items .= '<li>' . esc_html( (string) $item ) . '</li>';
	}

	return <<<HTML
<!-- wp:group {"align":"full","className":"construction-audience-page construction-audience-page--{$audience}","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull construction-audience-page construction-audience-page--{$audience}">
	<!-- wp:columns {"className":"construction-audience-page__hero"} -->
	<div class="wp-block-columns construction-audience-page__hero">
		<!-- wp:column {"width":"48%","className":"construction-audience-page__hero-copy"} -->
		<div class="wp-block-column construction-audience-page__hero-copy" style="flex-basis:48%">
			<!-- wp:heading {"level":1,"className":"construction-audience-page__title"} -->
			<h1 class="wp-block-heading construction-audience-page__title">{$title}</h1>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"className":"construction-audience-page__lead"} -->
			<p class="construction-audience-page__lead">{$lead}</p>
			<!-- /wp:paragraph -->
			<!-- wp:buttons {"className":"construction-audience-page__actions"} -->
			<div class="wp-block-buttons construction-audience-page__actions">
				<!-- wp:button -->
				<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="{$contact_url}">{$cta_label}</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column {"width":"52%","className":"construction-audience-page__hero-media"} -->
		<div class="wp-block-column construction-audience-page__hero-media" style="flex-basis:52%">
{$primary_image}		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->

	<!-- wp:group {"className":"construction-audience-page__intro","layout":{"type":"default"}} -->
	<div class="wp-block-group construction-audience-page__intro">
		<!-- wp:heading {"level":2} -->
		<h2 class="wp-block-heading">{$intro_title}</h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph -->
		<p>{$intro_text}</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"align":"full","className":"construction-audience-page__process","layout":{"type":"default"}} -->
	<div class="wp-block-group alignfull construction-audience-page__process">
		<!-- wp:group {"className":"construction-audience-page__section-inner","layout":{"type":"default"}} -->
		<div class="wp-block-group construction-audience-page__section-inner">
			<!-- wp:heading {"level":2,"className":"construction-audience-page__section-title"} -->
			<h2 class="wp-block-heading construction-audience-page__section-title">{$process}</h2>
			<!-- /wp:heading -->
			<!-- wp:group {"className":"construction-audience-page__steps","layout":{"type":"default"}} -->
			<div class="wp-block-group construction-audience-page__steps">
{$steps}			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->

	<!-- wp:columns {"className":"construction-audience-page__scope"} -->
	<div class="wp-block-columns construction-audience-page__scope">
		<!-- wp:column {"width":"48%","className":"construction-audience-page__scope-media"} -->
		<div class="wp-block-column construction-audience-page__scope-media" style="flex-basis:48%">
{$secondary_image}		</div>
		<!-- /wp:column -->
		<!-- wp:column {"width":"52%","className":"construction-audience-page__scope-copy"} -->
		<div class="wp-block-column construction-audience-page__scope-copy" style="flex-basis:52%">
			<!-- wp:heading {"level":2} -->
			<h2 class="wp-block-heading">{$scope_title}</h2>
			<!-- /wp:heading -->
			<!-- wp:list {"className":"construction-audience-page__scope-list"} -->
			<ul class="wp-block-list construction-audience-page__scope-list">{$scope_items}</ul>
			<!-- /wp:list -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->

	<!-- wp:group {"className":"construction-audience-page__cta","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
	<div class="wp-block-group construction-audience-page__cta">
		<!-- wp:group {"className":"construction-audience-page__cta-copy","layout":{"type":"default"}} -->
		<div class="wp-block-group construction-audience-page__cta-copy">
			<!-- wp:heading {"level":2} -->
			<h2 class="wp-block-heading">{$cta_title}</h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p>{$cta_text}</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
		<!-- wp:buttons -->
		<div class="wp-block-buttons">
			<!-- wp:button {"backgroundColor":"base","textColor":"contrast"} -->
			<div class="wp-block-button"><a class="wp-block-button__link has-contrast-color has-base-background-color has-text-color has-background wp-element-button" href="{$contact_url}">{$cta_label}</a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
HTML;
}

/**
 * Find current audience page IDs, grouped by audience and language.
 *
 * @return array<string, array{lv?:int,en?:int,ru?:int}>
 */
function construction_get_audience_page_ids(): array {
	$stored = get_option( 'construction_audience_page_ids', array() );
	$ids    = array();
	if ( is_array( $stored ) ) {
		foreach ( array( 'developers', 'homebuilders' ) as $audience ) {
			foreach ( construction_languages() as $lang ) {
				if ( ! empty( $stored[ $audience ][ $lang ] ) && get_post( (int) $stored[ $audience ][ $lang ] ) ) {
					$ids[ $audience ][ $lang ] = (int) $stored[ $audience ][ $lang ];
				}
			}
		}
	}

	$definitions = construction_audience_page_definitions();
	foreach ( $definitions as $audience => $definition ) {
		foreach ( construction_languages() as $lang ) {
			if ( ! empty( $ids[ $audience ][ $lang ] ) ) {
				continue;
			}
			$found = get_posts(
				array(
					'name'             => (string) $definition[ $lang ]['slug'],
					'post_type'        => 'page',
					'post_status'      => 'publish',
					'posts_per_page'   => 1,
					'fields'           => 'ids',
					'suppress_filters' => false,
				)
			);
			if ( ! empty( $found[0] ) ) {
				$ids[ $audience ][ $lang ] = (int) $found[0];
			}
		}
	}

	return $ids;
}

/**
 * Find pages owned by this audience-page seed.
 *
 * @return list<int>
 */
function construction_find_audience_page_candidate_ids(): array {
	$ids = array();
	foreach ( construction_audience_page_definitions() as $definition ) {
		foreach ( construction_languages() as $lang ) {
			$found = get_posts(
				array(
					'name'           => (string) $definition[ $lang ]['slug'],
					'post_type'      => 'page',
					'post_status'    => array( 'publish', 'draft', 'trash', 'private' ),
					'posts_per_page' => 10,
					'fields'         => 'ids',
				)
			);
			$ids = array_merge( $ids, $found );
		}
	}

	return array_values( array_unique( array_map( 'intval', $ids ) ) );
}

/**
 * Create linked LV, EN, and RU audience pages.
 *
 * The default mode creates missing pages only. Forced mode replaces only pages
 * that use the six audience slugs declared above.
 *
 * @return array<string, array{lv?:int,en?:int,ru?:int}>|WP_Error
 */
function construction_rebuild_polylang_audience_pages( bool $force = false ) {
	if ( ! function_exists( 'pll_set_post_language' ) || ! function_exists( 'pll_save_post_translations' ) ) {
		return new WP_Error( 'no_polylang', 'Polylang is not active.' );
	}

	$media = construction_import_media_library();
	if ( is_wp_error( $media ) ) {
		return $media;
	}
	if ( ! empty( $media['missing'] ) ) {
		return new WP_Error( 'missing_images', 'Missing source images: ' . implode( ', ', $media['missing'] ) );
	}

	if ( $force ) {
		foreach ( construction_find_audience_page_candidate_ids() as $old_id ) {
			wp_delete_post( $old_id, true );
		}
		$ids = array();
	} else {
		$ids = construction_get_audience_page_ids();
	}

	foreach ( construction_audience_page_definitions() as $audience => $definition ) {
		foreach ( construction_languages() as $lang ) {
			if ( ! empty( $ids[ $audience ][ $lang ] ) && get_post( (int) $ids[ $audience ][ $lang ] ) ) {
				continue;
			}

			$copy = $definition[ $lang ];
			$id   = wp_insert_post(
				array(
					'post_title'   => (string) $copy['title'],
					'post_name'    => (string) $copy['slug'],
					'post_status'  => 'publish',
					'post_type'    => 'page',
					'post_excerpt' => (string) $copy['lead'],
					'post_content' => construction_audience_page_content( (string) $audience, $lang ),
				),
				true
			);
			if ( is_wp_error( $id ) ) {
				return $id;
			}

			$ids[ $audience ][ $lang ] = (int) $id;
			pll_set_post_language( (int) $id, $lang );
		}

		if ( count( $ids[ $audience ] ?? array() ) === 3 ) {
			pll_save_post_translations( $ids[ $audience ] );
		}
	}

	update_option( 'construction_audience_page_ids', $ids );
	update_option( 'construction_flush_rewrites', '1' );

	return $ids;
}
