<div style="position: relative;width: 100%;height:100%;overflow-y: auto;">
    {if $ns.page_loaded == 0}
        <div style="text-align: center;">
        <button id="cm_load_page_button">Load page</button>
        </div>
    {else}
        <table id="aum_users_table" cellspacing="0" style="width:100%;">
            <thead>
                <tr>
                    <th></th>
                    <th>id</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Passive</th>                        
                    <th>Hidden</th>                        
                    <th>Blocked</th>                    
                    <th>Edit</th>                    
                </tr>
            </thead>
            <tbody>
                {foreach from=$ns.companies item=companyDto}
                    <tr>
                        <td>
                            <button class="cm_delete_company_buttons" company_id="{$companyDto->getId()}">X</button>
                        </td>
                        <td>{$companyDto->getId()}</td>
                        <td>{$companyDto->getName()}</td>
                        <td>{$companyDto->getEmail()}</td>
                        <td>{$companyDto->getPassword()}</td>
                        <td>
                            <input type="checkbox" {if $companyDto->getPassive() == 1}checked{/if}/>
                        </td>
                        <td>
                            <input type="checkbox" {if $companyDto->getHidden() == 1}checked{/if}/>
                        </td>
                        <td>
                            <input type="checkbox" {if $companyDto->getBlocked() == 1}checked{/if}/>
                        </td> 
                        <td>
                        <button class="cm_edit_company_buttons" company_id="{$companyDto->getId()}">Edit</button>
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    {/if}
</div>
