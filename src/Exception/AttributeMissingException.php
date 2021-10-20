<?php

namespace Proton\IosReceiptParser\Exception;

use Proton\IosReceiptParser\Attribute\AttributeType;
use Throwable;

final class AttributeMissingException extends \Exception
{
    /** @var int */
    private $type;

    /**
     * @throws \Exception
     */
    public function __construct(int $type, string $context = null, $code = 0, Throwable $previous = null)
    {
        $typeField = AttributeType::getJsonFieldName($type) ?? $type;
        $typeDescription = AttributeType::getHumanFieldDescription($type);

        $message = $context === null
            ? sprintf('Required attribute %s (%s) is missing', $typeField, $typeDescription)
            : sprintf('Required attribute %s (%s) is missing from %s', $typeField, $typeDescription, $context);

        parent::__construct($message, $code, $previous);
    }

    public function getType(): int
    {
        return $this->type;
    }
}
