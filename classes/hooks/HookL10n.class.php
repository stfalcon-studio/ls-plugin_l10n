<?php

/**
 * Хук для плагина L10n
 */
class PluginL10n_HookL10n extends Hook
{

    /**
     * Регистрируем нужные хуки
     *
     * @return void
     */
    public function RegisterHook()
    {
        // @todo в паре с плагином aceadminpanel хук вызывается дважды.
        $this->AddHook('lang_init_start', 'SetLang', __CLASS__);
        $this->AddHook('init_action', 'ShowBlockSelectLang', __CLASS__);
//        $this->AddHook('engine_init_complete', 'UpdateBlockRoutes', __CLASS__);
    }

    /**
     * Установка языка интерфейса
     *
     * @return void
     */
    public function SetLang()
    {
        if (!$this->User_IsInit()) {
            $this->User_Init();
        }
        if ($this->PluginL10n_L10n_GetLangFromUrl()) {
            // берем язык интерфейса с урла
            Config::Set('lang.current', $this->PluginL10n_L10n_GetLangFromUrl());
        } elseif ($this->User_IsAuthorization()) {
            // если в урле пусто - 
            // для авторизированных пользователей проверяем какой язык интерфейса
            // выбран по умолчанию и устанавливаем его в качестве текущего
            $oUser = $this->User_GetUserCurrent();
            Config::Set('lang.current', $oUser->getUserLang());
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            // для остальных язык будет передаваться по GeoIP
            require_once Plugin::GetPath(__CLASS__) . 'classes/lib/external/GeoIp/Wrapper.php';
            $gi = new GeoIp_Wrapper();
            // 2х значный код страны - он же код языка
            $country = $gi->getCountryCodeByAddr($_SERVER['REMOTE_ADDR']);
            $ruSpeaks = Config::Get('plugin.l10n.ru.countries');
            // @todo refact
            if ($country == 'ua') {
                Config::Set('lang.current', $this->PluginL10n_L10n_GetLangByAlias('uk'));
                setlocale(LC_ALL, "uk_UA.UTF-8");
            } else {
                Config::Set('lang.current', $this->PluginL10n_L10n_GetLangByAlias('ru'));
                setlocale(LC_ALL, "ru_RU.UTF-8");
                date_default_timezone_set('Europe/Moscow'); // See http://php.net/manual/en/timezones.php
            }
//            if (in_array($country, $ruSpeaks)) {
//                Config::Set('lang.current', $this->PluginL10n_L10n_GetLangByAlias('ru'));
//                //   setlocale(LC_ALL, "ru_RU.UTF-8");
//                date_default_timezone_set('Europe/Moscow'); // See http://php.net/manual/en/timezones.php
//            } else {
//                Config::Set('lang.current', $this->PluginL10n_L10n_GetLangByAlias('en'));
//                //   setlocale(LC_ALL, "en_EN.UTF-8");
//            }
        }
        $sLang = $this->PluginL10n_L10n_GetAliasByLang(Config::Get('lang.current'));
        $sWebPath = Router::GetPathWebCurrent();
        /**
         * Проверяем, включено ли принудительно проставление языка в урле, не является ли это CLI или Аяксом
         */
        if (Config::Get('plugin.l10n.lang_in_url') && $sWebPath && !$this->isAjax()) {
            /**
             * Проверям, был ли язык в урле, и не главная ли это страница
             */
            if (!Router::getLang() && rtrim($sWebPath, '/') !=Config::Get('path.root.web')) {
                $sWebPath = str_replace(Config::Get('path.root.web'), Config::Get('path.root.web') . '/' . $sLang, $sWebPath);
                Router::Location($sWebPath);
            }
            $this->PluginL10n_L10n_SetLangForUrl($sLang);
        }
        
        $sConfigFile = Config::Get('path.root.server') . '/config/plugins/l10n/config.' .$sLang . '.php';
        if (file_exists($sConfigFile)) {
            Config::LoadFromFile($sConfigFile);
        }
        $this->UpdateBlockRoutes();
        $this->Viewer_Assign('sLangCurrent', $this->PluginL10n_L10n_GetAliasByLang(Config::Get('lang.current')));
    }

    public function ShowBlockSelectLang()
    {
        if ($priority = Config::Get('plugin.l10n.lang_block.priority')) {
            $this->Viewer_AddBlock(
                    'right',
                    'L10nSelectLang',
                    array('plugin' => 'l10n'),
                    $priority
            );
        }

        $this->Viewer_PrependStyle(
        Plugin::GetTemplateWebPath(__CLASS__) . 'css/style.css');
    }

    public function UpdateBlockRoutes()
    {
        if (Router::getLang()) {
            $aBlocks = Config::Get('block');
            foreach ($aBlocks as $name => $block) {
                if (isset($block['path'])) {
                    $aPath = array();
                    foreach ($block['path'] as $path) {
                        $aPath[] = str_replace(Config::Get('path.root.web'), Config::Get('path.root.web') . '/' . $this->PluginL10n_L10n_GetAliasByLang(Config::Get('lang.current')), $path);
                    }
                    Config::Set('block.' . $name . '.path', $aPath);
                }
            }
        }
    }
    
    /**
     * Check is request is ajax
     * 
     * @return boolean 
     */
    protected function isAjax()
    {
        $bRet = false;
        /**
         * Стандартный заголовок для аяксов
         */
        if (isset($_SERVER['X-Requested-With']) && $_SERVER['X-Requested-With'] == 'XMLHttpRequest') {
            $bRet = true;
        }
        
        /**
         * Котеровская либа
         */
        if (getRequest('JsHttpRequest')) {
            $bRet = true;
        }
        
        return $bRet;
    }
}