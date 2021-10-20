<?php

namespace Proton\IosReceiptParser\Attribute;

use Proton\IosReceiptParser\Exception\AttributeMissingException;

/**
 * Set of attributes without assigned semantics.
 *
 * @internal
 * @psalm-type AttributeSequence = array{type: \phpseclib\Math\BigInteger, value: string}
 */
final class AttributeSet
{
    /**
     * @var array
     * @psalm-var list<AttributeSequence>
     */
    private $data;

    /** @var string|null */
    private $context;

    /**
     * @psalm-param list<AttributeSequence> $data
     * @param string $context Used to provide context for better error messages.
     */
    public function __construct(array $data, string $context = null)
    {
        $this->data = $data;
        $this->context = $context;
    }

    /**
     * @throws AttributeMissingException
     */
    public function getRequired(int $type): string
    {
        if (($value = $this->get($type)) !== null) {
            return $value;
        }

        throw new AttributeMissingException($type, $this->context);
    }

    /**
     * @psalm-return list<string>
     */
    public function getMulti(int $type): array
    {
        $return = [];
        foreach ($this->data as $attribute) {
            if ((int) (string) $attribute['type'] === $type) {
                $return[] = $attribute['value'];
            }
        }

        return $return;
    }

    public function get(int $type): ?string
    {
        foreach ($this->data as $attribute) {
            if ((int) (string) $attribute['type'] === $type) {
                return $attribute['value'];
            }
        }

        return null;
    }
}
