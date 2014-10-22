<div class="avo_style_upload_price_container">
    {if ($ns.userLevel === $ns.userGroupsAdmin)}
        <div class="avo_style_uploadTopBox">
            {include file="$TEMPLATE_DIR/main/orders/admin_header.tpl"}
        </div>
    {/if}

    {if isset($ns.customerMessages)}
        <div class="avo_style_uploadTopBox">
            <div style="line-height: 20px;padding: 10px; color: #00A800">
                {foreach from=$ns.customerMessages item=customerMessage} 
                    <div>{$customerMessage}</div>
                {/foreach}
            </div>
        </div>
    {/if}
    {if isset($ns.customerErrorMessages)}
        <div class="avo_style_uploadTopBox">
            <div style="line-height: 20px;padding: 10px; color:#FF0000">
                {foreach from=$ns.customerErrorMessages item=customerMessage} 
                    <div>{$customerMessage}</div>
                {/foreach}
            </div>
        </div>
    {/if}
    <div id="yo_table_container" class="avo_style_uploadWrapperBox">		
        {include file="$TEMPLATE_DIR/main/orders/table_header.tpl"}
        {foreach from=$ns.groupOrdersByOrderIdAndBundleId key=orderId item=orderItems name=foo}			
            <div  style="{if not $smarty.foreach.foo.last}border-bottom: 2px solid #777777{/if}">

                {*order head row*}
                {assign var="orderInfo" value=$orderItems|@current}		
                {if is_array($orderInfo)}			
                    {assign var="orderInfo" value=$orderInfo|@current}
                {/if}

                {include file="$TEMPLATE_DIR/main/orders/orders_head_row.tpl"}						
                <div style="clear: both"> </div>
                <div id="order_container_{$orderInfo->getId()}" style="width:100%;position: relative;display: none">

                    {include file="$TEMPLATE_DIR/main/orders/orders_head_row_table_head.tpl"}
                    <div style="clear: both"> </div>            
                    {foreach from=$orderItems key=bundleId item=orderItem name=foo}                
                        <div  style="{if not $smarty.foreach.foo.last}border-bottom: 1px solid #aaa{/if}">				                    
                            {if is_array($orderItem)}
                                <div  style="border-bottom: 1px solid #aaa;background-color: #FFFFCC">
                                    {include file="$TEMPLATE_DIR/main/orders/order_bundle_item_head_row.tpl"}											



                                    <div style="clear: both"> </div>
                                    <div id="bundle_container_{$orderItem[0]->getOrderDetailsBundleId()}" style="width:100%;position: relative;display: none">		
                                        {include file="$TEMPLATE_DIR/main/orders/order_bundle_item.tpl"}
                                        <div style="clear: both"> </div>
                                    </div>	
                                    <div style="clear: both"> </div>
                                </div>			
                            {else}
                                {include file="$TEMPLATE_DIR/main/orders/order_item.tpl"}
                            {/if}
                            <div style="clear: both"> </div>
                        </div>
                        <div style="clear:both"> </div>
                    {/foreach}			
                    <div style="clear: both"> </div>
                </div>

            </div>
        {/foreach }
    </div>
</div>