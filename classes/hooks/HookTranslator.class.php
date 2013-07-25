<?php


class PluginL10n_HookTranslator extends Hook {

    public function RegisterHook() {
        $this->AddHook('template_admin_action_item',
                'TemplateTranslator', __CLASS__);
    }

    public function TemplateTranslator()
    {
        return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . '/actions/ActionTranslator/translator.tpl');
    }
}
