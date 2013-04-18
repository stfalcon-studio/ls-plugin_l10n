<?php

/**
 * Плагин L10n. Хуки для коментариев
 */
class PluginL10n_HookComment extends Hook {

    /**
     * Регистрируем нужные хуки
     *
     * @return void
     */
    public function RegisterHook() {
        if (Config::Get('plugin.l10n.allowed_collapse_comments')) {
            $this->AddHook('template_comment_tree_begin', 'TemplateCommentTreeBegin', __CLASS__);
        }
    }

    /**
     * Метод пересчета коментариев
     *
     * @param array $aData
     */
    public function TemplateCommentTreeBegin($aData)
    {
        if ($aData['sTargetType'] == 'topic') {
            $oTopic = $this->Topic_GetTopicById($aData['iTargetId']);
            $commentCount = $oTopic->getExtraData('collapsedCount');

            if (Config::Get('plugin.l10n.allowed_collapse_comments')) {
                if ($commentCount !== null) {
                    $data = $commentCount;
                }
                else {
                    $data = 0;
                }
            }
            else {
                $data = $oTopic->GetTopicCountComment();
            }

            $this->Viewer_Assign('iCountComment', $data);
            $this->Viewer_Display(Plugin::GetTemplatePath(__CLASS__) . 'comment_count.tpl');
        }
    }
}
