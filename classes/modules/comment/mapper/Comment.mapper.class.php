<?php

/**
 * Маппер модуля Comment плагина L10n
 */
class PluginL10n_ModuleComment_MapperComment extends PluginL10n_Inherit_ModuleComment_MapperComment
{

    /**
     * Получить все комменты (с учетом языка)
     *
     * @param type $sTargetType
     * @param type $iCount
     * @param type $iCurrPage
     * @param type $iPerPage
     * @param type $aExcludeTarget
     * @param type $aExcludeParentTarget
     * @param string $sLang
     * @return type
     */
    public function GetCommentsAll($sTargetType, &$iCount, $iCurrPage, $iPerPage, $aExcludeTarget=array(), $aExcludeParentTarget=array(), $sLang = null) {
        $sql = "SELECT
                        c.comment_id
                FROM
                        " . Config::Get('db.table.comment') . " c
                JOIN
                        " . Config::Get('db.table.topic') . " t
                ON
                        ( t.topic_id = c.target_id )
                WHERE
                        c.target_type = ?
                        AND
                        c.comment_delete = 0
                        AND
                        c.comment_publish = 1
                        { AND c.target_id NOT IN(?a) }
                        { AND c.target_parent_id NOT IN(?a) }
                        " . (is_null($sLang) ? "AND t.topic_original_id IS NULL" : "AND t.topic_lang = '$sLang'") . "
                ORDER by c.comment_id desc
                LIMIT ?d, ?d ";
        $aComments = array();
        if ($aRows = $this->oDb->selectPage(
                $iCount, $sql, $sTargetType, (count($aExcludeTarget) ? $aExcludeTarget : DBSIMPLE_SKIP), (count($aExcludeParentTarget) ? $aExcludeParentTarget : DBSIMPLE_SKIP), ($iCurrPage - 1) * $iPerPage, $iPerPage
                )
        ) {
            foreach ($aRows as $aRow) {
                $aComments[] = $aRow['comment_id'];
            }
        }
        return $aComments;
    }

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

    /**
     * Получить комменты по владельцу
     *
     * @param  int $sId	ID владельца коммента
     * @param  string $sTargetType	Тип владельца комментария
     * @return array
     */
    public function GetCommentsByTargetId($sId,$sTargetType)
    {
        $subQuery = "
            SELECT _pp.topic_id
            FROM ".Config::Get('db.table.topic')." AS _p
            LEFT JOIN prefix_topic AS _pp ON _pp.topic_original_id = _p.topic_id
                WHERE _p.topic_id IN (
                    SELECT IFNULL(_px.topic_original_id, _px.topic_id)	FROM ".Config::Get('db.table.topic')." AS _px WHERE _px.topic_id = ?d
                )
            UNION
            SELECT IFNULL(_px.topic_original_id, _px.topic_id)	FROM ".Config::Get('db.table.topic')." AS _px WHERE _px.topic_id = ?d
        ";

        if ($sTargetType != 'topic' || !Config::Get('plugin.l10n.allowed_collapse_comments')) {
            $subQuery = '?d';
        }

        $sql = "SELECT
                    comment_id,
                    comment_id as ARRAY_KEY,
                    comment_pid as PARENT_KEY
                FROM
                    ".Config::Get('db.table.comment')."
                WHERE
                    target_id IN (".$subQuery.")
                    AND
                    target_type = ?
                ORDER by comment_id asc;
                    ";

        if ($sTargetType == 'topic' && Config::Get('plugin.l10n.allowed_collapse_comments')) {
            if ($aRows=$this->oDb->select($sql,$sId,$sId,$sTargetType)) {
                return $aRows;
            }
            return null;
        }

        if ($aRows=$this->oDb->select($sql,$sId,$sTargetType)) {
            return $aRows;
        }
        return null;
    }

    public function GetCommentsNewByTargetId($sId,$sTargetType,$sIdCommentLast)
    {
        if (is_array($sId)) {
            $query = 'target_id IN ('. implode($sId, ',').') #?d';
        }
        else {
            $query = 'target_id = ?d';
        }

        $sql = "SELECT
                    comment_id
                FROM
                    ".Config::Get('db.table.comment')."
                WHERE
                    {$query}
                    AND
                    target_type = ?
                    AND
                    comment_id > ?d
                ORDER by comment_id asc;
                    ";
        $aComments=array();
        if ($aRows=$this->oDb->select($sql,$sId,$sTargetType,$sIdCommentLast)) {
            foreach ($aRows as $aRow) {
                $aComments[]=$aRow['comment_id'];
            }
        }
        return $aComments;
    }
}