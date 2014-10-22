<div id="pc_configurator_header" style="width: 100%;height: 120px;background: white;position: relative;display: block">
    <div style="background: white; margin-top: 10px; margin-left: 8px;float:left">
        {section name=pid start=1 loop=$ns.pcc_components_count+1 step=1}
            <div id="component_icon_a_link_{$smarty.section.pid.index}" style="float: left;text-decoration: none;" class="component_icon_a_link_style">
                <div id="component_icon_div_{$smarty.section.pid.index}" class="component_icon_div_style">
                    <div style="font-size: 11px;color: black;word-wrap: break-word; text-align: center; position: relative; padding: 75px 0 0; background: transparent url({$SITE_PATH}/img/pc_configurator/{$smarty.section.pid.index}.png) no-repeat 50% 10px;" >
                        <div id="component_icon_flag_div_{$smarty.section.pid.index}"></div>
                        {assign var="index" value=$smarty.section.pid.index-1}
                        {$ns.langManager->getPhraseSpan($ns.component_display_names.$index)}
                    </div>
                </div>
            </div>
        {/section}
    </div>

</div>

<div style="clear: both"></div>
<div id="pc_configurator_body" style="left: 0px; width: 100%;background:white;float:left;">
    <div id="pc_configurator_left_side_panel" style="width: {$ns.left_side_panel_width}px;min-height: 584px;position: relative;overflow: hidden;float:left;border-right: 1px solid #666;margin-right: -1px;">
        <div id="pcc_components_container_overlay" class="modal_loading_div"></div>
        <div id="pcc_components_container"	style="width: 100%;">
            {nest ns = pcc_select_case}
        </div>
    </div>

    <div id="pc_configurator_right_side_panel" style="float:left;width: {$ns.wholePageWidth-$ns.left_side_panel_width-10}px;background-color: #fff;border-left: 1px solid #666;">

        <div id="pcc_total_calculation_container">
            {*nest ns = pcc_total_calculations*}
        </div>		
        <div id="pcc_autoconfiguration_container">
            {nest ns = pcc_auto_configuration_by_filters}
        </div>	

        <div id="pcc_selected_item_details_container"></div>
        <div style="clear: both"></div>

    </div>
    <div style="clear:both"></div>
</div>
<div style="clear: both"></div>
{include file="$TEMPLATE_DIR/main/pc_configurator/pcc_content_footer.tpl"}

<div style="clear: both"></div>
{if $ns.configurator_mode_edit_cart_row_id > 0}
    <input id='configurator_mode_edit_cart_row_id' type="hidden" value="{$ns.configurator_mode_edit_cart_row_id}"/>
{/if}
