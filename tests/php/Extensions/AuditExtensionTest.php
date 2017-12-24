<?php

namespace SilverStripe\DataObjectAuditor\Tests\Extensions;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\DataObjectAuditor\Models\Audit;
use SilverStripe\DataObjectAuditor\Models\AuditValue;
use SilverStripe\DataObjectAuditor\Tests\Stubs\MockObject;

/**
 * Class AuditExtensionTest
 */
class AuditExtensionTest extends SapphireTest
{
    protected static $fixture_file = '../fixtures.yml';

    protected static $extra_dataobjects = [
        MockObject::class
    ];

    public function testNoAuditOnRecordInsert()
    {
        $mock = MockObject::create()
            ->setField('Field1', 'Field1Value')
            ->setField('Field2', 'Field2Value');
        $mockID = $mock->write();

        $this->assertNull(Audit::get()->filter([
            Audit::DB_MODEL_ID => $mockID,
            Audit::DB_MODEL_NAME => MockObject::class
        ])->first());
    }

    public function testAuditOnRecordUpdate()
    {
        $mock = $this->objFromFixture(MockObject::class, 'test1');
        $prevVal = $mock->getField('Field1');
        $newVal = 'moo';
        $auditRecord = Audit::get()->filter([
            Audit::DB_MODEL_ID => $mock->ID,
            Audit::DB_MODEL_NAME => MockObject::class
        ]);
        $this->assertNull($auditRecord->last());
        $fetchedMock = MockObject::get()->byID($mock->ID);
        $fetchedMock->setField('Field1', $newVal)->write();
        $this->assertNotNull($auditRecord->last());
        $auditValue = $auditRecord->last()->getPreviousFieldValue('Field1');
        $this->assertEquals($prevVal, $auditValue);
        $this->assertEquals($auditRecord->last()->getField(Audit::DB_MUTATION_PEFORMED), 'update');
    }

    public function testAuditOnRecordDelete()
    {
        $mock = $this->objFromFixture(MockObject::class, 'test2');
        $field1Val = $mock->getField('Field1');
        $field2Val = $mock->getField('Field2');
        $mockID = $mock->ID;
        $mock->delete();
        $auditRecord = Audit::get()->filter([
            Audit::DB_MODEL_ID => $mockID,
            Audit::DB_MODEL_NAME => MockObject::class
        ])->last();
        $auditF1Value = $auditRecord->getPreviousFieldValue('Field1');
        $auditF2Value = $auditRecord->getPreviousFieldValue('Field2');
        $this->assertEquals($field1Val, $auditF1Value);
        $this->assertEquals($field2Val, $auditF2Value);
        $this->assertEquals($auditRecord->getField(Audit::DB_MUTATION_PEFORMED), 'delete');
    }
}
