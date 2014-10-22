{if $orderInfo->getPaymentType() == 'creditcard'}
    </br>
    {$ns.langManager->getPhraseSpan(367)}: {$ns.langManager->getPhraseSpan(618)}
{/if}
{if $orderInfo->getPaymentType() == 'paypal'}
    </br>
    {$ns.langManager->getPhraseSpan(367)}: Paypal
{/if}
{if $orderInfo->getPaymentType() == 'bank'}
    </br>
    {$ns.langManager->getPhraseSpan(367)}: {$ns.langManager->getPhraseSpan(440)}
{/if}
{if $orderInfo->getPaymentType() == 'arca'}
    </br>
    {$ns.langManager->getPhraseSpan(367)}: Arca
{/if}
{if $orderInfo->getPaymentType() == 'credit'}
    </br>
    {$ns.langManager->getPhraseSpan(367)}: {$ns.langManager->getPhraseSpan(364)}
{/if}
{if $orderInfo->getPaymentType() == 'cash'}
    </br>
    {$ns.langManager->getPhraseSpan(367)}: {$ns.langManager->getPhraseSpan(363)}
{/if}