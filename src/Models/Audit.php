<?php

namespace SilverStripe\DataObjectAuditor\Models;

use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;

/**
 * Class Audit
 * Tracks all mutation operations on dataobjects (i.e. deletes or updates)
 */
class Audit extends DataObject
{
    const DB_MODEL_NAME = 'ModelName';
    const DB_MODEL_ID = 'ModelID';
    const DB_MUTATION_PEFORMED = 'MutationPerformed';
    const HAS_MANY_AUDIT_VALUES = 'AuditValues';
    const HAS_ONE_DONE_BY = 'DoneBy';
    const DB_DONE_BY_ID = 'DoneByID';

    private static $db = [
        self::DB_MODEL_NAME => 'Varchar(150)',
        self::DB_MODEL_ID => 'Int',
        self::DB_MUTATION_PEFORMED => 'Varchar(50)'
    ];

    private static $has_one = [
        self::HAS_ONE_DONE_BY => Member::class
    ];

    private static $indexes = [
        'inxModelAndID' => [
            'type' => 'index',
            'columns' => [self::DB_MODEL_NAME, self::DB_MODEL_ID]
        ],
        self::DB_MUTATION_PEFORMED => true
    ];

    private static $has_many = [
        self::HAS_MANY_AUDIT_VALUES => AuditValue::class
    ];

    private static $table_name = 'DataObjectAuditor_Audit';

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if (!$this->isInDB()) {
            $memberID = ($member = Security::getCurrentUser()) ? $member->ID : 0;
            $this->setField(self::DB_DONE_BY_ID, $memberID);
        }
    }

    /**
     * Returns the previous value for a specific field.
     * @param string $field The field name to query
     * @return null|DataObject
     */
    public function getPreviousFieldValue($field)
    {
        if ($item = AuditValue::get()
            ->filter(
                [
                    AuditValue::DB_FIELD => $field,
                    AuditValue::HAS_ONE_AUDIT_ID => $this->getField('ID')
                ]
            )
            ->last()) {
            return $item->getField(AuditValue::DB_PREVIOUS_VALUE);
        }

        return null;
    }
}
