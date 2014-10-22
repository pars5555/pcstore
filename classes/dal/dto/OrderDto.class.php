<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class OrderDto extends AbstractDto {

    // Map of DB value to Field value
    protected $mapArray = array("id" => "id", "customer_email" => "customerEmail", "billing_phone" => "billingPhone", "billing_cell" => "billingCell",
        "shipping_phone" => "shippingPhone", "shipping_cell" => "shippingCell", "billing_recipient_name" => "billingRecipientName",
        "shipping_recipient_name" => "shippingRecipientName", "payment_type" => "paymentType", "customer_type" => "customerType",
        "dollar_exchange_usd_amd" => "dollarExchangeUsdAmd", "do_shipping" => "doShipping", "shipping_address" => "shippingAddress",
        "billing_address" => "billingAddress", "shipping_region" => "shippingRegion", "billing_region" => "billingRegion", "used_points" => "usedPoints",
        "order_date_time" => "orderDateTime", "confirmed_by_pcstore" => "confirmedByPcstore", "delivered_date_time" => "deliveredDateTime",
        "billing_is_same_as_shipping" => "billingIsSameAsShipping", "shipping_amd" => "shippingAmd", "order_total_amd" => "orderTotalAmd", "order_total_usd" => "orderTotalUsd",
        "status" => "status", "cancel_reason_text" => "cancelReasonText", "used_deals_ids" => "usedDealsIds",
        "total_promo_discount_amd" => "totalPromoDiscountAmd", "order_dealer_price_usd" => "orderDealerPriceUsd", "included_vat" => "includedVat",
        "3rd_party_payment_token" => "3rdPartyPaymentToken",
        "3rd_party_payment_received" => "3rdPartyPaymentReceived",
        "metadata_json" => "metadataJson",
        //for the order_details table join
        "order_details_bundle_id" => "orderDetailsBundleId", "order_details_item_id" => "orderDetailsItemId",
        "order_details_special_fee_id" => "orderDetailsSpecialFeeId", "order_details_item_display_name" => "orderDetailsItemDisplayName",
        "order_details_bundle_display_name_id" => "orderDetailsBundleDisplayNameId",
        "order_details_special_fee_display_name_id" => "orderDetailsSpecialFeeDisplayNameId",
        "order_details_bundle_count" => "orderDetailsBundleCount", "order_details_item_count" => "orderDetailsItemCount",
        "order_details_customer_item_price" => "orderDetailsCustomerItemPrice", "order_details_item_dealer_price" => "orderDetailsItemDealerPrice",
        "order_details_special_fee_price" => "orderDetailsSpecialFeePrice", "order_details_item_company_id" => "orderDetailsItemCompanyId",
        "order_details_is_dealer_of_item" => "orderDetailsIsDealerOfItem", "order_details_discount" => "orderDetailsDiscount",
        "order_details_customer_bundle_price_amd" => "orderDetailsCustomerBundlePriceAmd",
        "order_details_customer_bundle_price_usd" => "orderDetailsCustomerBundlePriceUsd",
        //for the credit_orders table join
        "credit_orders_deposit" => "creditOrdersDeposit",
        "credit_orders_credit_supplier_id" => "creditOrdersCreditSupplierId",
        "credit_orders_credit_months" => "creditOrdersCreditMonths",
        "credit_orders_annual_interest_percent" => "creditOrdersAnnualInterestPercent",
        "credit_orders_monthly_payment" => "creditOrdersMonthlyPayment"
    );

    // constructs class instance
    public function __construct() {
        
    }

    // returns map array
    public function getMapArray() {
        return $this->mapArray;
    }

}

?>
