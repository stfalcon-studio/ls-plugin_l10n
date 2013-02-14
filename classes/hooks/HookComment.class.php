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
            $id = "comment_count_{$aData['iTargetId']}";
            if (false === ($data = $this->Cache_Get($id))) {
                $data = 0;
                foreach ($this->Topic_GetNestedTopics($this->Topic_GetTopicById($aData['iTargetId'])) as $oTopic) {
                    $data += $oTopic->getCountComment();
                }
                $this->Cache_Set($data, $id, array('comment_update', 'comment_new_topic'), 60 * 60 * 24 * 3);
            }

            $this->Viewer_Assign('iCountComment', 125);
        }
    }
}
