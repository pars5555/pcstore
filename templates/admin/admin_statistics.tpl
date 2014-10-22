{foreach from=$ns.onlineusers item=ol name=cl}
{$ol->getId()} {$ol->getEmail()} {$ol->getLoginDateTime()} {$ol->getIp()} {$ol->getCountry()}<br/>
{/foreach}
