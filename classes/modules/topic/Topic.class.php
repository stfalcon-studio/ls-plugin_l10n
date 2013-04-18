<?php

/**
 * Модуль Topic плагина L10n
 */
class PluginL10n_ModuleTopic extends PluginL10n_Inherit_ModuleTopic
{

    /**
     * Список топиков по модифицированному фильтру
     *
     * @param  array $aFilter
     * @param  int   $iPage
     * @param  int   $iPerPage
     * @return array
     */
    public function GetTopicsByFilter($aFilter, $iPage = 0, $iPerPage = 0, $aAllowData = array('user' => array(), 'blog' => array('owner' => array()),
        'vote', 'favourite', 'comment_new')
    ) {
        return parent::GetTopicsByFilter($this->_getModifiedFilter($aFilter), $iPage, $iPerPage, $aAllowData
        );
    }

    /**
     * Список переводов топика
     *
     * @param integer $iTopicOriginalId
     * @return array
     */
    public function GetTopicTranslatesByTopicId($iTopicOriginalId) {
        $aFilter = array(
            'topic_publish' => 1,
            'topic_original_id' => $iTopicOriginalId,
        );

        return parent::GetTopicsByFilter($aFilter);
    }

    /**
     * Количество топиков по фильтру
     *
     * @param array $aFilter
     * @return integer
     */
    public function GetCountTopicsByFilter($aFilter) {
        return parent::GetCountTopicsByFilter($this->_getModifiedFilter($aFilter));
    }

    /**
     * Фильтр с дополнительными параметрами выборки для мультиязычности
     *
     * @param array $aFilter
     * @return array
     */
    private function _getModifiedFilter(array $aFilter) {

        if (!isset($aFilter['topic_lang'])) {
            $aFilter['topic_lang'] = $this->PluginL10n_L10n_GetLangForQuery();
        }
//                elseif ($this->User_IsAuthorization()) {
//                        $oUser = $this->User_GetUserCurrent();
//                        $aFilter['topic_lang'] = $oUser->getUserLang();
//                }
        return $aFilter;
    }

    /**
     * Получает список тегов из топиков открытых блогов (open,personal) с учетом языка сайта
     *
     * @param  int $iLimit
     * @return array
     */
    public function GetOpenTopicTags($iLimit, $iUserId=null) {
        $id = "tag_{$iLimit}_open" . (is_null($this->PluginL10n_L10n_GetLangForQuery()) ? '' : '_' . $this->PluginL10n_L10n_GetLangForQuery());
        if (false === ($data = $this->Cache_Get($id))) {
            $data = $this->oMapperTopic->GetOpenTopicTags($iLimit, $this->PluginL10n_L10n_GetLangFromUrl(), $iUserId);
            $this->Cache_Set($data, $id, array('topic_update', 'topic_new'), 60 * 60 * 24 * 3);
        }
        return $data;
    }

    /**
     * Получает список топиков по тегу с учетом языка сайта
     *
     * @param  string $sTag
     * @param  int    $iPage
     * @param  int    $iPerPage
     * @param  bool   $bAddAccessible Указывает на необходимость добавить в выдачу топики,
     *                                из блогов доступных пользователю. При указании false,
     *                                в выдачу будут переданы только топики из общедоступных блогов.
     * @return array
     */
    public function GetTopicsByTag($sTag, $iPage, $iPerPage, $bAddAccessible = true) {
        $aCloseBlogs = ($this->oUserCurrent && $bAddAccessible) ? $this->Blog_GetInaccessibleBlogsByUser($this->oUserCurrent) : $this->Blog_GetInaccessibleBlogsByUser();

        $s = serialize($aCloseBlogs);
        $id = "topic_tag_{$sTag}_{$iPage}_{$iPerPage}_{$s}"
                . (is_null($this->PluginL10n_L10n_GetLangForQuery()) ? '' : '_' . $this->PluginL10n_L10n_GetLangForQuery());
        if (false === ($data = $this->Cache_Get($id))) {
            $data = array('collection' => $this->oMapperTopic->GetTopicsByTag($sTag, $aCloseBlogs, $iCount, $iPage, $iPerPage, $this->PluginL10n_L10n_GetLangForQuery()), 'count' => $iCount);
            $this->Cache_Set($data, $id, array('topic_update', 'topic_new'), 60 * 60 * 24 * 2);
        }
        $data['collection'] = $this->GetTopicsAdditionalData($data['collection']);
        return $data;
    }

    /**
     * Возвращает количество топиков которые создал юзер
     *
     * @param unknown_type $sUserId
     * @param unknown_type $iPublish
     * @return unknown
     */
    public function GetCountTopicsPersonalByUser($sUserId, $iPublish) {
        $aFilter = array(
            'topic_publish' => $iPublish,
            'user_id' => $sUserId,
            'blog_type' => array('open', 'personal'),
        );
        /**
         * Если пользователь смотрит свой профиль, то добавляем в выдачу
         * закрытые блоги в которых он состоит
         */
        if ($this->oUserCurrent && $this->oUserCurrent->getId() == $sUserId) {
            $aFilter['blog_type'][] = 'close';
        }
        return parent::GetCountTopicsByFilter($aFilter);
    }

    /**
     * Получает список топиков по юзеру (язык не учитываем)
     *
     * @param unknown_type $sUserId
     * @param unknown_type $iPublish
     * @param unknown_type $iPage
     * @param unknown_type $iPerPage
     * @return unknown
     */
    public function GetTopicsPersonalByUser($sUserId, $iPublish, $iPage, $iPerPage) {
        $aFilter = array(
            'topic_publish' => $iPublish,
            'user_id' => $sUserId,
            'blog_type' => array('open', 'personal'),
        );
        /**
         * Если пользователь смотрит свой профиль, то добавляем в выдачу
         * закрытые блоги в которых он состоит
         */
        if ($this->oUserCurrent && $this->oUserCurrent->getId() == $sUserId) {
            $aFilter['blog_type'][] = 'close';
        }
        return parent::GetTopicsByFilter($aFilter, $iPage, $iPerPage);
    }

