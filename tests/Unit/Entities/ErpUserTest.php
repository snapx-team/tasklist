<?php

namespace Tests\Unit;

use Tests\TestCase;
use Xguard\Tasklist\Entities\ErpUser;

class ErpUserTest extends TestCase
{
    const FIRST_NAME = 'John';
    const LAST_NAME = 'Doe';
    const FIRST_NAME_2 = 'Jane';
    const LAST_NAME_2 = 'Smith';
    const ID = 'id';
    const FULL_NAME = 'full_name';

    public function testErpUserConstructor()
    {
        // Arrange
        $id = 1;
        $firstName = self::FIRST_NAME;
        $lastName = self::LAST_NAME;

        // Act
        $erpUser = new ErpUser($id, $firstName, $lastName);

        // Assert
        $this->assertInstanceOf(ErpUser::class, $erpUser);
        $this->assertSame($id, $erpUser->getId());
        $this->assertSame($firstName, $erpUser->getFirstName());
        $this->assertSame($lastName, $erpUser->getLastName());
    }

    public function testErpUserSettersAndGetters()
    {
        // Arrange
        $erpUser = new ErpUser(1, self::FIRST_NAME, self::LAST_NAME);

        // Act
        $erpUser->setId(2);
        $erpUser->setFirstName(self::FIRST_NAME_2);

        // Assert
        $this->assertSame(2, $erpUser->getId());
        $this->assertSame(self::FIRST_NAME_2, $erpUser->getFirstName());
    }

    public function testErpUserJsonSerialize()
    {
        // Arrange
        $id = 1;
        $firstName = self::FIRST_NAME;
        $lastName = self::LAST_NAME;
        $erpUser = new ErpUser($id, $firstName, $lastName);

        // Act
        $serializedData = $erpUser->jsonSerialize();

        // Assert
        $this->assertIsArray($serializedData);
        $this->assertArrayHasKey(self::ID, $serializedData);
        $this->assertArrayHasKey(self::FULL_NAME, $serializedData);
        $this->assertSame($id, $serializedData[self::ID]);
        $this->assertSame($firstName . ' ' . $lastName, $serializedData[self::FULL_NAME]);
    }
}
