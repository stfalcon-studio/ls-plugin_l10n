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
     * Получает ссылку на топик с учетом его языка
     *
     * @return string
     */
    public function getUrl() {

        // Код языка топика
        $sLangCode = $this->PluginL10n_L10n_GetAliasByLang($this->getLang());

        if ($this->getBlog()->getType() == 'personal') {
            return Router::GetPath('blog', $sLangCode) . $this->getId() . '.html';
        } else {
            return Router::GetPath('blog', $sLangCode) . $this->getBlog()->getUrl() . '/' . $this->getId() . '.html';
        }
    }

}