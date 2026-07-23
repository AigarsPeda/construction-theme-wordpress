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
 * Homepage URL for the current language (keeps lang during logo/nav).
 */
function construction_home_url(): string {
	if ( function_exists( 'construction_front_url_for_lang' ) ) {
		return construction_front_url_for_lang( construction_current_lang() );
	}

	if ( function_exists( 'pll_home_url' ) ) {
		$url = pll_home_url();
		if ( is_string( $url ) && $url !== '' ) {
			return $url;
		}
	}

	return home_url( '/' );
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
 * Theme string catalog.
 *
 * SOURCE OF TRUTH FOR PAGE COPY = WordPress database (Pages → Edit).
 * This array is only a seed used when creating missing pages or a forced reset
 * (`?construction_rebuild_homes=1&force=1`). Editing these strings does not
 * change live page content. Also used for header/footer chrome labels.
 *
 * @return array<string, array{lv: string, en: string, ru: string}>
 */
function construction_strings(): array {
	return array(
		'hero.title'           => array(
			'lv' => 'Būvniecība ar skaidru vadību',
			'en' => 'Construction under clear control',
			'ru' => 'Строительство под ясным контролем',
		),
		'hero.text'            => array(
			'lv' => 'Būvniecība, būvniecības vadība un uzraudzība — no plānošanas līdz objekta nodošanai.',
			'en' => 'Construction, construction management and supervision — from planning to handover.',
			'ru' => 'Строительство, управление и надзор — от планирования до сдачи объекта.',
		),
		'hero.cta'             => array(
			'lv' => 'Sazinies ar mums',
			'en' => 'Contact us',
			'ru' => 'Связаться с нами',
		),
		'hero.since'           => array(
			'lv' => 'Būvniecība un uzraudzība kopš 2008. gada',
			'en' => 'Construction and supervision since 2008',
			'ru' => 'Строительство и надзор с 2008 года',
		),
		'hero.alt'             => array(
			'lv' => 'Būvlaukums — būvniecība un uzraudzība',
			'en' => 'Construction site — management and supervision',
			'ru' => 'Стройплощадка — управление и надзор',
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
			'lv' => 'Būvniecība, vadība un uzraudzība',
			'en' => 'Construction management and supervision',
			'ru' => 'Строительство, управление и надзор',
		),
		'seo.home.desc'        => array(
			'lv' => 'Būvniecība un būvniecības vadība un uzraudzība Latvijā — no projekta līdz nodošanai. Skaidri termiņi, kvalitāte un viena komanda.',
			'en' => 'Construction and construction management and supervision in Latvia — from project to handover. Clear timelines, quality, and one dedicated team.',
			'ru' => 'Строительство, управление и строительный надзор в Латвии — от проекта до сдачи. Понятные сроки, качество и одна команда.',
		),
		'seo.projects.title'   => array(
			'lv' => 'Projekti — būvniecība un uzraudzība',
			'en' => 'Projects — construction and supervision',
			'ru' => 'Проекты — строительство и надзор',
		),
		'seo.projects.desc'    => array(
			'lv' => 'Apskati mūsu projektu galeriju — būvlaukumi, vadība objektā un gatavie darbi.',
			'en' => 'Browse our project gallery — construction sites, on-site management, and finished works.',
			'ru' => 'Смотрите галерею проектов — стройплощадки, управление на объекте и готовые работы.',
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
			'lv' => 'Atlasīti objekti — būvniecība, vadība un uzraudzība katrā posmā.',
			'en' => 'Selected projects — construction, management, and supervision at every stage.',
			'ru' => 'Избранные объекты — строительство, управление и надзор на каждом этапе.',
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
			'lv' => 'Būvniecība, vadība un uzraudzība vienā komandā',
			'en' => 'Construction, management, and supervision in one team',
			'ru' => 'Строительство, управление и надзор в одной команде',
		),
		'services.intro'       => array(
			'lv' => 'No plānošanas līdz nodošanai — skaidra būvniecības vadība, uzraudzība objektā un caurspīdīgs process.',
			'en' => 'From planning to handover — clear construction management, on-site supervision, and a transparent process.',
			'ru' => 'От планирования до сдачи — ясное управление строительством, надзор на объекте и прозрачный процесс.',
		),
		'services.item1.title' => array(
			'lv' => 'Būvniecība',
			'en' => 'Construction',
			'ru' => 'Строительство',
		),
		'services.item1.text'  => array(
			'lv' => 'Objekta būvdarbi ar plānotu grafiku, brigādēm un kvalitātes kontroli.',
			'en' => 'On-site construction with a planned schedule, crews, and quality control.',
			'ru' => 'Строительные работы на объекте с графиком, бригадами и контролем качества.',
		),
		'services.item2.title' => array(
			'lv' => 'Būvniecības vadība',
			'en' => 'Construction management',
			'ru' => 'Управление строительством',
		),
		'services.item2.text'  => array(
			'lv' => 'Koordinācija, termiņi, tāmes un komunikācija ar visām iesaistītajām pusēm.',
			'en' => 'Coordination, timelines, budgets, and communication with all parties involved.',
			'ru' => 'Координация, сроки, сметы и коммуникация со всеми участниками.',
		),
		'services.item3.title' => array(
			'lv' => 'Uzraudzība',
			'en' => 'Supervision',
			'ru' => 'Надзор',
		),
		'services.item3.text'  => array(
			'lv' => 'Būvniecības uzraudzība objektā — pārbaudes, dokumentācija un atbilstība projektam.',
			'en' => 'Construction supervision on site — inspections, documentation, and compliance with the plan.',
			'ru' => 'Строительный надзор на объекте — проверки, документация и соответствие проекту.',
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
			'lv' => 'No projekta līdz uzraudzībai viss noritēja raiti. Objekts nodots tieši tā, kā bijām vienojušies.',
			'en' => 'From planning to supervision everything went smoothly. The site was handed over exactly as agreed.',
			'ru' => 'От проекта до надзора всё прошло гладко. Объект сдали именно так, как договаривались.',
		),
		'reviews.3'            => array(
			'lv' => 'Lieliska komunikācija un skaidra būvniecības vadība. Paldies Construction komandai.',
			'en' => 'Great communication and clear construction management. Thanks to the Construction team.',
			'ru' => 'Отличная коммуникация и ясное управление строительством. Спасибо команде Construction.',
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
			'lv' => 'Būvniecība un uzraudzība',
			'en' => 'Construction & supervision',
			'ru' => 'Строительство и надзор',
		),
		'process.3.text'       => array(
			'lv' => 'Darbi objektā ar grafika atjauninājumiem, kvalitātes pārbaudēm un būvniecības uzraudzību katrā posmā.',
			'en' => 'Work on site with schedule updates, quality checks, and construction supervision at every stage.',
			'ru' => 'Работы на объекте с обновлениями графика, контролем качества и строительным надзором на каждом этапе.',
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
			'lv' => 'Termiņi ir atkarīgi no apjoma. Būvniecības un uzraudzības darbi tiek plānoti posmos ar skaidriem atskaites punktiem līgumā.',
			'en' => 'Timelines depend on scope. Construction and supervision work is planned in phases with clear milestones in the contract.',
			'ru' => 'Сроки зависят от объёма. Строительство и надзор планируются этапами с понятными вехами в договоре.',
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
