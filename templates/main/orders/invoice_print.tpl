<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <body class="print" style="width: 100%; height:100%">
        <div class="content">
            {if isset($ns.orderDto)}
                {if $ns.orderDto->getIncludedVat()==1}
                    {assign var='pcstore_company_bank_account_number' value=$ns.langManager->getCmsVar('pcstore_vat_company_bank_account_number')}
                    {assign var='pcstore_company_hvhh' value=$ns.langManager->getCmsVar('pcstore_vat_company_hvhh')}
                    {assign var='pcstore_company_registered_address' value=$ns.langManager->getPhrase(642)}
                    {assign var='pcstore_bank_name' value=$ns.langManager->getPhrase(633)}
                    {assign var='pcstore_company_name' value=$ns.langManager->getPhrase(634)}

                {else}
                    {assign var='pcstore_company_bank_account_number' value = $ns.langManager->getCmsVar('pcstore_non_vat_company_bank_account_number')}
                    {assign var='pcstore_company_hvhh' value=$ns.langManager->getCmsVar('pcstore_non_vat_company_hvhh')}
                    {assign var='pcstore_company_registered_address' value=$ns.langManager->getPhrase(643)}
                    {assign var='pcstore_bank_name' value=$ns.langManager->getPhrase(631)}
                    {assign var='pcstore_company_name' value=$ns.langManager->getPhrase(632)}
                {/if}
                <table>
                    <tr>
                        <td>
                            Մատակարար {$pcstore_company_name}</br>
                            Իրավ. հասցե {$pcstore_company_registered_address}</br>
                            Գործ․ հասցե {$ns.langManager->getPhrase(643)}</br>
                            Հ/Հ {$pcstore_company_bank_account_number}</br>
                            Բանկ {$pcstore_bank_name}</br>
                            ՀՎՀՀ {$pcstore_company_hvhh}</br>
                        </td>
                        <td>
                            Փոխանցման հաշիվ</br>
                            "1" սեպտեմբեր  2014 թ.</br>
                        </td>
                    </tr>
                    <tr><td></td><td></td></tr>
                    <tr>
                        <td>
                            Վճարող {$ns.metadata->company_name}</br>
                            Իրավ. հասցե {$ns.metadata->company_address}</br>
                            Գործ․ հասցե  {$ns.metadata->company_delivering_address}</br>
                            Հ/Հ  {$ns.metadata->bank_account_number}</br>
                            Բանկ {$ns.metadata->company_bank}</br>
                            ՀՎՀՀ {$ns.metadata->company_hvhh}</br>

                        </td>
                        <td>
                            Պայմանագիր/պատվեր</br>
                            N</br>
                        </td>
                    </tr>
                    <tr><td></td><td></td></tr>
                    <tr>
                        <td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td>
                    </tr>
                    <tr>
                        <td>total</td>
                        <td>total</td>  
                    </tr>
                    <tr>
                        <td>sign</td>
                        <td>sign</td> 
                    </tr>

                </table>
            {else}
                Order #{$ns.order_id} doesn't exists!
            {/if}
        </div>
    </body>
</html>

