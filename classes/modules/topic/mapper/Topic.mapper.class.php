<?php

/**
 * Маппер Topic модуля Topic плагина L10n
 */
class PluginL10n_ModuleTopic_MapperTopic extends PluginL10n_Inherit_ModuleTopic_MapperTopic
{

    /**
     * Добавляет топик
     *
     * @param ModuleTopic_EntityTopic $oTopic
     * @return integer|boolean
     */
    public function AddTopic(ModuleTopic_EntityTopic $oTopic)
    {
        $sql = "INSERT INTO " . Config::Get('db.table.topic') . "
                (
                    blog_id,
                    user_id,
                    topic_lang,
                    topic_type,
                    topic_title,
                    topic_tags,
                    topic_date_add,
                    topic_user_ip,
                    topic_publish,
                    topic_publish_draft,
                    topic_publish_index,
                    topic_cut_text,
                    topic_forbid_comment,
                    topic_text_hash
                )
                VALUES (?d, ?d, ?, ?, ?, ?, ?, ?, ?d, ?d, ?d, ?, ?, ?)
		";

        if ($iId = $this->oDb->query($sql, $oTopic->getBlogId(), $oTopic->getUserId(),
                        $oTopic->getTopicLang(), $oTopic->getType(), $oTopic->getTitle(),
                        $oTopic->getTags(), $oTopic->getDateAdd(), $oTopic->getUserIp(),
                        $oTopic->getPublish(), $oTopic->getPublishDraft(),
                        $oTopic->getPublishIndex(), $oTopic->getCutText(),
                        $oTopic->getForbidComment(), $oTopic->getTextHash())
        ) {
            $oTopic->setId($iId);
            $this->AddTopicContent($oTopic);
            return $iId;
        }
        return false;
    }

    /**
     * Обновляет топик
     *
     * @param ModuleTopic_EntityTopic $oTopic
     * @return boolean|ModuleTopic_EntityTopic
     */
    public function UpdateTopic(ModuleTopic_EntityTopic $oTopic)
    {
        $sql = "UPDATE " . Config::Get('db.table.topic') . "
                SET
                    blog_id = ?d,
                    user_id = ?d,
                    topic_lang = ?,
                    topic_original_id = ?d,
                    topic_title = ?,
                    topic_tags = ?,
                    topic_date_add = ?,
                    topic_date_edit = ?,
                    topic_user_ip = ?,
                    topic_publish = ?d ,
                    topic_publish_draft = ?d ,
                    topic_publish_index = ?d,
                    topic_rating = ?f,
                    topic_count_vote = ?d,
                    topic_count_read = ?d,
                    topic_count_comment = ?d,
                    topic_cut_text = ? ,
                    topic_forbid_comment = ? ,
                    topic_text_hash = ?
                WHERE
                    topic_id = ?d
		";

        if ($this->oDb->query($sql, $oTopic->getBlogId(), $oTopic->getUserId(), $oTopic->getTopicLang(), $oTopic->getTopicOriginalId(),
                        $oTopic->getTitle(), $oTopic->getTags(), $oTopic->getDateAdd(),
                        $oTopic->getDateEdit(), $oTopic->getUserIp(), $oTopic->getPublish(),
                        $oTopic->getPublishDraft(), $oTopic->getPublishIndex(),
                        $oTopic->getRating(), $oTopic->getCountVote(), $oTopic->getCountRead(),
                        $oTopic->getCountComment(), $oTopic->getCutText(),
                        $oTopic->getForbidComment(), $oTopic->getTextHash(), $oTopic->getId())
        ) {
            $this->UpdateTopicContent($oTopic);
            return true;
        }
        return false;
    }

    /**
     * Строит условие по фильтру
     *
     * @param array $aFilter
     * @return string
     */
    protected function buildFilter(array $aFilter)
    {
        $sWhere = parent::buildFilter($aFilter);

        //@todo temporary
        if (!in_array('l10n', Engine::getInstance()->Plugin_GetActivePlugins())) {
            return $sWhere;
        }

        if (isset($aFilter['topic_lang'])) {
            $sWhere.=" AND t.topic_lang = '" . $aFilter['topic_lang'] . "'";
        }

        if (isset($aFilter['topic_original_id'])) {
            $sWhere.=" AND t.topic_original_id = " . $aFilter['topic_original_id'];
        } elseif (!isset($aFilter['topic_lang'])) {
            $sWhere.=" AND t.topic_original_id IS NULL";
        }

        return $sWhere;
    }

    /**
     * Достает теги открытых топиков
     *
     * @param integer $iLimit
     * @param null|string $sLang
     * @param int iUserId задаем для выборки персональных тегов пользователя
     * @return array
     */
    public function GetOpenTopicTags($iLimit, $sLang = null, $iUserId=null)
    {
        $sql = "SELECT
                        tt.topic_tag_text,
                        count(tt.topic_tag_text) as count
                FROM
                        " . Config::Get('db.table.topic_tag') . " as tt,
                        " . Config::Get('db.table.blog') . " as b,
                        " . Config::Get('db.table.topic') . " as t
                WHERE
                        tt.blog_id = b.blog_id
                        " . (is_null($iUserId) ? " " : " AND tt.user_id = '{$iUserId}' ") . "
                        AND b.blog_type NOT IN ('close')
                        AND t.topic_id = tt.topic_id
                        " . (is_null($sLang) ? "AND t.topic_original_id IS NULL" : "AND t.topic_lang = '$sLang'") . "
                GROUP BY
                        tt.topic_tag_text
                ORDER BY
                        count DESC
                LIMIT 0, ?d
                        ";
        $aReturn = array();
        $aReturnSort = array();
        if ($aRows = $this->oDb->select($sql, $iLimit)) {
            foreach ($aRows as $aRow) {
                $aReturn[mb_strtolower($aRow['topic_tag_text'], 'UTF-8')] = $aRow;
            }
            ksort($aReturn);
            foreach ($aReturn as $aRow) {
                $aReturnSort[] = Engine::GetEntity('Topic_TopicTag', $aRow);
            }
        }
        return $aReturnSort;
    }

