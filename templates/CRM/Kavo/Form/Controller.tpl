{* HEADER *}

<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="top"}
</div>

{* CONTENT FWIW *}

<div>
{if count($codes) == 0}
    <p>
        {ts 1=$kavoId}Assigned KAVO-ID %1.{/ts}
    </p>
    {if $entityName == 'contact'}
    <p>
        {ts}You will need to refresh the page after clicking 'OK'.{/ts}
        {ts}(because I don't know how to update the view automatically.){/ts}
    </p>
    {/if}
{else}
    <p>
        {* TODO: split error code into distinct errors, because smarty cannot do bitwise operations. *}
        {ts}Some problems have occurred.{/ts}
    </p>
    <ul>
        {foreach from=$codes item=code}
            <li>
                {* I wonder whether I could use the constants from CRM_Kavo_Error in this template *}
                {if $code == 1}
                    {ts}Some required contact fields are missing.{/ts}
                {/if}
                {if $code == 2}
                    {ts}Only individuals can get a KAVO account.{/ts}
                {/if}
                {if $code == 4}
                    {ts 1=$entityName}This %1 already has a KAVO-ID.{/ts}
                {/if}
                {if $code == 8}
                    {ts}A KAVO-ID already exists for this email address.{/ts}
                {/if}
                {if $code == 16}
                    {ts}A responsible contact for the course is missing.{/ts}
                {/if}
                {if $code == 32}
                    {ts}The course should have a location with an address.{/ts}
                {/if}
                {if $code == 128}
                    {ts}The KAVO API returned an unexpected result.{/ts}
                {/if}
            </li>
        {/foreach}
    </ul>
    {if count($missing) != 0}
        <p>{ts}Missing fields:{/ts}</p>
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
