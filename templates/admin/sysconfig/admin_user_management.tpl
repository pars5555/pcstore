<div style="position: relative;width: 100%;height:100%;overflow-y: auto">
    {if $ns.page_loaded == 0}
        <div style="text-align: center;">
            <button id="aum_load_page_button">Load page</button>
        </div>
    {else}
        <table id="aum_users_table" cellspacing="0" style="width:100%;">
            <thead>
                <tr>
                    <th></th>
                    <th>id</th>
                    <th>Email</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Phones</th>                        
                    <th>Points</th>
                    <th>Last SMS Code</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$ns.users item=userDto}
                    {assign var="user_id" value=$userDto->getId()}
                    {assign var="user_companies_names" value=$ns.userCompanies.$user_id}
                    {assign var="user_service_companies_names" value=$ns.userServiceCompanies.$user_id}
                    
                    <tr title="{if isset($user_companies_names)}companies: {', '|implode:$user_companies_names}{/if}
                        {if isset($user_service_companies_names)} service companies: {', '|implode:$user_service_companies_names}{/if}">
                        <td>
                            <button class="aum_delete_user_buttons" user_id="{$userDto->getId()}">X</button>
                        </td>
                        <td>{$userDto->getId()}</td>
                        <td>{$userDto->getEmail()}</td>
                        <td>{$userDto->getName()}</td>
                        <td>{$userDto->getLastName()}</td>
                        <td>{$userDto->getPhones()}</td>
                        <td>{$userDto->getPoint()}</td>
                        <td>{$userDto->getLastSmsValidationCode()}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    {/if}
</div>