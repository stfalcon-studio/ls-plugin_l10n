{include file='header.tpl'}
<h1>(текстовка)Здесь мы будем назначать переводчиков.</h1><br>
<ul>
{foreach from=$aNames item=sName}
    <li>{$sName}</li>
{/foreach}
</ul>
{include file='footer.tpl'}
