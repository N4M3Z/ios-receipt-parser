<?php

namespace Cthulhu\IosReceiptParser;

use Cthulhu\IosReceiptParser\ASN1\SimpleDecoder;
use Cthulhu\IosReceiptParser\Attribute\AttributeSet;
use Cthulhu\IosReceiptParser\Attribute\AttributeType;
use phpseclib3\File\ASN1;

/**
 * @psalm-import-type AttributeSequence from AttributeSet
 */
final class InApp implements \JsonSerializable
{
    /** @var AttributeSet */
    private $attributes;

    /** @var SimpleDecoder */
    private $decoder;

    /**
     * @psalm-param list<AttributeSet> $data
     */
    public function __construct(array $data, SimpleDecoder $decoder)
    {
        $this->attributes = new AttributeSet($data, 'in_app');
        $this->decoder = $decoder;
    }

    public function getQuantity(): string
    {
        return (string) $this->decoder->decodeBase64(
            $this->attributes->getRequired(AttributeType::IN_APP_QUANTITY),
            ASN1::TYPE_INTEGER
        );
    }

    public function getProductIdentifier(): string
    {
        return $this->decoder->decodeBase64(
            $this->attributes->getRequired(AttributeType::IN_APP_PRODUCT_IDENTIFIER),
            ASN1::TYPE_UTF8_STRING,
        );
    }

    public function getTransactionIdentifier(): string
    {
        return $this->decoder->decodeBase64(
            $this->attributes->getRequired(AttributeType::IN_APP_TRANSACTION_IDENTIFIER),
            ASN1::TYPE_UTF8_STRING,
        );
    }

    public function getPurchaseDate(): string
    {
        return $this->decoder->decodeBase64(
            $this->attributes->getRequired(AttributeType::IN_APP_PURCHASE_DATE),
            ASN1::TYPE_IA5_STRING,
        );
    }

    public function getOriginalTransactionIdentifier(): string
    {
        $raw = $this->attributes->get(AttributeType::IN_APP_ORIGINAL_TRANSACTION_IDENTIFIER);

        return $raw === null
            ? $this->getTransactionIdentifier()
            : $this->decoder->decodeBase64($raw, ASN1::TYPE_UTF8_STRING);
    }

    public function getOriginalPurchaseDate(): string
    {
        $raw = $this->attributes->get(AttributeType::IN_APP_ORIGINAL_PURCHASE_DATE);

        return $raw === null
            ? $this->getPurchaseDate()
            : $this->decoder->decodeBase64($raw, ASN1::TYPE_IA5_STRING);
    }

    public function getSubscriptionExpirationDate(): ?string
    {
        $raw = $this->attributes->get(AttributeType::IN_APP_SUBSCRIPTION_EXPIRATION_DATE);

        return $raw === null ? null : $this->decoder->decodeBase64($raw, ASN1::TYPE_IA5_STRING);
    }

    public function getWebOrderLineItemID(): ?string
    {
        $raw = $this->attributes->get(AttributeType::IN_APP_WEB_ORDER_LINE_ITEM_ID);

        return $raw === null ? null : (string) $this->decoder->decodeBase64($raw, ASN1::TYPE_INTEGER);
    }

    public function getCancellationDate(): ?string
    {
        $raw = $this->attributes->get(AttributeType::IN_APP_CANCELLATION_DATE);

        return $raw === null ? null : $this->decoder->decodeBase64($raw, ASN1::TYPE_IA5_STRING);
    }

    public function getSubscriptionIntroductoryPricePeriod(): ?string
    {
        $raw = $this->attributes->get(AttributeType::IN_APP_SUBSCRIPTION_INTRODUCTORY_PRICE_PERIOD);

        return $raw === null ? null : $this->decoder->decodeBase64($raw, ASN1::TYPE_IA5_STRING);
    }

    public function jsonSerialize(): array
    {
        $return = [];

        foreach ([
            AttributeType::IN_APP_QUANTITY => $this->getQuantity(),
            AttributeType::IN_APP_PRODUCT_IDENTIFIER => $this->getProductIdentifier(),
            AttributeType::IN_APP_TRANSACTION_IDENTIFIER => $this->getTransactionIdentifier(),
            AttributeType::IN_APP_PURCHASE_DATE => $this->getPurchaseDate(),
            AttributeType::IN_APP_ORIGINAL_TRANSACTION_IDENTIFIER => $this->getOriginalTransactionIdentifier(),
            AttributeType::IN_APP_ORIGINAL_PURCHASE_DATE => $this->getOriginalPurchaseDate(),
            AttributeType::IN_APP_SUBSCRIPTION_EXPIRATION_DATE => $this->getSubscriptionExpirationDate(),
            AttributeType::IN_APP_WEB_ORDER_LINE_ITEM_ID => $this->getWebOrderLineItemID(),
            AttributeType::IN_APP_CANCELLATION_DATE => $this->getCancellationDate(),
            AttributeType::IN_APP_SUBSCRIPTION_INTRODUCTORY_PRICE_PERIOD => $this
                ->getSubscriptionIntroductoryPricePeriod(),
        ] as $type => $value) {
            if ($value === null) {
                continue;
            }

            $return[AttributeType::getJsonFieldName($type)] = $value;
        }

        return $return;
    }
}
