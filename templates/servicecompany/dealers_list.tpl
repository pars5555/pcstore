<div style="position: relative; width: 100%;">
	<div style="text-align: center;margin: 30px">
		<h1>{$ns.langManager->getPhraseSpan(59)}</h1>
	</div>
	<div id="dl_dealers_container" style="float:left; width:100%; position: relative;">
		{if (($ns.dealers |@count )>0 )}

			<table cellspacing="0" style="width:100%;">

				<thead>
					<tr>
						<th>{$ns.langManager->getPhraseSpan(60)}</th>
						<th>{$ns.langManager->getPhraseSpan(61)}</th>
						<th >{$ns.langManager->getPhraseSpan(62)}</th>
						<th >{$ns.langManager->getPhraseSpan(63)}</th>
					</tr>
				</thead>
				<tbody>

					{foreach from=$ns.dealers item=dealer name=dl}
						<tr>
							<td >{$smarty.foreach.dl.index+1}</td>
							<td >{$dealer->getUserName()} {$dealer->getUserLastName()}</td>					
							<td ><span style="display: inline-block;"> 
									{assign var=foo value=","|explode:$dealer->getUserPhones()}
									{$foo[0]}
									<br/>
									{$foo[1]}
									<br/>
									{$foo[2]} </span> </td>
							<td >{$dealer->getUserEmail()}</td>
							<td ><a class="red_a_style" style="color:red" id="remove_dealer_from_company^{$dealer->getUserId()}" href="javascript:void(0);">{$ns.langManager->getPhraseSpan(148)}</a></td>
						</tr>
					{/foreach}
				</tbody>
			</table>
		{else}
			<div style="top:30%; width: 100%;  text-align: center;position: absolute;">
				<h2>{$ns.langManager->getPhraseSpan(64)}</h2>
			</div>
		{/if}

	</div>
		<div style="clear: both"></div>
</div>

