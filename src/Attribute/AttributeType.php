<?php

namespace Proton\IosReceiptParser\Attribute;

/**
 * @internal
 */
final class AttributeType
{
    // Receipt
    public const RECEIPT_APP_ITEM_ID = 0;
    public const RECEIPT_BUNDLE_ID = 2;
    public const RECEIPT_APP_VERSION = 3;
    public const RECEIPT_OPAQUE = 4;
    public const RECEIPT_SHA1 = 5;
    public const RECEIPT_CREATION_DATE = 12;
    public const RECEIPT_CREATION_DATE_MS = 12 * 1000;
    public const RECEIPT_IN_APP = 17;
    public const RECEIPT_ORIGINAL_APP_VERSION = 19;
    public const RECEIPT_EXPIRATION_DATE = 21;
    public const RECEIPT_EXPIRATION_DATE_MS = 21 * 1000;

    // InApp
    public const IN_APP_QUANTITY = 1701;
    public const IN_APP_PRODUCT_IDENTIFIER = 1702;
    public const IN_APP_TRANSACTION_IDENTIFIER = 1703;
    public const IN_APP_PURCHASE_DATE = 1704;
    public const IN_APP_PURCHASE_DATE_MS = 1704 * 1000;
    public const IN_APP_ORIGINAL_TRANSACTION_IDENTIFIER = 1705;
    public const IN_APP_ORIGINAL_PURCHASE_DATE = 1706;
    public const IN_APP_ORIGINAL_PURCHASE_DATE_MS = 1706 * 1000;
    public const IN_APP_SUBSCRIPTION_EXPIRATION_DATE = 1708;
    public const IN_APP_SUBSCRIPTION_EXPIRATION_DATE_MS = 1708 * 1000;
    public const IN_APP_WEB_ORDER_LINE_ITEM_ID = 1711;
    public const IN_APP_CANCELLATION_DATE = 1712;
    public const IN_APP_CANCELLATION_DATE_MS = 1712 * 1000;
    public const IN_APP_SUBSCRIPTION_INTRODUCTORY_PRICE_PERIOD = 1719;

    private const JSON_FIELD_NAMES = [
        self::RECEIPT_APP_ITEM_ID => 'app_item_id',
        self::RECEIPT_BUNDLE_ID => 'bundle_id',
        self::RECEIPT_APP_VERSION => 'application_version',
        self::RECEIPT_CREATION_DATE => 'receipt_creation_date',
        self::RECEIPT_CREATION_DATE_MS => 'receipt_creation_date_ms',
        self::RECEIPT_IN_APP => 'in_app',
        self::RECEIPT_ORIGINAL_APP_VERSION => 'original_application_version',
        self::RECEIPT_EXPIRATION_DATE => 'receipt_expiration_date',
        self::RECEIPT_EXPIRATION_DATE_MS => 'receipt_expiration_date_ms',

        self::IN_APP_QUANTITY => 'quantity',
        self::IN_APP_PRODUCT_IDENTIFIER => 'product_id',
        self::IN_APP_TRANSACTION_IDENTIFIER => 'transaction_id',
        self::IN_APP_PURCHASE_DATE => 'purchase_date',
        self::IN_APP_PURCHASE_DATE_MS => 'purchase_date_ms',
        self::IN_APP_ORIGINAL_TRANSACTION_IDENTIFIER => 'original_transaction_id',
        self::IN_APP_ORIGINAL_PURCHASE_DATE => 'original_purchase_date',
        self::IN_APP_ORIGINAL_PURCHASE_DATE_MS => 'original_purchase_date_ms',
        self::IN_APP_SUBSCRIPTION_EXPIRATION_DATE => 'expires_date',
        self::IN_APP_SUBSCRIPTION_EXPIRATION_DATE_MS => 'expires_date_ms',
        self::IN_APP_WEB_ORDER_LINE_ITEM_ID => 'web_order_line_item_id',
        self::IN_APP_CANCELLATION_DATE => 'cancellation_date',
        self::IN_APP_CANCELLATION_DATE_MS => 'cancellation_date_ms',
        self::IN_APP_SUBSCRIPTION_INTRODUCTORY_PRICE_PERIOD => 'is_in_intro_offer_period',
    ];

    private const HUMAN_FIELD_DESCRIPTIONS = [
        self::RECEIPT_APP_ITEM_ID => 'A string that the App Store uses to uniquely identify the application that created the transaction.',
        self::RECEIPT_BUNDLE_ID => 'The app\'s bundle identifier',
        self::RECEIPT_APP_VERSION => 'The app\'s version number',
        self::RECEIPT_OPAQUE => 'An opaque value used, with other data, to compute the SHA-1 hash during validation',
        self::RECEIPT_SHA1 => 'A SHA-1 hash, used to validate the receipt',
        self::RECEIPT_CREATION_DATE => 'The date when the app receipt was created',
        self::RECEIPT_IN_APP => 'The receipt for an in-app purchase',
        self::RECEIPT_ORIGINAL_APP_VERSION => 'The version of the app that was originally purchased',
        self::RECEIPT_EXPIRATION_DATE => 'The date that the app receipt expires',

        self::IN_APP_QUANTITY => 'The number of items purchased',
        self::IN_APP_PRODUCT_IDENTIFIER => 'The product identifier of the item that was purchased',
        self::IN_APP_TRANSACTION_IDENTIFIER => 'The transaction identifier of the item that was purchased',
        self::IN_APP_PURCHASE_DATE => 'The date and time that the item was purchased',
        self::IN_APP_ORIGINAL_TRANSACTION_IDENTIFIER => 'For a transaction that restores a previous transaction, the transaction identifier of the original transaction. Otherwise, identical to the transaction identifier',
        self::IN_APP_ORIGINAL_PURCHASE_DATE => 'For a transaction that restores a previous transaction, the date of the original transaction',
        self::IN_APP_SUBSCRIPTION_EXPIRATION_DATE => 'The expiration date for the subscription, expressed as the number of milliseconds since January 1, 1970, 00:00:00 GMT',
        self::IN_APP_WEB_ORDER_LINE_ITEM_ID => 'The primary key for identifying subscription purchases',
        self::IN_APP_CANCELLATION_DATE => 'For a transaction that was canceled by Apple customer support, the time and date of the cancellation. For an auto-renewable subscription plan that was upgraded, the time and date of the upgrade transaction',
        self::IN_APP_SUBSCRIPTION_INTRODUCTORY_PRICE_PERIOD => 'For an auto-renewable subscription, whether or not it is in the introductory price period',
    ];

    public static function getJsonFieldName(int $type): ?string
    {
        return self::JSON_FIELD_NAMES[$type] ?? null;
    }

    public static function getHumanFieldDescription(int $type): string
    {
        if (($description = self::HUMAN_FIELD_DESCRIPTIONS[$type] ?? null) !== null) {
            return $description;
        }

        throw new \Exception("Unknown attribute type: {$type}");
    }
}
