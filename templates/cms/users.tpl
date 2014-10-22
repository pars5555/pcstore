{include file="$TEMPLATE_DIR/cms/left_panel.tpl"}
<table>
    <thead>
        <tr>
            {foreach from=$ns.visible_fields_names item=fieldName}
                <th>
                    {$fieldName}    
                </th>
            {/foreach}
        </tr>
    </thead>
    <tbody>
        {foreach from=$ns.users item=userDto}
            <tr>
                {foreach from=$ns.visible_fields_names item=fieldName}
                    <td>
                        {$userDto->$fieldName}    
                    </td>
                {/foreach}
            </tr>
        {/foreach}
    </tbody>
</table>