<div id="ic_contact_tabs" title="{$ns.langManager->getPhrase(497)}">
	<ul>
		<li><a href="#ic_companies_tab">{$ns.langManager->getPhraseSpan(494)}</a></li>
		{if $ns.userLevel !== $ns.userGroupsUser  && $ns.userLevel !== $ns.userGroupsServiceCompany}
		<li><a href="#ic_dealers_tab">{$ns.langManager->getPhraseSpan(495)}</a></li>
		{/if}
		<li><a href="#ic_admins_tab">{$ns.langManager->getPhraseSpan(496)}</a></li>

	</ul>
	<div id="ic_companies_tab">
		<div>
			{foreach from=$ns.allCompanies item=company name=cl}
				<div style="margin: 5px;">
					<input id="ic_company_{$company->getId()}" type="checkbox" cust_email="{$company->getEmail()}" cust_name="{$company->getName()}" cust_type="company"/>
					<label for="ic_company_{$company->getId()}">{$company->getName()}</label>              
				</div>
			{/foreach}
		</div>

	</div>
		{if $ns.userLevel !== $ns.userGroupsUser && $ns.userLevel !== $ns.userGroupsServiceCompany}
	<div id="ic_dealers_tab">
		{if ($ns.userLevel === $ns.userGroupsCompany)}
			{foreach from=$ns.allDealers item=dealer name=cl}
				<div style="margin: 5px;">
					<input id="ic_dealer_{$dealer->getUserId()}" type="checkbox" cust_email="{$dealer->getUserEmail()}" cust_name="{$dealer->getUserName()} {$dealer->getUserLastName()}" cust_type="user"/>
					<label for="ic_dealer_{$dealer->getUserId()}">{$dealer->getUserName()} {$dealer->getUserLastName()} ({$dealer->getUserEmail()})</label>              
				</div>
			{/foreach}
		{/if}
		{if ($ns.userLevel === $ns.userGroupsAdmin)}
			{foreach from=$ns.allUsers item=user name=cl}
				<div style="margin: 5px;">
					<input id="ic_dealer_{$user->getId()}" type="checkbox" cust_email="{$user->getEmail()}" cust_name="{$user->getName()} {$user->getLastName()}" cust_type="user"/>
					<label for="ic_dealer_{$user->getId()}">{$user->getName()} {$user->getLastName()} ({$user->getEmail()})</label>              
				</div>
			{/foreach}	
		{/if}
	</div>
	{/if}
	<div id="ic_admins_tab">
		{foreach from=$ns.allAdmins item=admin name=cl}
			<div style="margin: 5px;">
				<input id="ic_admin_{$admin->getId()}" type="checkbox" cust_email="{$admin->getEmail()}" cust_name="{$admin->getTitle()}" cust_type="admin"/>
				<label for="ic_admin_{$admin->getId()}">{$admin->getTitle()}</label>              
			</div>
		{/foreach}
	</div>

</div>
