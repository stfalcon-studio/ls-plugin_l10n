<?php

/**
 * Маппер Blog модуля Blog плагина L10n
 */
class PluginL10n_ModuleBlog_MapperBlog extends PluginL10n_Inherit_ModuleBlog_MapperBlog {

    public function GetBlogsByArrayId($aArrayId, $aOrder = null, $sLang = null) {
        if (!is_array($aArrayId) or count($aArrayId) == 0) {
            return array();
        }

        if (is_null($sLang)) {
            return parent::GetBlogsByArrayId($aArrayId);
        }

        if (!is_array($aOrder)) {
            $aOrder = array($aOrder);
        }

        $sOrder = '';
        foreach ($aOrder as $key => $value) {
            $value = (string)$value;
            if (!in_array($key, array('blog_id', 'blog_title', 'blog_type', 'blog_rating', 'blog_count_user', 'blog_date_add'))) {
                unset($aOrder[$key]);
            } elseif (in_array($value, array('asc', 'desc'))) {
                $sOrder.=" {$key} {$value},";
            }
        }
        $sOrder = trim(trim($sOrder, ','));

        $sql = "SELECT
                    b.*,
                    bl.blog_title_l10n,
                    bl.blog_description_l10n,
                    bl.blog_url_l10n,
                    bl.blog_lang
                FROM
                    " . Config::Get('db.table.blog') . " as b
                LEFT JOIN (SELECT * FROM " . Config::Get('db.table.blog_l10n') . " as bl WHERE blog_lang = ?) as bl
                    ON bl.blog_id = b.blog_id
                WHERE
                    b.blog_id IN(?a)
                ORDER BY
                    { FIELD(b.blog_id, ?a) } ";
        if ($sOrder != '') {
            $sql .= $sOrder;
        }

        $aBlogs = array();
        if ($aRows = $this->oDb->select($sql, $sLang, $aArrayId, $sOrder == '' ? $aArrayId : DBSIMPLE_SKIP)) {
            foreach ($aRows as $aBlog) {
                $aBlogs[] = Engine::GetEntity('Blog', $aBlog);
            }
        }

        return $aBlogs;
    }

    public function GetBlogByUrl($sUrl, $sLang = null) {
        if (is_null($sLang)) {
            return parent::GetBlogByUrl($sUrl);
        }

        $sql = "SELECT
                    bl.blog_id
                FROM
                    " . Config::Get('db.table.blog_l10n') . " as bl
                WHERE
                    bl.blog_url_l10n = ?
                    AND bl.blog_lang = ?
                ";
        if ($aRow = $this->oDb->selectRow($sql, $sUrl, $sLang)) {
            return $aRow['blog_id'];
        }

        return parent::GetBlogByUrl($sUrl);
    }

    /**
     * Обновить описание блога
     *
     * @param ModuleBlog_EntityBlog $oBlog
     * @param string $sLang
     * @return boolean
     */
    public function UpdateBlogDescription(ModuleBlog_EntityBlog $oBlog, $sLang) {
        $sql = "UPDATE " . Config::Get('db.table.blog_l10n') . "
                SET
                    blog_description_l10n = ?
                WHERE
                    blog_id = ?d
                    AND blog_lang = ?
                ";
        if ($this->oDb->query($sql, $oBlog->getDescription(), $oBlog->getId(), $sLang)) {
            return true;
        }
        return false;
    }

    /**
     * Add blog localisation info to l10n
     *
     * @param PluginL10n_ModuleBlog_EntityBlog $oBlog
     * @return boolean
     */
    public function ReplaceBlogL10n($oBlog) {
        $sql = "REPLACE INTO " . Config::Get('db.table.blog_l10n') . "
                (
                    blog_id,
                    blog_title_l10n,
                    blog_description_l10n,
                    blog_url_l10n,
                    blog_lang
                )
                VALUES (?d, ?, ?, ?, ?)
		";

        if ($this->oDb->query($sql, $oBlog->getId(), $oBlog->getTitle(),
                        $oBlog->getDescription(), $oBlog->getUrl(), $oBlog->getLang())
        ) {
            return true;
        }
        return false;
    }

    /**
     * Get blog localisation info
     *
     * @param PluginL10n_ModuleBlog_EntityBlog $oBlog
     * @param string $sLang
     * @return PluginL10n_ModuleBlog_EntityBlog
     */
    public function GetBlogL10n($oBlog, $sLang) {
        $sql = "SELECT
                    *
                FROM
                    " . Config::Get('db.table.blog_l10n') . "
                WHERE
                    blog_id = ?d
                    AND blog_lang = ?
                ";

        if ($aRow = $this->oDb->selectRow($sql, $oBlog->getId(), $sLang)) {
                $oBlog->setTitleL10n($aRow['blog_title_l10n']);
                $oBlog->setDescriptionL10n($aRow['blog_description_l10n']);
                $oBlog->setUrlL10n($aRow['blog_url_l10n']);
                $oBlog->setLang($aRow['blog_lang']);
        }

        return $oBlog;
    }

}
