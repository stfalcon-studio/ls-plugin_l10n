<?php

/**
 * Модуль Comment плагина L10n
 */
class PluginL10n_ModuleComment extends PluginL10n_Inherit_ModuleComment
{
    /**
     * Получить все комменты
     *
     * @param unknown_type $sTargetType
     * @param unknown_type $iPage
     * @param unknown_type $iPerPage
     *
     * @return unknown
     */
    public function GetCommentsAll($sTargetType, $iPage, $iPerPage, $aExcludeTarget = array(), $aExcludeParentTarget = array())
    {
        $s = serialize($aExcludeTarget) . serialize($aExcludeParentTarget);

        $sCacheId = "comment_all_{$sTargetType}_{$iPage}_{$iPerPage}_{$s}"
            . (is_null($this->PluginL10n_L10n_GetLangForQuery()) ? '' : '_' . $this->PluginL10n_L10n_GetLangForQuery());

        if (false === ($data = $this->Cache_Get($sCacheId))) {
            $data = array('collection' => $this->oMapper->GetCommentsAll($sTargetType, $iCount, $iPage, $iPerPage, $aExcludeTarget, $aExcludeParentTarget, $this->PluginL10n_L10n_GetLangForQuery()), 'count' => $iCount);
            $this->Cache_Set($data, $sCacheId, array("comment_new_{$sTargetType}", "comment_update_status_{$sTargetType}"), 60 * 60 * 24 * 1);
        }
        $data['collection'] = $this->GetCommentsAdditionalData($data['collection'], array('target', 'favourite', 'user' => array()));

        return $data;
    }

    /**
     * Получить все комменты сгрупированные по топику (для вывода прямого эфира)
     *
     * @param string  $sTargetType
     * @param integer $iLimit
     *
     * @return array
     */
    public function GetCommentsOnline($sTargetType, $iLimit)
    {
        /**
         * Исключаем из выборки идентификаторы закрытых блогов (target_parent_id)
         */
        $aCloseBlogs = ($this->oUserCurrent) ? $this->Blog_GetInaccessibleBlogsByUser($this->oUserCurrent) : $this->Blog_GetInaccessibleBlogsByUser();

        $s = serialize($aCloseBlogs);

        $id = "comment_online_{$sTargetType}_{$s}_{$iLimit}"
            . (is_null($this->PluginL10n_L10n_GetLangForQuery()) ? '' : '_' . $this->PluginL10n_L10n_GetLangForQuery());
        if (false === ($data = $this->Cache_Get($id))) {
            $data = $this->oMapper->GetCommentsOnline($sTargetType, $aCloseBlogs, $iLimit, $this->PluginL10n_L10n_GetLangForQuery());
            $this->Cache_Set($data, $id, array("comment_online_update_{$sTargetType}"), 60 * 60 * 24 * 1);
        }
        $data = $this->GetCommentsAdditionalData($data);

        return $data;
    }

    /**
     * Получить новые комменты для владельца
     *
     * @param int    $sId            ID владельца коммента
     * @param string $sTargetType    Тип владельца комментария
     * @param int    $sIdCommentLast ID последнего прочитанного комментария
     *
     * @return array('comments'=>array,'iMaxIdComment'=>int)
     */
    public function GetCommentsNewByTargetId($sId, $sTargetType, $sIdCommentLast)
    {
        if (false === ($aComments = $this->Cache_Get("comment_target_{$sId}_{$sTargetType}_{$sIdCommentLast}"))) {

            if ($sTargetType == 'topic' && Config::Get('plugin.l10n.allowed_collapse_comments')) {
                $aNestedTopicsId = array_keys($this->Topic_GetNestedTopics($this->Topic_GetTopicById($sId)));
                $aComments = $this->oMapper->GetCommentsNewByTargetId($aNestedTopicsId, $sTargetType, $sIdCommentLast);
            } else {
                $aComments = $this->oMapper->GetCommentsNewByTargetId($sId, $sTargetType, $sIdCommentLast);
            }

            $this->Cache_Set($aComments, "comment_target_{$sId}_{$sTargetType}_{$sIdCommentLast}", array("comment_new_{$sTargetType}_{$sId}"), 60 * 60 * 24 * 1);
        }
        if (count($aComments) == 0) {
            return array('comments' => array(), 'iMaxIdComment' => 0);
        }

        $iMaxIdComment = max($aComments);
        $aCmts = $this->GetCommentsAdditionalData($aComments);
        $oViewerLocal = $this->Viewer_GetLocalViewer();
        $oViewerLocal->Assign('oUserCurrent', $this->User_GetUserCurrent());
        $oViewerLocal->Assign('bOneComment', true);
        if ($sTargetType != 'topic') {
            $oViewerLocal->Assign('bNoCommentFavourites', true);
        }
        $aCmt = array();
        foreach ($aCmts as $oComment) {
            $oViewerLocal->Assign('oComment', $oComment);
            $sText = $oViewerLocal->Fetch($this->GetTemplateCommentByTarget($sId, $sTargetType));
            $aCmt[] = array(
                'html' => $sText,
                'obj' => $oComment,
            );
        }

        return array('comments' => $aCmt, 'iMaxIdComment' => $iMaxIdComment);
    }

    /**
     * Добавляет коммент
     *
     * @param  ModuleComment_EntityComment $oComment	Объект комментария
     *
     * @return bool|ModuleComment_EntityComment
     */
    public function AddComment(ModuleComment_EntityComment $oComment)
    {
        if (Config::Get('plugin.l10n.allowed_collapse_comments') && $oComment->getTargetType() == 'topic') {
            if ($oComment->GetCommentPid()) {
                $oCommentParent = $this->Comment_GetCommentById($oComment->GetCommentPid());
                $oComment->setTargetId($oCommentParent->getTargetId());
                $oComment->setTargetParentId($oCommentParent->getTargetParentId());
            }
        }

        return parent::AddComment($oComment);
    }
}