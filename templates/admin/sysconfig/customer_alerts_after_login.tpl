<div style="position: relative;width: 100%; height: 100%;overflow-y: auto;">
    <div style="height: 40px;white-space: nowrap;overflow: auto">
        <button id="ca_add_new_alert_button">Add new alert...</button>	
    </div>

    <div style="overflow: auto;position: absolute;top: 50px;right: 10px;bottom: 10px;left: 10px;">
        <table style="border-collapse:collapse; width: 100%" style="width:100%;">
            <thead>
                <tr>
                    <th>Delete</th>
                    <th>from</th>
                    <th>to</th>
                    <th>title</th>
                    <th>message</th>
                    <th>show count</th>
                    <th>viewed count</th>
                    <th>type</th>                  
                    <th>Preview</th>                  
                </tr>
            </thead>
            <tbody>

                {foreach from=$ns.dtos item=dto name=rd}
                    <tr style="{if $dto->getShowedCount()==0}background: #FFBFBF{else}{if $dto->getShowedCount()==$dto->getShowsCount()}background:#E5FFE5{else}background:#FFDE95{/if}{/if}">
                        <td class="f_ca_delete_row" pk="{$dto->getId()}">X</td>
                        <td>{$dto->getFromEmail()}</td>
                        <td>{$dto->getEmail()}</td>
                        <td>{$dto->getTitleFormula()}</td>
                        <td>{$dto->getMessageFormula()}</td>
                        <td>{$dto->getShowsCount()}</td>
                        <td>{$dto->getShowedCount()}</td>
                        <td>{$dto->getType()}</td>
                        <td class="f_ca_preview_row" style="cursor: pointer" pk="{$dto->getId()}"><img src="{$SITE_PATH}/img/preview.png"/></td>
                    </tr>           
                {/foreach}
            </tbody>
        </table>
    </div>
</div>