<?php

class PluginL10n_ActionBlog extends PluginL10n_Inherit_ActionBlog
{
    public function Init()
    {
        parent::Init();
    }

    protected function RegisterEvent()
    {
        parent::RegisterEvent();
    }

    /**
     * Проверка на соответсвие коментария родительскому коментарию
     *
     * @param ModuleTopic_EntityTopic $oTopic
     * @param string $sText
     * @param ModuleComment_EntityComment $oCommentParent
     *
     * @return bool result
     */
    protected function CheckParentComment($oTopic, $sText, $oCommentParent) {

        $sParentId = 0;
        if ($oCommentParent) {
            $sParentId = $oCommentParent->GetCommentId();
        }

        $bOk = true;
        /**
         * Проверям на какой коммент отвечаем
         */
        if (!func_check($sParentId,'id')) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
            $bOk = false;
        }

        if ($sParentId) {
            /**
             * Проверяем существует ли комментарий на который отвечаем
             */
            if (!($oCommentParent)) {
                $this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
                $bOk = false;
            }
            /**
             * Проверяем из одного топика ли новый коммент и тот на который отвечаем
             */
            if (Config::Get('plugin.l10n.allowed_collapse_comments')) {
                $aTopicsId = array_keys($this->Topic_GetNestedTopics($oTopic));

                if (!in_array($oCommentParent->getTargetId(), $aTopicsId)) {
                    $this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
                    $bOk = false;
                }
            }
        } else {
            $sParentId = NULL;
        }

        /**
         * Проверка на дублирующий коммент
         */
        if ($this->Comment_GetCommentUnique($oTopic->getId(),'topic',$this->oUserCurrent->getId(),$sParentId,md5($sText))) {
            $this->Message_AddErrorSingle($this->Lang_Get('topic_comment_spam'),$this->Lang_Get('error'));
            $bOk = false;
        }

        $this->Hook_Run('comment_check_parent', array('oTopic'=>$oTopic, 'sText'=>$sText, 'oCommentParent'=>$oCommentParent, 'bOk'=>&$bOk));

        return $bOk;
    }

}