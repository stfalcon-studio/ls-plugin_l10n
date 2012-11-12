<?php

/**
 * Плагин L10n. Хуки для пользователя
 */
class PluginL10n_HookUser extends Hook
{

    /**
     * Регистрируем нужные хуки
     *
     * @return void
     */
    public function RegisterHook()
    {
        // хук на метод добавления пользователя
        $this->AddHook('module_user_add_after', 'ModuleUserAddAfter', __CLASS__);
        // хук на форму регистрации пользователя
        $this->AddHook('template_form_registration_end',
                'TemplateFormRegistrationEnd', __CLASS__);
        // хук на меню настроек пользователя
        $this->AddHook('template_menu_settings_settings_item', 'TemplateMenuSettings', __CLASS__);
    }

    /**
     * Добавляем в меню пользователя ссылку на страницу языковых настроек
     *
     * @return string
     */
    public function TemplateMenuSettings()
    {
        if (Config::Get('plugin.l10n.user_lang_settings')) {
            $sTemplatePath = Plugin::GetTemplatePath('l10n') . 'actions/ActionSettings/l10n.menu.settings.tpl';
            return $this->Viewer_Fetch($sTemplatePath);
        }
    }

    /**
     * Елемент выбора языка для формы регистрации
     *
     * @return string
     */
    public function TemplateFormRegistrationEnd()
    {
        $this->Viewer_Assign('aLangs', $this->PluginL10n_L10n_GetAllowedLangsToViewer());
        $sTemplatePath = Plugin::GetTemplatePath('l10n')
                . 'actions/ActionUser/form_element_select_lang.tpl';
        return $this->Viewer_Fetch($sTemplatePath);
    }

    /**
     * Хук на метод добавления пользователя
     * Сохраняем язык пользователя при регистрации
     *
     * @param array $aResult
     * @return void
     */
    public function ModuleUserAddAfter($aResult)
    {
        $oUser = $aResult['result'];

        // сохраняем языковые настройки пользователя
        $sUserLang = getRequest('l10n_user_lang');

        if ($this->PluginL10n_L10n_IsAllowedLang($sUserLang)) {
            $oUser->setUserLang($sUserLang);
            $this->User_UpdateUserLang($oUser);
        }
    }

}