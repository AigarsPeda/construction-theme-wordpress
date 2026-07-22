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
 * URL for a language (translated current page, else front page).
 */
function construction_lang_url( string $lang ): string {
	if ( ! in_array( $lang, construction_languages(), true ) ) {
		$lang = 'lv';
	}

	// Prefer the translated version of the current page (e.g. Projects).
	if ( function_exists( 'pll_get_post' ) ) {
		$current_id = get_queried_object_id();
		if ( $current_id > 0 ) {
			$translated_id = pll_get_post( $current_id, $lang );
			if ( $translated_id ) {
				$url = get_permalink( (int) $translated_id );
				if ( is_string( $url ) && $url !== '' ) {
					return $url;
				}
			}
		}

		// Fallback: translated homepage (Sākums / Home / Главная).
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
			'lv' => 'Sazinies ar mums',
			'en' => 'Contact us',
			'ru' => 'Связаться с нами',
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
		'nav.contact'          => array(
			'lv' => 'Kontakti',
			'en' => 'Contact',
			'ru' => 'Контакты',
		),
		'nav.menu'             => array(
			'lv' => 'Izvēlne',
			'en' => 'Menu',
			'ru' => 'Меню',
		),
		'nav.close'            => array(
			'lv' => 'Aizvērt',
			'en' => 'Close',
			'ru' => 'Закрыть',
		),
		'nav.language'         => array(
			'lv' => 'Valoda',
			'en' => 'Language',
			'ru' => 'Язык',
		),
		'seo.home.title'       => array(
			'lv' => 'Karkasa mājas — projektēšana un būvniecība',
			'en' => 'Frame houses — design and construction',
			'ru' => 'Каркасные дома — проектирование и строительство',
		),
		'seo.home.desc'        => array(
			'lv' => 'Projektējam un būvējam karkasa mājas Latvijā — no idejas līdz atslēgai. Skaidri termiņi, kvalitāte un viena komanda.',
			'en' => 'We design and build frame houses in Latvia — from idea to turnkey. Clear timelines, quality, and one dedicated team.',
			'ru' => 'Проектируем и строим каркасные дома в Латвии — от идеи до ключей. Понятные сроки, качество и одна команда.',
		),
		'seo.projects.title'   => array(
			'lv' => 'Projekti un galerija',
			'en' => 'Projects and gallery',
			'ru' => 'Проекты и галерея',
		),
		'seo.projects.desc'    => array(
			'lv' => 'Apskati mūsu projektu galeriju — karkasa mājas, būvlaukumi un gatavie objekti.',
			'en' => 'Browse our project gallery — frame houses, construction sites, and finished builds.',
			'ru' => 'Смотрите галерею проектов — каркасные дома, стройки и готовые объекты.',
		),
		'projects.eyebrow'     => array(
			'lv' => 'Galerija',
			'en' => 'Gallery',
			'ru' => 'Галерея',
		),
		'projects.title'       => array(
			'lv' => 'Mūsu projekti',
			'en' => 'Our projects',
			'ru' => 'Наши проекты',
		),
		'projects.intro'       => array(
			'lv' => 'Atlasīti objekti un būvniecības mirkļi — no karkasa līdz gatavai mājai.',
			'en' => 'Selected builds and construction moments — from frame to finished home.',
			'ru' => 'Избранные объекты и моменты строительства — от каркаса до готового дома.',
		),
		'projects.cta'         => array(
			'lv' => 'Sazinies ar mums',
			'en' => 'Contact us',
			'ru' => 'Связаться с нами',
		),
		'projects.alt.1'       => array(
			'lv' => 'Būvlaukums ar celtņiem',
			'en' => 'Construction site with cranes',
			'ru' => 'Стройплощадка с кранами',
		),
		'projects.alt.2'       => array(
			'lv' => 'Modernas mājas fasāde',
			'en' => 'Modern house façade',
			'ru' => 'Фасад современного дома',
		),
		'projects.alt.3'       => array(
			'lv' => 'Būvniecības process',
			'en' => 'Construction in progress',
			'ru' => 'Процесс строительства',
		),
		'projects.alt.4'       => array(
			'lv' => 'Mājas eksterjers',
			'en' => 'House exterior',
			'ru' => 'Экстерьер дома',
		),
		'projects.alt.5'       => array(
			'lv' => 'Arhitektūras detaļa',
			'en' => 'Architectural detail',
			'ru' => 'Архитектурная деталь',
		),
		'projects.alt.6'       => array(
			'lv' => 'Karkasa konstrukcija',
			'en' => 'Frame structure',
			'ru' => 'Каркасная конструкция',
		),
		'projects.alt.7'       => array(
			'lv' => 'Interjera / objekta skats',
			'en' => 'Interior or site view',
			'ru' => 'Интерьер или вид объекта',
		),
		'projects.alt.8'       => array(
			'lv' => 'Būvniecības darbi',
			'en' => 'Building works',
			'ru' => 'Строительные работы',
		),
		'projects.alt.9'       => array(
			'lv' => 'Mājokļa vizuālis',
			'en' => 'Home visual',
			'ru' => 'Визуал жилья',
		),
		'projects.alt.10'      => array(
			'lv' => 'Lielais būvobjekts',
			'en' => 'Large construction project',
			'ru' => 'Крупный строительный объект',
		),
		'projects.alt.11'      => array(
			'lv' => 'Objekta atmosfēra',
			'en' => 'Site atmosphere',
			'ru' => 'Атмосфера объекта',
		),
		'projects.alt.12'      => array(
			'lv' => 'Apdare un montāža',
			'en' => 'Finishing and installation',
			'ru' => 'Отделка и монтаж',
		),
		'projects.alt.13'      => array(
			'lv' => 'Gatavs projekts',
			'en' => 'Finished project',
			'ru' => 'Готовый проект',
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
		'process.eyebrow'      => array(
			'lv' => 'Process',
			'en' => 'Process',
			'ru' => 'Процесс',
		),
		'process.title'        => array(
			'lv' => 'Kā mēs strādājam',
			'en' => 'How we work',
			'ru' => 'Как мы работаем',
		),
		'process.intro'        => array(
			'lv' => 'Skaidrs ceļš no pirmā zvana līdz nodošanai — bez minējumiem un slēptām izmaksām.',
			'en' => 'A clear path from the first call to handover — no guesswork and no hidden costs.',
			'ru' => 'Понятный путь от первого звонка до сдачи объекта — без догадок и скрытых расходов.',
		),
		'process.1.title'      => array(
			'lv' => 'Konsultācija',
			'en' => 'Consultation',
			'ru' => 'Консультация',
		),
		'process.1.text'       => array(
			'lv' => 'Pastāstiet par objektu, mērķiem un budžetu. Mēs ieskicējam apjomu un nākamos soļus.',
			'en' => 'Tell us about the site, goals, and budget. We outline the scope and next steps.',
			'ru' => 'Расскажите об объекте, целях и бюджете. Мы определяем объём работ и следующие шаги.',
		),
		'process.2.title'      => array(
			'lv' => 'Projekts un tāme',
			'en' => 'Design & estimate',
			'ru' => 'Проект и смета',
		),
		'process.2.text'       => array(
			'lv' => 'Tehniskie zīmējumi, materiāli un caurspīdīga tāme — pirms darba sākuma.',
			'en' => 'Technical drawings, materials, and a transparent cost plan — before work begins.',
			'ru' => 'Технические чертежи, материалы и прозрачная смета — до начала работ.',
		),
		'process.3.title'      => array(
			'lv' => 'Būvniecība',
			'en' => 'Construction',
			'ru' => 'Строительство',
		),
		'process.3.text'       => array(
			'lv' => 'Brigādes objektā ar grafika atjauninājumiem un kvalitātes pārbaudēm katrā posmā.',
			'en' => 'Crews on site with schedule updates and quality checks at every stage.',
			'ru' => 'Бригады на объекте с обновлениями графика и контролем качества на каждом этапе.',
		),
		'process.4.title'      => array(
			'lv' => 'Nodošana',
			'en' => 'Handover',
			'ru' => 'Сдача объекта',
		),
		'process.4.text'       => array(
			'lv' => 'Gala apskate, dokumentācija un garantijas atbalsts pēc darba pabeigšanas.',
			'en' => 'Final walkthrough, documentation, and warranty support after completion.',
			'ru' => 'Финальный осмотр, документация и гарантийная поддержка после завершения работ.',
		),
		'faq.eyebrow'          => array(
			'lv' => 'Jautājumi',
			'en' => 'FAQ',
			'ru' => 'Вопросы',
		),
		'faq.title'            => array(
			'lv' => 'Biežāk uzdotie jautājumi',
			'en' => 'Frequently asked questions',
			'ru' => 'Частые вопросы',
		),
		'faq.intro'            => array(
			'lv' => 'Īsas atbildes par termiņiem, budžetu un to, kā sākt sadarbību.',
			'en' => 'Short answers about timelines, budget, and how to get started.',
			'ru' => 'Краткие ответы о сроках, бюджете и том, как начать сотрудничество.',
		),
		'faq.1.q'              => array(
			'lv' => 'Cik ilgi parasti ilgst projekts?',
			'en' => 'How long does a typical project take?',
			'ru' => 'Сколько обычно длится проект?',
		),
		'faq.1.a'              => array(
			'lv' => 'Termiņi ir atkarīgi no apjoma. Fasādes darbi bieži aizņem vairākas nedēļas; pilna būve tiek plānota posmos ar skaidriem atskaites punktiem līgumā.',
			'en' => 'Timelines depend on scope. A façade project often takes several weeks; full builds are planned in phases with clear milestones in the contract.',
			'ru' => 'Сроки зависят от объёма. Фасадные работы часто занимают несколько недель; полное строительство планируется этапами с понятными вехами в договоре.',
		),
		'faq.2.q'              => array(
			'lv' => 'Kur jūs strādājat?',
			'en' => 'Where do you work?',
			'ru' => 'Где вы работаете?',
		),
		'faq.2.a'              => array(
			'lv' => 'Galvenokārt Latvijā. Pierakstiet atrašanās vietu pieteikumā — apstiprināsim, vai varam uzņemties projektu.',
			'en' => 'Primarily in Latvia. Include your location in the enquiry and we will confirm availability.',
			'ru' => 'В основном в Латвии. Укажите локацию в заявке — мы подтвердим, можем ли взять проект.',
		),
		'faq.3.q'              => array(
			'lv' => 'Vai tāme ir fiksēta?',
			'en' => 'Is the estimate fixed?',
			'ru' => 'Смета фиксированная?',
		),
		'faq.3.a'              => array(
			'lv' => 'Apjomu un budžetu saskaņojam rakstiski pirms darba sākuma. Izmaiņas apspriežam un apstiprinām, pirms tās ietekmē izmaksas vai grafiku.',
			'en' => 'We agree scope and budget in writing before work begins. Changes are discussed and approved before they affect cost or schedule.',
			'ru' => 'Объём и бюджет согласуем письменно до начала работ. Изменения обсуждаем и утверждаем до того, как они повлияют на стоимость или график.',
		),
		'faq.4.q'              => array(
			'lv' => 'Kāda ir garantija?',
			'en' => 'What guarantee do you provide?',
			'ru' => 'Какая гарантия?',
		),
		'faq.4.a'              => array(
			'lv' => 'Darbu kvalitātes garantija ir iekļauta līgumā. Ilgums ir atkarīgs no darbu veida — precizēsim piedāvājumā.',
			'en' => 'A workmanship warranty is included in the contract. Duration depends on the type of works — we confirm it in your proposal.',
			'ru' => 'Гарантия на качество работ включена в договор. Срок зависит от вида работ — уточним в предложении.',
		),
		'faq.5.q'              => array(
			'lv' => 'Kā sākt?',
			'en' => 'How do I get started?',
			'ru' => 'Как начать?',
		),
		'faq.5.a'              => array(
			'lv' => 'Uzrakstiet e-pastu vai zvaniet ar īsu projekta aprakstu. Mēs ātri atbildēsim un vienosimies par konsultāciju.',
			'en' => 'Email or call us with a short project description. We reply quickly and book a consultation.',
			'ru' => 'Напишите на email или позвоните с кратким описанием проекта. Мы быстро ответим и согласуем консультацию.',
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
		'contact.hint'         => array(
			'lv' => 'Rakstiet vai zvaniet — mēs ātri atbildēsim',
			'en' => 'Email or call — we reply quickly',
			'ru' => 'Напишите или позвоните — ответим быстро',
		),
		'contact.mail_cta'     => array(
			'lv' => 'Rakstīt e-pastu',
			'en' => 'Email us',
			'ru' => 'Написать email',
		),
		'contact.call_cta'     => array(
			'lv' => 'Zvanīt',
			'en' => 'Call',
			'ru' => 'Позвонить',
		),
		'contact.mail_subject' => array(
			'lv' => 'Projekta pieprasījums',
			'en' => 'Project enquiry',
			'ru' => 'Заявка на проект',
		),
		'contact.email'        => array(
			'lv' => 'info@construction.lv',
			'en' => 'info@construction.lv',
			'ru' => 'info@construction.lv',
		),
		'contact.phone'        => array(
			'lv' => '+371 2000 0000',
			'en' => '+371 2000 0000',
			'ru' => '+371 2000 0000',
		),
		'contact.address'      => array(
			'lv' => 'Rīga, Latvija',
			'en' => 'Riga, Latvia',
			'ru' => 'Рига, Латвия',
		),
		'contact.address_label' => array(
			'lv' => 'Adrese',
			'en' => 'Address',
			'ru' => 'Адрес',
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
