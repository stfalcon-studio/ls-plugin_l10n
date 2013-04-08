<?php

/**
 * Модуль Blog плагина L10n
 */
class PluginL10n_ModuleBlog extends PluginL10n_Inherit_ModuleBlog {

    /**
     * Список блогов по ID
     *
     * @param array $aUserId
     */
    public function GetBlogsByArrayId($aBlogId, $aOrder=null) {
        if (!$aBlogId) {
            return array();
        }
        if (Config::Get('sys.cache.solid')) {
            return $this->GetBlogsByArrayIdSolid($aBlogId);
        }
        if (!is_array($aBlogId)) {
            $aBlogId = array($aBlogId);
        }
        $aBlogId = array_unique($aBlogId);
        $aBlogs = array();
        $aBlogIdNotNeedQuery = array();
        /**
         * Делаем мульти-запрос к кешу
         */
//        $sLang = $this->PluginL10n_L10n_GetLangForQuery();
        $sLang = is_null($this->PluginL10n_L10n_GetLangFromUrl()) ? Config::Get('lang.current') : $this->PluginL10n_L10n_GetLangFromUrl();
        $id = $sLang . '_';
        $aCacheKeys = func_build_cache_keys($aBlogId, $id . 'blog_');
        if (false !== ($data = $this->Cache_Get($aCacheKeys))) {
            /**
             * проверяем что досталось из кеша
             */
            foreach ($aCacheKeys as $sValue => $sKey) {
                if (array_key_exists($sKey, $data)) {
                    if ($data[$sKey]) {
                        $aBlogs[$data[$sKey]->getId()] = $data[$sKey];
                    } else {
                        $aBlogIdNotNeedQuery[] = $sValue;
                    }
                }
            }
        }
        /**
         * Смотрим каких блогов не было в кеше и делаем запрос в БД
         */
        $aBlogIdNeedQuery = array_diff($aBlogId, array_keys($aBlogs));
        $aBlogIdNeedQuery = array_diff($aBlogIdNeedQuery, $aBlogIdNotNeedQuery);
        $aBlogIdNeedStore = $aBlogIdNeedQuery;
        if ($data = $this->oMapperBlog->GetBlogsByArrayId($aBlogIdNeedQuery, $sLang)) {
            foreach ($data as $oBlog) {
                /**
                 * Добавляем к результату и сохраняем в кеш
                 */
                $aBlogs[$oBlog->getId()] = $oBlog;
                $this->Cache_Set($oBlog, $id . "blog_{$oBlog->getId()}", array(), 60 * 60 * 24 * 4);
                $aBlogIdNeedStore = array_diff($aBlogIdNeedStore, array($oBlog->getId()));
            }
        }
        /**
         * Сохраняем в кеш запросы не вернувшие результата
         */
        foreach ($aBlogIdNeedStore as $sId) {
            $this->Cache_Set(null, $id . "blog_{$sId}", array(), 60 * 60 * 24 * 4);
        }
        /**
         * Сортируем результат согласно входящему массиву
         */
        $aBlogs = func_array_sort_by_keys($aBlogs, $aBlogId);
        return $aBlogs;
    }

    /**
     * Список блогов по ID, но используя единый кеш
     *
     * @param unknown_type $aBlogId
     * @return unknown
     */
    public function GetBlogsByArrayIdSolid($aBlogId, $aOrder = NULL) {
        if (!is_array($aBlogId)) {
            $aBlogId = array($aBlogId);
        }
        $aBlogId = array_unique($aBlogId);
        $aBlogs = array();
        $s = join(',', $aBlogId);

//        $sLang = $this->PluginL10n_L10n_GetLangForQuery();
        $sLang = is_null($this->PluginL10n_L10n_GetLangFromUrl()) ? Config::Get('lang.current') : $this->PluginL10n_L10n_GetLangFromUrl();
        $id = $sLang . '_';
        if (false === ($data = $this->Cache_Get($id . "blog_id_{$s}"))) {
            $data = $this->oMapperBlog->GetBlogsByArrayId($aBlogId, $sLang);
            foreach ($data as $oBlog) {
                $aBlogs[$oBlog->getId()] = $oBlog;
            }
            $this->Cache_Set($aBlogs, $id . "blog_id_{$s}", array("blog_update"), 60 * 60 * 24 * 1);
            return $aBlogs;
        }
        return $data;
    }

    /**
     * Получить блог по УРЛу
     *
     * @param unknown_type $sBlogUrl
     * @return unknown
     */
    public function GetBlogByUrl($sBlogUrl) {
//        $sLang = $this->PluginL10n_L10n_GetLangForQuery();
        $sLang = is_null($this->PluginL10n_L10n_GetLangFromUrl()) ? Config::Get('lang.current') : $this->PluginL10n_L10n_GetLangFromUrl();
        $id = $sLang . '_';
        if (false === ($id = $this->Cache_Get($id . "blog_url_{$sBlogUrl}"))) {
            if ($id = $this->oMapperBlog->GetBlogByUrl($sBlogUrl, $sLang)) {
                $this->Cache_Set($id, "blog_url_{$sBlogUrl}", array("blog_update_{$id}"), 60 * 60 * 24 * 2);
            } else {
                $this->Cache_Set(null, "blog_url_{$sBlogUrl}", array('blog_update', 'blog_new'), 60 * 60);
            }
        }
        return $this->GetBlogById($id);
    }

    /**
     *  Replace blog l10n
     *
     * @param PluginL10n_ModuleBlog_EntityBlog $oBlog
     * @return boolean
     */
    public function ReplaceBlogL10n(PluginL10n_ModuleBlog_EntityBlog $oBlog) {
        return $this->oMapperBlog->ReplaceBlogL10n($oBlog);
    }

    /**
     * Get blog localisation info
     *
     * @param PluginL10n_ModuleBlog_EntityBlog $oBlog
     * @param string $lang
     * @return PluginL10n_ModuleBlog_EntityBlog
     */
    public function GetBlogL10n($oBlog, $lang) {
        return $this->oMapperBlog->GetBlogL10n($oBlog, $lang);
    }

    
    /**
     * Обновляет описание блога
     *
     * @param ModuleBlog_EntityBlog $oBlog
     * @param type $lang
     * @return type
     */
    public function UpdateBlogDescription(ModuleBlog_EntityBlog $oBlog, $lang) {
        return $this->oMapperBlog->UpdateBlogDescription($oBlog, $lang);
    }

}