<div id="sc_system_config_tabs">
    <ul>
        <li><a href="#sc_actions_tab">Actions</a></li>
        <li><a href="#sc_table_view_tab">Table View</a></li>
        <li><a href="#sc_newletter_tab">Newsletter</a></li>
        <li><a href="#sc_gallery_tab">Gallery</a></li>
        <li><a href="#sc_search_statistics_tab">Search Statistics</a></li>
        <li><a href="#sc_user_management_tab">User Management</a></li>
        <li><a href="#sc_items_management_tab">Items Management</a></li>
        <li><a href="#sc_companies_management_tab">Companies Management</a></li>
        <li><a href="#sc_customer_alerts_after_login_tab">Customer Alerts</a></li>
    </ul>
    <div id="sc_actions_tab">
        <div id="f_admin_actions_view_container" style="overflow: hidden;height:100%">
            {nest ns=admin_actions_view}
        </div>
    </div>
    <div id="sc_table_view_tab" style="overflow: hidden;height:90%">
        <div id="f_admin_config_table_view_container" style="overflow: hidden;height:100%">
            {nest ns=table_view}
        </div>
    </div>
    <div id="sc_newletter_tab" style="overflow: hidden;height:90%">
        <div id="f_admin_config_newsletter_view_container" style="overflow: auto;height:100%">
            {include file="$TEMPLATE_DIR/admin/newsletters/newsletters.tpl" cartItem=$cartItem}
        </div>
    </div>
    <div id="sc_gallery_tab" style="overflow: hidden;height:90%">
        <div id="f_admin_config_gallery_container" style="overflow: hidden;height:100%">
            {nest ns=gallery_view}
        </div>
    </div>
    <div id="sc_search_statistics_tab" style="overflow: hidden;height:90%">
        <div id="f_admin_config_search_statistics_container" style="overflow: hidden;height:100%">
            {nest ns=search_statistics_view}
        </div>
    </div>
    <div id="sc_user_management_tab" style="overflow: hidden;height:90%">
        <div id="f_admin_config_user_management_container" style="overflow: hidden;height:100%">
            {nest ns=admin_user_management}
        </div>
    </div>
    <div id="sc_items_management_tab" style="overflow: hidden;height:90%">
        <div id="f_items_management_container" style="overflow: hidden;height:100%">
            {nest ns=items_management}
        </div>
    </div>
    <div id="sc_companies_management_tab" style="overflow: hidden;height:90%">
        <div id="f_companies_management_container" style="overflow: hidden;height:100%">
            {nest ns=companies_management}
        </div>
    </div>
    <div id="sc_customer_alerts_after_login_tab" style="overflow: hidden;height:90%">
        <div id="f_customer_alerts_after_login_container" style="overflow: hidden;height:100%">
            {nest ns=customer_alerts_after_login}
        </div>
    </div>
</div>

