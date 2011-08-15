<?php

/**
 * Маппер модуля Comment плагина L10n
 */
class PluginL10n_ModuleComment_MapperComment extends PluginL10n_Inherit_ModuleComment_MapperComment {

    /**
     * Комментарии для прямого эфира (с учетом языка)
     *
     * @param string $sTargetType
     * @param array $aExcludeTargets
     * @param integer $iLimit
     * @param string $sLang
     * @return array
     */
    public function GetCommentsOnline($sTargetType, $aExcludeTargets, $iLimit, $sLang = null) {
        $sql = "SELECT
                    c.comment_id
                FROM
                    " . Config::Get('db.table.comment_online') . " as c,
                    " . Config::Get('db.table.topic') . " as t
                WHERE
                    c.target_type = ?
                    { AND c.target_parent_id NOT IN(?a) }
                    AND c.target_id = t.topic_id
                    " . (is_null($sLang) ? "AND t.topic_original_id IS NULL" : "AND t.topic_lang = '$sLang'") . "
                ORDER BY
                    c.comment_online_id DESC limit 0, ?d ; ";

        $aComments = array();

        $aRows = $this->oDb->select(
                        $sql, $sTargetType,
                        (count($aExcludeTargets) ? $aExcludeTargets : DBSIMPLE_SKIP),
                        $iLimit);
        if ($aRows) {
            foreach ($aRows as $aRow) {
                $aComments[] = $aRow['comment_id'];
            }
        }
        return $aComments;
    }

}