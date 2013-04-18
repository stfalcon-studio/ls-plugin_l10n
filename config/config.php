<?php

Config::Set('db.table.blog_l10n', '___db.table.prefix___blog_l10n');

$config = array();

// список доступных языков (для разных языковых версий сайта)
$config['allowed_langs'] = array('russian', 'english');

// Разрешить публиковать коментарии из топиков переводов
$config['allowed_collapse_comments'] = true;

// ключ - язык в урл, значение - папка языка в livestreet
// коды языков взяты из вики http://ru.wikipedia.org/wiki/%D0%9A%D0%BE%D0%B4%D1%8B_%D1%8F%D0%B7%D1%8B%D0%BA%D0%BE%D0%B2
$config['langs_aliases'] = array(
    'ukrainian' => 'uk',
    'russian' => 'ru',
    'english' => 'en',
    'german' => 'de',
    'spanish' => 'es',
    'belarussian' => 'be',
    'bulgarian' => 'bg',
    'lithuanian' => 'lt',
    'georgian' => 'ka',
    'uzbek' => 'uz',
);
$config['lang_block']['priority'] = 500;
$config['translate_block']['priority'] = 500;
$config['user_lang_settings'] = 1;  //  позволяет менять язык через настройки пользователя
$config['lang_in_url'] = 1; // подставлять язык в урл всегда, позволяет делать выборку в базе по всем языкам, если в урле не передан язык
// 'lang' - уведомления о новом топике будут получать пользователи с таким же языком в настройках как и язык топика
// 'original' - уведомления будут получать все, но только об оригинальном топике (не переводе)
// 'default' или любое другое значение - будет использоватся базовая рассылка
$config['notify'] = 'original';

$config['ru']['countries'] = array(
            'uz', // Узбекистан
            'ua', // Украина
            'tm', // Туркменистан
            'tj', // Таджикистан
            'mn', // Монголия
            'md', // Молдавия
            'ge', // Грузия
            'az', // Азербайджан
            'am', // Армения
            'kg', // Киргизия
            'kz', // Казахстан
            'by', // Белоруссия
            'ru', // Россия
        );

$aAllowedLangsAliases = array_intersect_key(
                $config['langs_aliases'],
                array_flip($config['allowed_langs'])
);

// Добавляем rewrite rules для sitemap'ов в роутер
$aRouterUri = Config::Get('router.uri');
foreach ($aAllowedLangsAliases as $sLangAlias) {
    $aRouterUri['/^' . $sLangAlias . '\/sitemap\.xml/i'] = $sLangAlias . '/sitemap';
    $aRouterUri['/^' . $sLangAlias . '\/sitemap_(\w+)_(\d+)\.xml/i'] = $sLangAlias . '/sitemap/sitemap/\\1/\\2';
}
Config::Set('router.uri', $aRouterUri);

$config['use_geoip'] = false;
return $config;