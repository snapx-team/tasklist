<?php

namespace Tests\Unit;

use Tests\TestCase;
use Xguard\Tasklist\Entities\ErpContract;

class ErpContractTest extends TestCase
{
    const CONTRACT_IDENTIFIER_1 = 'ABC-123';
    const CONTRACT_IDENTIFIER_2 = 'XYZ-789';
    const ID = 'id';
    const CONTRACT_IDENTIFIER_TEXT = 'contract_identifier';

    public function testErpContractConstructor()
    {
        // Arrange
        $id = 1;
        $contractIdentifier = self::CONTRACT_IDENTIFIER_1;

        // Act
        $erpContract = new ErpContract($id, $contractIdentifier);

        // Assert
        $this->assertInstanceOf(ErpContract::class, $erpContract);
        $this->assertSame($id, $erpContract->getId());
        $this->assertSame($contractIdentifier, $erpContract->getContractIdentifier());
    }

    public function testErpContractSettersAndGetters()
    {
        // Arrange
        $erpContract = new ErpContract(1, self::CONTRACT_IDENTIFIER_1);

        // Act
        $erpContract->setId(2);
        $erpContract->setContractIdentifier(self::CONTRACT_IDENTIFIER_2);

        // Assert
        $this->assertSame(2, $erpContract->getId());
        $this->assertSame(self::CONTRACT_IDENTIFIER_2, $erpContract->getContractIdentifier());
    }

    public function testErpContractJsonSerialize()
    {
        // Arrange
        $id = 1;
        $contractIdentifier = self::CONTRACT_IDENTIFIER_1;
        $erpContract = new ErpContract($id, $contractIdentifier);

        // Act
        $serializedData = $erpContract->jsonSerialize();

        // Assert
        $this->assertIsArray($serializedData);
        $this->assertArrayHasKey(self::ID, $serializedData);
        $this->assertArrayHasKey(self::CONTRACT_IDENTIFIER_TEXT, $serializedData);
        $this->assertSame($id, $serializedData[self::ID]);
        $this->assertSame($contractIdentifier, $serializedData[self::CONTRACT_IDENTIFIER_TEXT]);
    }
}
