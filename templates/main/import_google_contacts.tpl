<div id="igc_container" title="{$ns.langManager->getPhrase(497)}">
    {foreach  from=$ns.emails item=email name=foo}
        <div>
            <input type="checkbox" id="google_contact_checkbox_{$smarty.foreach.foo.index}" email="{$email}"/>
            <label for="google_contact_checkbox_{$smarty.foreach.foo.index}">{$email}</label>
        </div>
    {/foreach}
</div>
