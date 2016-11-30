{* HEADER *}

<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="top"}
</div>

{* CONTENT FWIW *}

<div>
{if $code eq 0}
    <p>
        {ts 1=$kavoId}Assigned KAVO-ID %1.{/ts}
    </p>
    <p>
        {ts}You will need to refresh the page after clicking 'OK'.{/ts}
        {ts}(because I don't know how to update the view automatically.){/ts}
    </p>
{else}
    <p>
        {* TODO: split error code into distinct errors, because smarty cannot do bitwise operations. *}
        {ts}Some problems have occurred, error code {$code}.{/ts}
    </p>
    {if $missing ne ''}
        <p>{ts}Required fields are missing:{/ts}</p>
        <ul>
            {foreach from=$missing item=fieldName}
                <li>{ts}{$fieldName}{/ts}</li>
            {/foreach}
        </ul>
    {/if}
{/if}
</div>

{* FOOTER *}
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
