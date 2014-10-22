<input type="hidden" value='{$ns.allCompaniesDtosToArray}' id="all_companies_dtos_to_array_json"/>
<input type="hidden" value='{$ns.allCompaniesBranchesDtosToArray}' id="all_companies_branches_dtos_to_array_json"/>
<div id="cl_gmap" style="height:300px"></div>

{if $ns.userLevel === $ns.userGroupsCompany}
    <div style="text-align: left;margin:30px">
        {$ns.langManager->getPhraseSpan(609)}</br></br>
        <span style="font-size: 16px">
            {$ns.langManager->getPhraseSpan(610)} <span style="font-size: 20px;color:#AA0000">{$ns.user->getAccessKey()}</span>
        </span>
    </div>
{/if}

<div class="avo_style_companyBox" id="cl_tabs">
    <ul id="cl_tabs_ul" class="avo_style_tab">
        <li><a href="#cl_imprter_companies_tab" onclick='return false;'>{$ns.langManager->getPhraseSpan(578)}</a></li>
        <li><a href="#cl_service_companies_tab" onclick='return false;'>{$ns.langManager->getPhraseSpan(579)}</a></li>
    </ul>
    <div class="avo_style_companyWrapperBox" id="cl_imprter_companies_tab">
        {include file="$TEMPLATE_DIR/main/sub_templates/importer_companies_list.tpl"}
    </div>
    <div id="cl_service_companies_tab">
        {include file="$TEMPLATE_DIR/main/sub_templates/service_companies_list.tpl"}
    </div>
</div>	