    /**
     * Получает топики по рейтингу и дате
     *
     * @param unknown_type $sDate
     * @param unknown_type $iLimit
     * @return unknown
     */
    public function GetTopicsRatingByDate($sDate, $iLimit=20) {
        /**
         * Получаем список блогов, топики которых нужно исключить из выдачи
         */
        $aCloseBlogs = ($this->oUserCurrent) ? $this->Blog_GetInaccessibleBlogsByUser($this->oUserCurrent) : $this->Blog_GetInaccessibleBlogsByUser();

        $s = serialize($aCloseBlogs);

        $id = "topic_rating_{$sDate}_{$iLimit}_{$s}"
                . (is_null($this->PluginL10n_L10n_GetLangForQuery()) ? '' : '_' . $this->PluginL10n_L10n_GetLangForQuery());
        if (false === ($data = $this->Cache_Get($id))) {
            $data = $this->oMapperTopic->GetTopicsRatingByDate(
                    $sDate, $iLimit, $aCloseBlogs, $this->PluginL10n_L10n_GetLangForQuery()
            );
            $this->Cache_Set($data, $id, array('topic_update'), 60 * 60 * 24 * 2);
        }
        $data = $this->GetTopicsAdditionalData($data);
        return $data;
    }

    /**
     * Set topic original id and author id
     *
     * @param ModuleTopic_EntityTopic $oTopic
     * @param ModuleTopic_EntityTopic $oTopicOriginal
     * @return boolean
     */
    public function SetTopicOriginal($oTopic, $oTopicOriginal) {
        return $this->oMapperTopic->SetTopicOriginal($oTopic, $oTopicOriginal);
    }

    /**
     * Рассылает уведомления о новом топике подписчикам блога
     *
     * @param unknown_type $oBlog
     * @param unknown_type $oTopic
     * @param unknown_type $oUserTopic
     */
    public function SendNotifyTopicNew($oBlog, $oTopic, $oUserTopic) {
        if (Config::Get('plugin.l10n.notify') == 'original') {
            if (is_null($oTopic->getTopicOriginalId())) {
                return parent::SendNotifyTopicNew($oBlog, $oTopic, $oUserTopic);
            } else {
                return;
            }
        } elseif (Config::Get('plugin.l10n.notify') != 'lang') {
            parent::SendNotifyTopicNew($oBlog, $oTopic, $oUserTopic);
        }

        $aBlogUsersResult = $this->Blog_GetBlogUsersByBlogId($oBlog->getId());

        $aBlogUsers = $aBlogUsersResult['collection'];

        foreach ($aBlogUsers as $oBlogUser) {
            if ($oBlogUser->getUserId() == $oUserTopic->getId()) {
                continue;
            }
            if ($oBlogUser->getUser()->getUserLang() != $oTopic->getTopicLang()) {
                continue;
            }
            $this->Notify_SendTopicNewToSubscribeBlog($oBlogUser->getUser(), $oTopic, $oBlog, $oUserTopic);
        }
        //отправляем создателю блога
        if ($oBlog->getOwnerId() != $oUserTopic->getId()) {
            $this->Notify_SendTopicNewToSubscribeBlog($oBlog->getOwner(), $oTopic, $oBlog, $oUserTopic);
        }
    }

    public function GetNestedTopics($oTopic)
    {
        $id = "nested_topic_" . $oTopic->GetId();

        if (false === ($data = $this->Cache_Get($id))) {
            if ($oTopic->getTopicOriginalId()) {
                $aTopics = $this->GetTopicTranslatesByTopicId($oTopic->getTopicOriginalId());
                if ($oTopicOriginal = $this->Topic_GetTopicById($oTopic->getTopicOriginalId())) {
                    $aTopics['collection'][$oTopic->getTopicOriginalId()] = $oTopicOriginal;
                }
            } else {
                $aTopics = $this->GetTopicTranslatesByTopicId($oTopic->getId());
                $aTopics['collection'][$oTopic->getId()] = $oTopic;
            }
            $data = $aTopics['collection'];
            $this->Cache_Set($data , $id, array('topic_update'), 60 * 60 * 24 * 2);
        }

        return $data;
    }

    public function increaseTopicCountComment($sTopicId)
    {
        if (Config::Get('plugin.l10n.allowed_collapse_comments')) {
            $oTopic = $this->Topic_GetTopicById($sTopicId);
            $commentsCount = 0;
            $aNestedTopics = $this->Topic_GetNestedTopics($oTopic);
            foreach ($aNestedTopics as $oTopicItem) {
                $commentsCount += $oTopicItem->getTopicCountComment();
            }

            foreach ($aNestedTopics as $oTopicItem) {
                $oTopicItem->setExtraData('collapsedCount', $commentsCount + 1);
                if ($oTopicItem->GetTopicId() == $sTopicId) {
                    $oTopicItem->SetTopicCountComment($oTopicItem->GetTopicCountComment() + 1);
                }
                $this->Topic_UpdateTopic($oTopicItem);
            }

            return true;
        }
        else {
            return parent::increaseTopicCountComment($sTopicId);
        }
    }

    public function UpdateTopicContent($oTopic)
    {
        $this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array('topic_update',"topic_update_user_{$oTopic->getUserId()}"));
        $this->Cache_Delete("topic_{$oTopic->getId()}");

        return $this->oMapperTopic->UpdateContent($oTopic);
    }

}
