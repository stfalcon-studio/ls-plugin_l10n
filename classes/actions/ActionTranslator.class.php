<?php

class PluginL10n_ActionTranslator extends PluginL10n_Inherit_ActionAdmin
{
    protected function RegisterEvent() {
        parent::RegisterEvent();
        $this->AddEvent('translator','EventIndex');
    }

    protected function EventIndex()
    {
        $aUsersRegister=$this->User_GetUsersByDateRegister(9999);
        $aUsersNames = array();
        foreach($aUsersRegister as $users)
        {
            $aUsersNames[] = $users->getUserLogin();
        }
        $this->Viewer_Assign('aNames', $aUsersNames);
    }
}