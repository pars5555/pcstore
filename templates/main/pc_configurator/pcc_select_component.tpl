{if !$ns.load_more}
    {if !isset($ns.only_update_component_container)}
        <div id="pcc_select_component_inner_container" style="width: 100%;" component_index="{$ns.componentIndex}">
            <div class="component_page_heder_div_style">
                <div style="float:left;cursor: pointer" {if $ns.componentIndex>0}id="pccsc_go_to_prev_component"{/if} component_index="{$ns.componentIndex}">
                    <img {if $ns.componentIndex<=1}class="grayscale"{/if} src="{$SITE_PATH}/img/pc_configurator/prev.png"/> 
                </div>
                {$ns.tab_header_info_text}
                <div style="float:right;cursor: pointer" {if $ns.componentIndex<14}id="pccsc_go_to_next_component"{/if} component_index="{$ns.componentIndex}">
                    <img {if $ns.componentIndex>=14}class="grayscale"{/if}src="{$SITE_PATH}/img/pc_configurator/next.png"/> 
                </div>
                <div style="margin: 12px 0">
                    {$ns.langManager->getPhraseSpan(131)}: <input id="pcc_search_component_text" type="search"/>
                </div>
            </div>

            <div id="component_selection_container" class="component_selection_container_style">
            {/if}
            {if (($ns.itemsDtos |@count )>0)}
                {include file="$TEMPLATE_DIR/main/pc_configurator/pcc_item_view_container_cycle.tpl" items = $ns.itemsDtos}
            {else}
                <div style="text-align: center;padding: 30px">
                    <h1>{$ns.langManager->getPhraseSpan(350)}</h1>
                </div>
            {/if}
            {if !isset($ns.only_update_component_container)}
            </div>
        </div>
    {/if}
    {include file="$TEMPLATE_DIR/main/pc_configurator/selected_components_hidden_values.tpl"}
{else}
    {*this part og html will be append to component_selection_container div in js*}
    {if (($ns.itemsDtos |@count )>0)}
        {include file="$TEMPLATE_DIR/main/pc_configurator/pcc_item_view_container_cycle.tpl" items = $ns.itemsDtos}
    {/if}
{/if}