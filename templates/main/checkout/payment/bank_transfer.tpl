<div style="padding: 10px;">
    {$ns.langManager->getPhraseSpan(621)}
</div>
<table class="f_company_details">
    <tr>
        <td>{$ns.langManager->getPhraseSpan(623)}</td>
        <td><input type="text" name="cho_company_name" id="cho_company_name"  value="{$ns.req_params.cho_company_name}"/></td>
        <td><div style="color:red;display: none;" class="f_error" id="cho_company_name_error">{$ns.langManager->getPhraseSpan(635)}</div></td>
    </tr>
    <tr>
        <td>{$ns.langManager->getPhraseSpan(624)}</td>
        <td><input type="text" name="cho_company_hvhh" id="cho_company_hvhh" value="{$ns.req_params.cho_company_hvhh}"/></td>
        <td><div style="color:red;display: none;" class="f_error" id="cho_company_hvhh_error">{$ns.langManager->getPhraseSpan(636)}</div></td>
    </tr>
    <tr>
        <td>{$ns.langManager->getPhraseSpan(625)}</td>
        <td><input type="text" name="cho_company_address" id="cho_company_address" value="{$ns.req_params.cho_company_address}"/></td>
        <td><div style="color:red;display: none;" class="f_error" id="cho_company_address_error">{$ns.langManager->getPhraseSpan(637)}</div></td>
    </tr>
    <tr>
        <td>{$ns.langManager->getPhraseSpan(626)}</td>
        <td><input type="text" name="cho_company_bank"  id="cho_company_bank" value="{$ns.req_params.cho_company_bank}"/></td>
        <td><div style="color:red;display: none;" class="f_error" id="cho_company_bank_error">{$ns.langManager->getPhraseSpan(638)}</div></td>
    </tr>
    <tr>
        <td>{$ns.langManager->getPhraseSpan(627)}</td>
        <td><input type="text" name="cho_company_bank_account_number" id="cho_company_bank_account_number" value="{$ns.req_params.cho_company_bank_account_number}"/></td>
        <td><div style="color:red;display: none;" class="f_error" id="cho_company_bank_account_number_error">{$ns.langManager->getPhraseSpan(639)}</div></td>
    </tr>
    <tr>
        <td>{$ns.langManager->getPhraseSpan(628)}</td>
        <td><input type="text" name="cho_company_delivering_address" id="cho_company_delivering_address" value="{$ns.req_params.cho_company_delivering_address}"/></td>
        <td><div style="color:red;display: none;" class="f_error" id="cho_company_delivering_address_error">{$ns.langManager->getPhraseSpan(640)}</div></td>
    </tr>
</table>
