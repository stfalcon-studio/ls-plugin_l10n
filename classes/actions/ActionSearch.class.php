<?php

class PluginL10n_ActionSearch extends PluginL10n_Inherit_ActionSearch
{
    public function Init()
    {
        $sLang = Config::Get('lang.current');
        $this->sTypesEnabled['topics']['topic_lang'] = crc32(strtolower($sLang));
        $this->sTypesEnabled['comments']['comment_topic_lang'] = crc32(strtolower($sLang));
        $this->SetDefaultEvent('index');
        $this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.l10n.search'));
    }
}