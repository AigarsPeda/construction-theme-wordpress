<?php
/**
 * Multilingual strings (LV default, EN, RU).
 *
 * @package Construction
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Supported language codes.
 *
 * @return list<string>
 */
function construction_languages(): array {
	return array( 'lv', 'en', 'ru' );
}

/**
 * Current language slug.
 */
function construction_current_lang(): string {
	if ( function_exists( 'pll_current_language' ) ) {
		$lang = pll_current_language( 'slug' );
		if ( is_string( $lang ) && in_array( $lang, construction_languages(), true ) ) {
			return $lang;
		}
	}

	// Fallback: language of the current page/post.
	if ( function_exists( 'pll_get_post_language' ) ) {
		$post_id = get_queried_object_id();
		if ( $post_id > 0 ) {
			$lang = pll_get_post_language( $post_id, 'slug' );
			if ( is_string( $lang ) && in_array( $lang, construction_languages(), true ) ) {
				return $lang;
			}
		}
	}

	if ( isset( $_GET['lang'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$lang = sanitize_key( wp_unslash( $_GET['lang'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( in_array( $lang, construction_languages(), true ) ) {
			return $lang;
		}
	}

	return 'lv';
}

/**
 * URL for a language (translated front page when Polylang is available).
 */
function construction_lang_url( string $lang ): string {
	if ( ! in_array( $lang, construction_languages(), true ) ) {
		$lang = 'lv';
	}

	// Prefer the translated homepage permalink (Sākums / Home / Главная).
	if ( function_exists( 'pll_get_post' ) ) {
		$front_id = (int) get_option( 'page_on_front' );
		if ( $front_id > 0 ) {
			$translated_id = pll_get_post( $front_id, $lang );
			if ( $translated_id ) {
				$url = get_permalink( (int) $translated_id );
				if ( is_string( $url ) && $url !== '' ) {
					return $url;
				}
			}
		}
	}

	if ( function_exists( 'pll_home_url' ) && function_exists( 'pll_languages_list' ) ) {
		$available = pll_languages_list( array( 'fields' => 'slug' ) );
		if ( is_array( $available ) && in_array( $lang, $available, true ) ) {
			$url = pll_home_url( $lang );
			if ( is_string( $url ) && $url !== '' ) {
				return $url;
			}
		}
	}

	$home = home_url( '/' );
	if ( 'lv' === $lang ) {
		return remove_query_arg( 'lang', $home );
	}

	return add_query_arg( 'lang', $lang, $home );
}

/**
 * All theme UI strings.
 *
 * @return array<string, array{lv: string, en: string, ru: string}>
 */
function construction_strings(): array {
	return array(
		'hero.title'           => array(
			'lv' => 'Tava māja — tavi noteikumi',
			'en' => 'Your house — your rules',
			'ru' => 'Твой дом — твои правила',
		),
		'hero.text'            => array(
			'lv' => 'Projektējam un būvējam karkasa mājas pēc jūsu vajadzībām — no idejas līdz atslēgai.',
			'en' => 'We design and build frame houses for your needs — from idea to turnkey.',
			'ru' => 'Проектируем и строим каркасные дома под ваши задачи — от идеи до ключей.',
		),
		'hero.cta'             => array(
			'lv' => 'Iziet testu',
			'en' => 'Take the quiz',
			'ru' => 'Пройти тест',
		),
		'hero.since'           => array(
			'lv' => 'Būvējam karkasa mājas kopš 2008. gada',
			'en' => 'Building frame houses since 2008',
			'ru' => 'Строим каркасные дома с 2008 года',
		),
		'hero.alt'             => array(
			'lv' => 'Modernas karkasa mājas vizuālis',
			'en' => 'Modern frame house visual',
			'ru' => 'Визуал современного каркасного дома',
		),
		'nav.projects'         => array(
			'lv' => 'Projekti',
			'en' => 'Projects',
			'ru' => 'Проекты',
		),
		'nav.photos'           => array(
			'lv' => 'Foto',
			'en' => 'Photos',
			'ru' => 'Фото',
		),
		'nav.about'            => array(
			'lv' => 'Par mums',
			'en' => 'About us',
			'ru' => 'О нас',
		),
		'services.title'       => array(
			'lv' => 'Uzņemamies projektēšanu, dizainu un objekta montāžu',
			'en' => 'We handle design, planning, and installation',
			'ru' => 'Берём на себя проектирование, дизайн и монтаж объекта',
		),
		'services.intro'       => array(
			'lv' => 'No tehniskā projekta līdz fasādes montāžai — viena komanda, skaidri termiņi un caurspīdīgs process.',
			'en' => 'From technical design to façade installation — one team, clear timelines, and a transparent process.',
			'ru' => 'От технического проекта до монтажа фасада — одна команда, понятные сроки и прозрачный процесс.',
		),
		'services.item1.title' => array(
			'lv' => 'Projektēšana',
			'en' => 'Design',
			'ru' => 'Проектирование',
		),
		'services.item1.text'  => array(
			'lv' => 'Aprēķini, tehniskās shēmas un projektēšana no A līdz Z.',
			'en' => 'Calculations, technical drawings, and full design from A to Z.',
			'ru' => 'Расчёты, технические схемы и проектирование от А до Я.',
		),
		'services.item2.title' => array(
			'lv' => 'Fasāžu dizains',
			'en' => 'Façade design',
			'ru' => 'Дизайн фасадов',
		),
		'services.item2.text'  => array(
			'lv' => 'Ventilējamās fasādes ar arhitektūras vizualizāciju.',
			'en' => 'Ventilated façades with architectural visualization.',
			'ru' => 'Вентилируемые фасады с архитектурной визуализацией.',
		),
		'services.item3.title' => array(
			'lv' => 'Fasāžu montāža',
			'en' => 'Façade installation',
			'ru' => 'Монтаж фасадов',
		),
		'services.item3.text'  => array(
			'lv' => 'Profesionāla montāža un apdares materiālu uzstādīšana.',
			'en' => 'Professional installation and finishing materials.',
			'ru' => 'Профессиональный монтаж и установка отделочных материалов.',
		),
		'quality.title'        => array(
			'lv' => 'Ātri termiņi un augsta darba kvalitāte',
			'en' => 'Fast timelines and high-quality work',
			'ru' => 'Быстрые сроки и высокое качество работы',
		),
		'quality.item1'        => array(
			'lv' => 'Augsti kvalitātes standarti',
			'en' => 'High quality standards',
			'ru' => 'Высокие стандарты качества',
		),
		'quality.item2'        => array(
			'lv' => 'Apmierināti klienti',
			'en' => 'Satisfied clients',
			'ru' => 'Довольные клиенты',
		),
		'quality.item3'        => array(
			'lv' => 'Individuāli projekti',
			'en' => 'Individual projects',
			'ru' => 'Индивидуальные проекты',
		),
		'quality.item4'        => array(
			'lv' => 'Būvniecības brigādes',
			'en' => 'Construction crews',
			'ru' => 'Строительные бригады',
		),
		'reviews.title'        => array(
			'lv' => 'Galvenais kvalitātes rādītājs — apmierināts klients',
			'en' => 'The main measure of our quality is a satisfied client',
			'ru' => 'Главный показатель качества нашей работы — довольный клиент',
		),
		'reviews.avg'          => array(
			'lv' => 'Vidējais vērtējums',
			'en' => 'Average rating',
			'ru' => 'Средняя оценка',
		),
		'reviews.count'        => array(
			'lv' => '48 atsauksmes · Google / Facebook',
			'en' => '48 reviews · Google / Facebook',
			'ru' => '48 отзывов · Google / Facebook',
		),
		'reviews.1'            => array(
			'lv' => 'Profesionāla komanda, skaidri termiņi un skaists rezultāts. Iesakām!',
			'en' => 'Professional team, clear deadlines, and a beautiful result. Highly recommend!',
			'ru' => 'Профессиональная команда, понятные сроки и отличный результат. Рекомендуем!',
		),
		'reviews.2'            => array(
			'lv' => 'No projekta līdz montāžai viss noritēja raiti. Māja izdevās tieši tā, kā gribējām.',
			'en' => 'From design to installation everything went smoothly. The house turned out exactly as we wanted.',
			'ru' => 'От проекта до монтажа всё прошло гладко. Дом получился именно таким, как хотели.',
		),
		'reviews.3'            => array(
			'lv' => 'Lieliska komunikācija un kvalitatīva fasāde. Paldies Construction komandai.',
			'en' => 'Great communication and a high-quality façade. Thanks to the Construction team.',
			'ru' => 'Отличная коммуникация и качественный фасад. Спасибо команде Construction.',
		),
		'reviews.4'            => array(
			'lv' => 'Ātri, kārtīgi un bez pārsteigumiem budžetā. Noteikti strādāsim atkal.',
			'en' => 'Fast, tidy, and no budget surprises. We will definitely work together again.',
			'ru' => 'Быстро, аккуратно и без сюрпризов по бюджету. Обязательно обратимся снова.',
		),
		'contact.label'        => array(
			'lv' => 'Sazināties',
			'en' => 'Get in touch',
			'ru' => 'Связаться',
		),
		'contact.placeholder'  => array(
			'lv' => 'e-pasts / numurs',
			'en' => 'email / phone',
			'ru' => 'email / телефон',
		),
		'contact.submit'       => array(
			'lv' => 'Nosūtīt',
			'en' => 'Send',
			'ru' => 'Отправить',
		),
		'contact.field'        => array(
			'lv' => 'E-pasts / tālrunis',
			'en' => 'Email / phone',
			'ru' => 'Email / телефон',
		),
		'credits.title'        => array(
			'lv' => 'Foto kredīti (Unsplash)',
			'en' => 'Photo credits (Unsplash)',
			'ru' => 'Фото кредиты (Unsplash)',
		),
		'lang.label'           => array(
			'lv' => 'Valoda',
			'en' => 'Language',
			'ru' => 'Язык',
		),
		'back.top'             => array(
			'lv' => 'Augšā',
			'en' => 'Back to top',
			'ru' => 'Наверх',
		),
	);
}

/**
 * Translate a UI string by key for the current language.
 */
function construction_t( string $key ): string {
	$strings = construction_strings();
	$lang    = construction_current_lang();

	if ( isset( $strings[ $key ][ $lang ] ) ) {
		return $strings[ $key ][ $lang ];
	}

	if ( isset( $strings[ $key ]['en'] ) ) {
		return $strings[ $key ]['en'];
	}

	return $key;
}
