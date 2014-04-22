<?php

/**
 *
 */
class PluginL10n_ActionTranslation extends  ActionPlugin
{
    public function Init()
    {
        $this->SetDefaultEvent('index');
    }

    protected function RegisterEvent()
    {
        $this->AddEventPreg('/^index$/i', '/^(page([1-9]\d{0,5}))?$/i', 'EventTranslation');
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
}