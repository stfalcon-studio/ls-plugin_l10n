<?php

/**
 * Модуль плагина L10n
 */
class PluginL10n_ModuleL10n extends Module
{

    /**
     * Метод инициализации модуля
     *
     * @return void
     */
    public function Init() {

    }

    /**
     * Передает в Viewer список доступных на сайте языков
     *
     * @param null|string $sExcludeLang
     * @return array
     */
    public function GetAllowedLangsToViewer($sExcludeLang = null) {
        $aAllowedLangs = $this->GetAllowedLangs($sExcludeLang);
        $aLangs = array();
        foreach ($aAllowedLangs as $sLang) {
            $aLangs[$sLang] = 'l10n_lang_' . $sLang;
        }

        return $aLangs;
    }

    /**
     * Возвращает код языка в LS по его алиасу
     *
     * @param null|string $sAlias
     * @return null|string
     */
    public function GetLangByAlias($sAlias = null) {
        if (is_null($sAlias)) {
            return null;
        }

        $aLangsAliases = Config::Get('plugin.l10n.langs_aliases');

        if (in_array($sAlias, $aLangsAliases)) {
            return array_search($sAlias, $aLangsAliases);
        } else {
            throw new Exception('Language alias "' . $sAlias . '" is not allowed!');
        }
    }

    /**
     * Возвращает алиас по коду языка
     *
     * @param null|string $sLang
     * @return null|string
     */
    public function GetAliasByLang($sLang = null) {
        if (is_null($sLang)) {
            return null;
        }

        $aLangsAliases = Config::Get('plugin.l10n.langs_aliases');
        if (array_key_exists($sLang, $aLangsAliases)) {
            return $aLangsAliases[$sLang];
        } else {
            throw new Exception('Language "' . $sLang . '" is not allowed!');
        }
    }

    /**
     * Список языковых алиасов
     *
     * @return array
     */
    public function GetLangsAliases() {
        return Config::Get('plugin.l10n.langs_aliases');
    }

    /**
     * Список разрешенных языков
     *
     * @return array
     */
    public function GetAllowedLangs($sExcludeLang = 'null') {
        $aAllowedLangs = Config::Get('plugin.l10n.allowed_langs');
        foreach ($aAllowedLangs as $iKey => $sLang) {
            if ((is_array($sExcludeLang) && in_array($sLang, $sExcludeLang))
                    || (is_string($sExcludeLang) && ($sExcludeLang == $sLang))) {
                unset($aAllowedLangs[$iKey]);
            }
        }

        return $aAllowedLangs;
    }

    /**
     * Список языковых алиасов для разрешенных языков
     *
     * @return array
     */
    public function GetAllowedLangsAliases() {
        return array_intersect_key($this->GetLangsAliases(), array_flip($this->GetAllowedLangs())
        );
    }

    /**
     * Проверка или указанный язык значится в списке разрешенных
     *
     * @param string $sLang
     * @return boolean
     */
    public function IsAllowedLang($sLang) {
        return in_array($sLang, $this->GetAllowedLangs());
    }

    /**
     * Проверка или указанный языковый алиас принадлежит разрешенному языку
     *
     * @param string $sLangAlias
     * @return boolean
     */
    public function IsAllowedLangAlias($sLangAlias) {
        try {
            $sLang = $this->PluginL10n_L10n_GetLangByAlias($sLangAlias);
            if ($this->PluginL10n_L10n_IsAllowedLang($sLang)) {
                return true;
            }
        } catch (Exception $e) {

        }

        return false;
    }

    /**
     * Возвращает язык с урла (если он там указан)
     *
     * @return
     */
    public function GetLangFromUrl() {
        return $this->PluginL10n_L10n_GetLangByAlias($this->PluginL10n_L10n_GetLangAliasFromUrl());
    }

    /**
     * Возвращает алиас языка с урла (если он там указан)
     *
     * @return
     */
    public function GetLangAliasFromUrl() {
        return Router::getLang();
    }

    public function SetLangForUrl($sLang) {
        Router::setLang($sLang);
    }

    public function GetLangForQuery() {
        if (Config::Get('plugin.l10n.lang_in_url')) {
            return Config::Get('lang.current');
        } else {
            return $this->PluginL10n_L10n_GetLangFromUrl();
        }
    }

}
