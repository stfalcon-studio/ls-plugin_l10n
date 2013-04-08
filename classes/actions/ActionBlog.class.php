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
     * Обработка добавление комментария к топику
     *
     */
    protected function SubmitComment()
    {
        /**
         * Проверям авторизован ли пользователь
         */
        if (!$this->User_IsAuthorization()) {
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization'),$this->Lang_Get('error'));
            return;
        }
        /**
         * Проверяем топик
         */
        if (!($oTopic=$this->Topic_GetTopicById(getRequest('cmt_target_id')))) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
            return;
        }
        /**
         * Возможность постить коммент в топик в черновиках
         */
        if (!$oTopic->getPublish() and $this->oUserCurrent->getId()!=$oTopic->getUserId() and !$this->oUserCurrent->isAdministrator()) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
            return;
        }
        /**
         * Проверяем разрешено ли постить комменты
         */
        if (!$this->ACL_CanPostComment($this->oUserCurrent) and !$this->oUserCurrent->isAdministrator()) {
            $this->Message_AddErrorSingle($this->Lang_Get('topic_comment_acl'),$this->Lang_Get('error'));
            return;
        }
        /**
         * Проверяем разрешено ли постить комменты по времени
         */
        if (!$this->ACL_CanPostCommentTime($this->oUserCurrent) and !$this->oUserCurrent->isAdministrator()) {
            $this->Message_AddErrorSingle($this->Lang_Get('topic_comment_limit'),$this->Lang_Get('error'));
            return;
        }
        /**
         * Проверяем запрет на добавления коммента автором топика
         */
        if ($oTopic->getForbidComment()) {
            $this->Message_AddErrorSingle($this->Lang_Get('topic_comment_notallow'),$this->Lang_Get('error'));
            return;
        }
        /**
         * Проверяем текст комментария
         */
        $sText=$this->Text_Parser(getRequest('comment_text'));
        if (!func_check($sText,'text',2,10000)) {
            $this->Message_AddErrorSingle($this->Lang_Get('topic_comment_add_text_error'),$this->Lang_Get('error'));
            return;
        }
        /**
         * Проверям на какой коммент отвечаем
         */
        $sParentId=(int)getRequest('reply');
        if (!func_check($sParentId,'id')) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
            return;
        }
        $oCommentParent=null;
        if ($sParentId!=0) {
            /**
             * Проверяем существует ли комментарий на который отвечаем
             */
            if (!($oCommentParent=$this->Comment_GetCommentById($sParentId))) {
                $this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
                return;
            }
            /**
             * Проверяем из одного топика ли новый коммент и тот на который отвечаем
             */
            if (Config::Get('plugin.l10n.allowed_collapse_comments')) {
                    $aTopicsId = array_keys($this->Topic_GetNestedTopics($oTopic));

                if (!in_array($oCommentParent->getTargetId(), $aTopicsId)) {
                    $this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
                    return;
                }
            }
        } else {
            /**
             * Корневой комментарий
             */
            $sParentId=null;
        }
        /**
         * Проверка на дублирующий коммент
         */
        if ($this->Comment_GetCommentUnique($oTopic->getId(),'topic',$this->oUserCurrent->getId(),$sParentId,md5($sText))) {
            $this->Message_AddErrorSingle($this->Lang_Get('topic_comment_spam'),$this->Lang_Get('error'));
            return;
        }
        /**
         * Создаём коммент
         */
        $oCommentNew=Engine::GetEntity('Comment');
        $oCommentNew->setTargetId($oTopic->getId());
        $oCommentNew->setTargetType('topic');
        $oCommentNew->setTargetParentId($oTopic->getBlog()->getId());
        $oCommentNew->setUserId($this->oUserCurrent->getId());
        $oCommentNew->setText($sText);
        $oCommentNew->setDate(date("Y-m-d H:i:s"));
        $oCommentNew->setUserIp(func_getIp());
        $oCommentNew->setPid($sParentId);
        $oCommentNew->setTextHash(md5($sText));
        $oCommentNew->setPublish($oTopic->getPublish());

        if ($oCommentParent && $oCommentNew->getTargetType() == 'topic') {
            $oCommentNew->setTargetId($oCommentParent->getTargetId());
            $oCommentNew->setTargetParentId($oCommentParent->getTargetParentId());
        }

        /**
         * Добавляем коммент
         */
        $this->Hook_Run('comment_add_before', array('oCommentNew'=>$oCommentNew,'oCommentParent'=>$oCommentParent,'oTopic'=>$oTopic));
        if ($this->Comment_AddComment($oCommentNew)) {
            $this->Hook_Run('comment_add_after', array('oCommentNew'=>$oCommentNew,'oCommentParent'=>$oCommentParent,'oTopic'=>$oTopic));

            $this->Viewer_AssignAjax('sCommentId',$oCommentNew->getId());
            if ($oTopic->getPublish()) {
                /**
                 * Добавляем коммент в прямой эфир если топик не в черновиках
                 */
                $oCommentOnline=Engine::GetEntity('Comment_CommentOnline');
                $oCommentOnline->setTargetId($oCommentNew->getTargetId());
                $oCommentOnline->setTargetType($oCommentNew->getTargetType());
                $oCommentOnline->setTargetParentId($oCommentNew->getTargetParentId());
                $oCommentOnline->setCommentId($oCommentNew->getId());

                $this->Comment_AddCommentOnline($oCommentOnline);
            }
            /**
             * Сохраняем дату последнего коммента для юзера
             */
            $this->oUserCurrent->setDateCommentLast(date("Y-m-d H:i:s"));
            $this->User_Update($this->oUserCurrent);

            /**
             * Список емайлов на которые не нужно отправлять уведомление
             */
            $aExcludeMail=array($this->oUserCurrent->getMail());
            /**
             * Отправляем уведомление тому на чей коммент ответили
             */
            if ($oCommentParent and $oCommentParent->getUserId()!=$oTopic->getUserId() and $oCommentNew->getUserId()!=$oCommentParent->getUserId()) {
                $oUserAuthorComment=$oCommentParent->getUser();
                $aExcludeMail[]=$oUserAuthorComment->getMail();
                $this->Notify_SendCommentReplyToAuthorParentComment($oUserAuthorComment,$oTopic,$oCommentNew,$this->oUserCurrent);
            }
            /**
             * Отправка уведомления автору топика
             */
            $this->Subscribe_Send('topic_new_comment',$oTopic->getId(),'notify.comment_new.tpl',$this->Lang_Get('notify_subject_comment_new'),array(
                'oTopic' => $oTopic,
                'oComment' => $oCommentNew,
                'oUserComment' => $this->oUserCurrent,
            ),$aExcludeMail);
            /**
             * Добавляем событие в ленту
             */
            $this->Stream_write($oCommentNew->getUserId(), 'add_comment', $oCommentNew->getId(), $oTopic->getPublish() && $oTopic->getBlog()->getType()!='close');
        } else {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
        }
    }
}