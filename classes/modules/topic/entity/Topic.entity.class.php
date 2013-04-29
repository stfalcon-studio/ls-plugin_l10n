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
        return isset($this->_aData['topic_lang']) ? $this->_aData['topic_lang'] : '';
    }

    /**
     * Get values from extra fields directly
     *
     * @param string $keyName
     * @param mixed $value
     *
     * @return bool result
     */
    public function setExtraData($keyName, $value)
    {
        return $this->setExtraValue($keyName, $value);
    }

    /**
     * Get values from extra fields directly (by key)
     *
     * @param string $keyName
     *
     * @return mixed
     */
    public function getExtraData($keyName)
    {
        return $this->getExtraValue($keyName);
    }
}