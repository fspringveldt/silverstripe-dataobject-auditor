<?php

namespace SilverStripe\DataObjectAuditor\Models;

use SilverStripe\ORM\DataObject;

/**
 * Class AuditValue
 */
class AuditValue extends DataObject
{
    const DB_FIELD = 'Field';
    const DB_PREVIOUS_VALUE = 'PreviousValue';
    const HAS_ONE_AUDIT_ID = 'AuditID';
    const HAS_ONE_AUDIT = 'Audit';

    private static $table_name = 'DataObjectAuditor_AuditValue';

    private static $db = [
        self::DB_FIELD => 'Varchar(50)',
        self::DB_PREVIOUS_VALUE => 'Text',
    ];

    private static $has_one = [
        self::HAS_ONE_AUDIT => Audit::class
    ];

    private static $indexes = [
        'inxAuditAndField' => [
            'type' => 'index',
            'columns' => [self::DB_FIELD, self::HAS_ONE_AUDIT_ID]
        ]
    ];

    private static $summary_fields = [
        'ID',
        self::DB_FIELD,
        self::DB_PREVIOUS_VALUE
    ];
}
