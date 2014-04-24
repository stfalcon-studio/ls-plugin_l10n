<?php

class PluginL10n_ModuleTopic_EntityTopic extends PluginL10n_Inherit_ModuleTopic_EntityTopic
{

    /**
     * Отдает блог в зависимости от языка рецепта
     *
     * @return ModuleBlog_EntityBlog
     */
    public function getBlog() {
        $oBlog = $this->_aData['blog'];
        if ($this->getLang() != $oBlog->getLang()) {
            $this->setBlog($this->PluginL10n_ModuleBlog_GetBlogL10n($oBlog, $this->getLang()));
        }

        return $this->_aData['blog'];
    }

    /**
     * Получает язык топика
     *
     * @return string
     */
    public function getLang() {
        return $this->_aData['topic_lang'];
    }

    /**
     * Возвращает полный URL до топика
     *
     * @return string
     */
    public function getUrl($sLang = null)
    {
        if ($sLang && in_array($sLang, $this->PluginL10n_L10n_GetAllowedLangs())) {
            $sLang = $this->PluginL10n_L10n_GetAliasByLang($sLang);
        }

        if ($this->getBlog()->getType() == 'personal') {
            return Router::GetPath('blog', $sLang) . $this->getId() . '.html';
        } else {
            return Router::GetPath('blog', $sLang) . $this->getBlog()->getUrl() . '/' . $this->getId() . '.html';
        }
    }

    /**
     * Возвращает полный URL до страницы редактировани топика
     *
     * @return string
     */
    public function getUrlEdit($sLang = null)
    {
        if ($sLang && in_array($sLang, $this->PluginL10n_L10n_GetAllowedLangs())) {
            $sLang = $this->PluginL10n_L10n_GetAliasByLang($sLang);
        }

        return Router::GetPath($this->getType(), $sLang) . 'edit/' . $this->getId() . '/';
    }

}