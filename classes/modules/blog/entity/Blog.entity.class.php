<?php

class PluginL10n_ModuleBlog_EntityBlog extends PluginL10n_Inherit_ModuleBlog_EntityBlog {

    public function getTitle() {
        if (isset($this->_aData['blog_title_l10n']) && strlen($this->_aData['blog_title_l10n'])) {
            return $this->_aData['blog_title_l10n'];
        }

        return parent::getTitle();
    }

    public function getDescription() {
        if (isset($this->_aData['blog_description_l10n']) && strlen($this->_aData['blog_description_l10n'])) {
            return $this->_aData['blog_description_l10n'];
        }

        return parent::getDescription();
    }

    public function getUrl() {
        if (isset($this->_aData['blog_url_l10n']) && strlen($this->_aData['blog_url_l10n'])) {
            return $this->_aData['blog_url_l10n'];
        }

        return parent::getUrl();
    }

    public function getLang() {
        if (isset($this->_aData['blog_lang']) && strlen($this->_aData['blog_lang'])) {
            return $this->_aData['blog_lang'];
        }
        return null;
    }

    public function setLang($data) {
        $this->_aData['blog_lang'] = $data;
    }

}
