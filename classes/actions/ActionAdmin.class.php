<?php

/**
 *
 */
class PluginL10n_ActionAdmin extends PluginL10n_Inherit_ActionAdmin
{
    protected function RegisterEvent()
    {
        parent::RegisterEvent();
        $this->AddEventPreg('/^translation$/i', '/^(page([1-9]\d{0,5}))?$/i', 'EventTranslation');
        $this->AddEvent('role', 'EventUpdateUserRole');
    }

    protected function EventTranslation()
    {
        $iPage=$this->GetParamEventMatch(0,2) ? $this->GetParamEventMatch(0,2) : 1;

        $aFilter = array();

        $aResult = $this->Topic_GetNotTranslatedTopicsByFilter($aFilter, $iPage, Config::Get('module.topic.per_page'));

        $aPaging = $this->Viewer_MakePaging($aResult['count'], $iPage, Config::Get('module.topic.per_page'),
            Config::Get('pagination.pages.count'), Router::GetPath('admin') . 'translation');

        $this->Viewer_Assign('aPaging', $aPaging);
        $this->Viewer_Assign('noSidebar', true);
        $this->Viewer_Assign('aTopicData', $aResult['collection']);
    }

    protected function EventUpdateUserRole()
    {
        // Check is id parameter exists
        $iUserId = getRequest('user_id', false);
        if (!$iUserId) {
            return Router::Action('error');
        }

        // Check is current user admin
        $oCurrentUser = $this->User_GetUserCurrent();
        if (!$oCurrentUser->isAdministrator()) {
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