    /**
     * Топики которые соответствуют заданному тегу
     *
     * @param string $sTag
     * @param array $aExcludeBlog
     * @param integer $iCount
     * @param integer $iCurrPage
     * @param integer $iPerPage
     * @param null|string $sLang
     * @return array
     */
    public function GetTopicsByTag($sTag, $aExcludeBlog, &$iCount, $iCurrPage, $iPerPage, $sLang = null)
    {
        $sql = "SELECT
                    tt.topic_id
                FROM
                    " . Config::Get('db.table.topic_tag') . " as tt,
                    " . Config::Get('db.table.topic') . " as t
                WHERE
                    tt.topic_tag_text = ?
                    { AND tt.blog_id NOT IN (?a) }
                    AND t.topic_id = tt.topic_id
                    " . (is_null($sLang) ? "AND t.topic_original_id IS NULL" : "AND t.topic_lang = '$sLang'") . "
                ORDER BY tt.topic_id DESC
                    LIMIT ?d, ?d ";

        $aTopics = array();

        $aRows = $this->oDb->selectPage(
                        $iCount, $sql, $sTag,
                        (is_array($aExcludeBlog) && count($aExcludeBlog)) ? $aExcludeBlog : DBSIMPLE_SKIP,
                        ($iCurrPage - 1) * $iPerPage, $iPerPage);
        if ($aRows) {
            foreach ($aRows as $aTopic) {
                $aTopics[] = $aTopic['topic_id'];
            }
        }
        return $aTopics;
    }

    /**
     * Рейтинг топиков
     *
     * @param string $sDate
     * @param int $iLimit
     * @param array $aExcludeBlog
     * @param string $sLang
     * @return array
     */
    public function GetTopicsRatingByDate($sDate, $iLimit, $aExcludeBlog=array(), $sLang = null)
    {
        $sql = "SELECT
                    t.topic_id
                FROM
                    " . Config::Get('db.table.topic') . " as t
                WHERE
                    t.topic_publish = 1
                    AND
                    t.topic_date_add >= ?
                    AND
                    t.topic_rating >= 0
                    { AND t.blog_id NOT IN(?a) }
                    " . (is_null($sLang) ? "AND t.topic_original_id IS NULL" : "AND t.topic_lang = '$sLang'") . "
                ORDER by t.topic_rating desc, t.topic_id desc
                LIMIT 0, ?d ";
        $aTopics = array();

        $aRows = $this->oDb->select(
                        $sql, $sDate,
                        (is_array($aExcludeBlog) && count($aExcludeBlog)) ? $aExcludeBlog : DBSIMPLE_SKIP,
                        $iLimit);
        if ($aRows) {
            foreach ($aRows as $aTopic) {
                $aTopics[] = $aTopic['topic_id'];
            }
        }
        return $aTopics;
    }

    /**
     * Set topic original id and author id
     *
     * @param ModuleTopic_EntityTopic $oTopic
     * @param ModuleTopic_EntityTopic $oTopicOriginal
     * @return boolean
     */
    public function SetTopicOriginal($oTopic, $oTopicOriginal)
    {
        $sql = "UPDATE " . Config::Get('db.table.topic') . "
                SET
                    user_id =?d,
                    topic_original_id = ?d
                WHERE
                    topic_id = ?d
                ";

        if ($this->oDb->query($sql, $oTopicOriginal->getUserId(), $oTopicOriginal->getId(), $oTopic->getId())) {
            return true;
        }
        return false;
    }

    public function GetNotTranslatedTopicsByFilter($aFilter, &$iCount, $iPage, $iPerPage)
    {
        $sWhere = $this->buildFilter($aFilter);

        if (!isset($aFilter['order'])) {
            $aFilter['order'] = 't.topic_date_add desc';
        }
        if (!is_array($aFilter['order'])) {
            $aFilter['order'] = array($aFilter['order']);
        }

        $sql = "SELECT t.topic_id
                FROM " . Config::Get('db.table.topic') . " AS t
                LEFT OUTER JOIN " . Config::Get('db.table.topic') . " AS tt
                    ON t.topic_original_id IS NULL
                WHERE
                    t.topic_id <> tt.topic_original_id
					ORDER BY " .
            implode(', ', $aFilter['order'])
            . "
					LIMIT ?d, ?d";
        $aTopics = array();
        if ($aRows = $this->oDb->selectPage($iCount, $sql, ($iPage - 1) * $iPerPage, $iPerPage)) {
            foreach ($aRows as $aTopic) {
                $aTopics[] = $aTopic['topic_id'];
            }
        }

        return $aTopics;
    }
}