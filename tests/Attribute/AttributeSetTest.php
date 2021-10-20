<?php

namespace Attribute;

use Proton\IosReceiptParser\Attribute\AttributeSet;
use Proton\IosReceiptParser\Exception\AttributeMissingException;
use PHPUnit\Framework\TestCase;

class AttributeSetTest extends TestCase
{
    /**
     * @dataProvider attributesProvider
     */
    public function testGetRequired(AttributeSet $attributes): void
    {
        $this->assertSame('abcd', $attributes->getRequired(1));
        $this->assertSame('wxyz', $attributes->getRequired(666));

        $this->expectException(AttributeMissingException::class);
        $attributes->getRequired(2);
    }

    /**
     * @dataProvider attributesProvider
     */
    public function testGetMulti(AttributeSet $attributes): void
    {
        $this->assertSame(['abcd', '1234'], $attributes->getMulti(1));
        $this->assertSame(['wxyz'], $attributes->getMulti(666));
        $this->assertIsArray($attributes->getMulti(2));
        $this->assertEmpty($attributes->getMulti(2));
    }

    /**
     * @dataProvider attributesProvider
     */
    public function testGet(AttributeSet $attributes): void
    {
        $this->assertSame('abcd', $attributes->get(1));
        $this->assertSame('wxyz', $attributes->get(666));
        $this->assertNull($attributes->get(2));
    }

    public function attributesProvider(): array
    {
        return [
            [new AttributeSet([
                ['type' => 1, 'value' => 'abcd'],
                ['type' => 666, 'value' => 'wxyz'],
                ['type' => 1, 'value' => '1234'],
            ])]
        ];
    }
}
