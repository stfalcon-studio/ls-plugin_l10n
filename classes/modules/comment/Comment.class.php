<?php

/**
 * Модуль Comment плагина L10n
 */
class PluginL10n_ModuleComment extends PluginL10n_Inherit_ModuleComment {

    /**
     * Получить все комменты сгрупированные по топику (для вывода прямого эфира)
     *
     * @param string $sTargetType
     * @param integer $iLimit
     * @return array
     */
    public function GetCommentsOnline($sTargetType, $iLimit) {
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

}