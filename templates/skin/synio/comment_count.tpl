<script type="text/javascript">
    jQuery('document').ready(function(){
        jQuery('#comments .comments-header h3').
            html('<span id="count-comments">{$iCountComment}</span> {$iCountComment|declension:$aLang.comment_declension:'russian'}');
    });
</script>