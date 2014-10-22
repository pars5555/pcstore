<div>
    {assign var="picCount" value=$ns.itemDto->getPicturesCount()}
    {assign var="itemId" value=$ns.itemDto->getId()}
    {section name=item_picture_id start=0 loop=$picCount step=1}
        {assign var="picture_index" value=$smarty.section.item_picture_id.index+1}
        <div id="item_image_div^{$picture_index}" style="position: relative;float: left;margin:10px"> 
            <img src="{$SITE_PATH}/images/item_60_60/{$itemId}/{$picture_index}?{$smarty.now}" />
            <div class="ip_remove_item_picture_x" picture_index ="{$picture_index}" item_id="{$itemId}"
                 style="position:absolute; right: 0px;top: 0px;font-size: 14px;padding-right:2px;background: white;cursor: pointer;">
                x
            </div>
            {if $picture_index>1}
                <div class="ip_default_item_picture" picture_index ="{$picture_index}" item_id="{$itemId}"
                     style="position:absolute; right: 0px;bottom: 0px;font-size: 14px;padding-right:2px;background: white;cursor: pointer;">
                    Def
                </div>
            {/if}
        </div>
    {/section}
    <div style="clear: both"></div>
    <form id ="ip_add_item_picture_form" target="ip_upload_target" enctype="multipart/form-data" method="post"
          action="{$SITE_PATH}/dyn/company_item/do_add_remove_item_picture"
          style="position: relative;width:100%;height:100%;overflow-y: hidden">
        <button id ="ip_select_picture_button" class="button glyph"  style="margin-left: 10px;float: left">
            Add Picture ...
        </button>
        <input type="hidden" name="item_id" value="{$itemId}"/>
        <input type="hidden" name="action" value="add"/>
        <input id="ip_file_input" name="item_picture" type="file" accept="image/*" style="display:none"/>
        <iframe id="mi_upload_target" name="ip_upload_target" style="width:0;height:0;border:0px solid #fff;display: none;" ></iframe>
    </form>
</div>
