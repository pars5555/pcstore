<div style="border-top-right-radius:20px; overflow: auto;">
	<form name="invite_user_form" id="invite_user_form" style="margin: 10px;padding:10px; ">
		<a>{$ns.langManager->getPhraseSpan(146)}: </a>
		<input name="invited_user_email" type="text" size="40"/>
		<button id= "um_invite_button">{$ns.langManager->getPhraseSpan(182)}</button>

	</form>	
        <span id="googleGetAllContactsBtn">    
            <img style="width: 35px;cursor: pointer"  src="{$SITE_PATH}/img/social/gp.png"/>
        </span>
</div>
{if (($ns.pendingUsers |@count )>0)}

	<div style="text-align: center;margin: 40px">
		<h1>{$ns.langManager->getPhraseSpan(147)}</h1>
	</div>
	<div>
        {assign var="yesterday" value='-1 day'|strtotime}
        {assign var="yesterday" value=$yesterday|date_format:"%Y-%m-%d %H:%M:%S"}
		<table border="1px" style="width:100%;">
			<th>{$ns.langManager->getPhraseSpan(60)}</th>
			<th>{$ns.langManager->getPhraseSpan(3)}</th>
			<th>Resend</th>
			{foreach from=$ns.pendingUsers item=pu_dto name=pu}
				<tr	class="{if $smarty.foreach.pu.index % 2 == 1}even{/if}" >
					<td style="text-align: center">{$smarty.foreach.pu.index+1}</td>
					<td style="text-align: center">{$pu_dto->getPendingSubUserEmail()}</td>			
					<td style="text-align: center">
                        {if $pu_dto->getLastSent()<$yesterday}
                        <button class="button f_resendinvitation" pk="{$pu_dto->getId()}">{$ns.langManager->getPhraseSpan(612)}</button>
                       {/if}
                    </td>			
        

				</tr>
			{/foreach}
		</table>

	</div>

{else}
	<div style="text-align: center;margin:20px">
		<h1>{$ns.langManager->getPhraseSpan(150)}</h1>
	</div>
{/if}
