{if (($ns.subUsers |@count )>0)}

	<div style="text-align: center;margin:40px 0 ">
		<h1>{$ns.langManager->getPhraseSpan(143)}</h1>
	</div>
	<div>
		<table border="1px" style="width:100%;">
			<th>{$ns.langManager->getPhraseSpan(60)}</th>
			<th>{$ns.langManager->getPhraseSpan(61)}</th>
			<th>{$ns.langManager->getPhraseSpan(3)}</th>		
				{foreach from=$ns.subUsers item=user name=su}
				<tr		class="{if $smarty.foreach.su.index % 2 == 1}even{/if}" >
					<td style="text-align: center">{$smarty.foreach.su.index+1}</td>
					<td style="text-align: center">{$user->getUserName()} {$user->getUserLastName()}</td>
					<td style="text-align: center">{if $user->getUserLoginType()!=='pcstore'}{$user->getUserLoginType()} {/if}{$ns.userManager->getRealEmailAddress($user->getSubUserId())}</td>
					<!--td><a class="red_a_style" style="color:red" id="remove_sub_user^{$user->getSubUserId()}" href="javascript:void(0);" title="Click to remove user.">remove...</td-->

				</tr>
			{/foreach}
		</table>

	</div>

{else}
	<div style="text-align: center;margin: 20px">
		<h1>{$ns.langManager->getPhraseSpan(145)}</h1>
	</div>
{/if}
