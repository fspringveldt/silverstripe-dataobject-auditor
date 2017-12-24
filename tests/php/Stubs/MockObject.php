<?php

namespace SilverStripe\DataObjectAuditor\Tests\Stubs;

use SilverStripe\Dev\TestOnly;
use SilverStripe\DataObjectAuditor\Extensions\AuditExtension;
use SilverStripe\ORM\DataObject;

/**
 * Class MockObject
 */
class MockObject extends DataObject implements TestOnly
{
    private static $db = [
        'Field1' => 'Varchar(20)',
        'Field2' => 'Varchar(20)'
    ];
}
