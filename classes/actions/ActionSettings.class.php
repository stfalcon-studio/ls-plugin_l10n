<?php

class PluginL10n_ActionSettings extends PluginL10n_Inherit_ActionSettings
{
    protected function RegisterEvent()
    {
        if (Config::Get('plugin.l10n.user_lang_settings')) {
            $this->AddEvent('l10n', 'EventL10n');
        }
//        $this->AddEventPreg('/^settings$/i', '/^$/i', 'EventSettings');
        parent::RegisterEvent();
    }

    /**
     * Страница языковых настроек пользователя
     *
     * @return void
     */
    protected function EventL10n()
    {
        // сохраняем языковые настройки пользователя
        if (isPost('l10n_settings_submit')) {
            $this->Security_ValidateSendForm();

            $sUserLang = getRequest('l10n_user_lang', null, 'post');
            if ($this->PluginL10n_L10n_IsAllowedLang($sUserLang)) {
                $this->oUserCurrent->setUserLang($sUserLang);
            }

            if ($this->PluginL10n_User_UpdateUserLang($this->oUserCurrent)) {
                // меняем язык текущей сесии
                $this->Lang_SetLang($this->oUserCurrent->getUserLang());
                $this->Message_AddNoticeSingle($this->Lang_Get('plugin.l10n.l10n_settings_submit_ok'));
            } else {
                $this->Message_AddErrorSingle($this->Lang_Get('system_error'));
            }
        }

        $this->Viewer_Assign('sMenuItemSelect', 'l10n');
        $this->Viewer_Assign('aLangs', $this->PluginL10n_L10n_GetAllowedLangsToViewer());
        $this->SetTemplate(Plugin::GetTemplatePath(__CLASS__) . 'actions/ActionSettings/l10n.tpl'); // проблема

    }

}