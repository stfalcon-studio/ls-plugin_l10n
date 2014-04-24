<?php

class PluginL10n_ActionBlog extends PluginL10n_Inherit_ActionBlog
{
    protected function RegisterEvent()
    {
        parent::RegisterEvent();
    }

    protected function EventShowTopic()
    {
        if ($this->GetParamEventMatch(0,1)) {
            $iTopicId=$this->GetParamEventMatch(0,1);
        } else {
            $iTopicId=$this->GetEventMatch(1);
        }

        $oTopic = $this->Topic_GetTopicById($iTopicId);

        if (!$oTopic || ($oTopic->getLang() != Config::get('lang.current'))) {
            return parent::EventNotFound();
        }

        parent::EventShowTopic();
    }

}