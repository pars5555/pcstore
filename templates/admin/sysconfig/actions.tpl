<div style="padding:20px">
    <a href="javascript:void(0);" id="update_all_amd_items_prices_link" class="button">update all AMD items prices</a>
    <a href="javascript:void(0);" id="update_all_companies_price_texts_link" class="button">update all Companies Price Texts</a>
    <a href="javascript:void(0);" id="delete_all_unnecessary_items_pictures_link" class="button">Delete All Unnecessary Items' Pictures</a>
    <a href="javascript:void(0);" id="delete_old_hidden_items" class="button">Delete old hidden items...</a>
    <a href="javascript:void(0);" id="deploy_latest_pcstore_changes" class="button">Deploy pcstore latest changes</a>
</div>

<div id="select_company_to_update_price_text_dialog_root_div" style="display: none">
    <select id="select_company_to_update_price_text" onkeyup="this.blur();
            this.focus();" class="cmf-skinned-select cmf-skinned-text">
        {html_options options=$ns.companies selected=0}
    </select>
</div>
