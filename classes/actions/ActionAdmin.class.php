<?php

/**
 *
 */
class PluginL10n_ActionAdmin extends PluginL10n_Inherit_ActionAdmin
{
    protected function RegisterEvent()
    {
        parent::RegisterEvent();
        $this->AddEvent('role', 'EventUpdateUserRole');
    }

    protected function EventUpdateUserRole()
    {
        // Check is id parameter exists
        $iUserId = getRequest('user_id', false);
        if (!$iUserId) {
            return Router::Action('error');
        }

        // Check is edited user exists
        $oUserProfile = $this->User_GetUserById($iUserId);
        if (!$oUserProfile) {
            return Router::Action('error');
        }

        // add\remove role translator
        if (getRequest('add', false)) {
            $oUserProfile->setUserRole(Config::Get('plugin.l10n.role.translator'));
        } else {
            $oUserProfile->setUserRole(Config::Get('plugin.l10n.role.user'));
        }

        $this->User_UpdateUserRole($oUserProfile);

        Router::Location($oUserProfile->getUserWebPath());
    }
}