<?php

namespace SilverStripe\DataObjectAuditor\Extensions;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\DataObjectAuditor\Models\Audit;
use SilverStripe\DataObjectAuditor\Models\AuditValue;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataObject;

/**
 * Class AuditExtension
 */
class AuditExtension extends DataExtension
{
    use Configurable;

    private static $audit_exclusions = [
        Audit::class,
        AuditValue::class
    ];

    /**
     * Writes to the audit table after the item was deleted
     */
    public function onAfterDelete()
    {
        parent::onAfterDelete();
        $this->createAudit($this->owner->toMap());
    }

    public function onAfterWrite()
    {
        parent::onAfterWrite();
        $baseChangedFields = $this->owner->getChangedFields(true, DataObject::CHANGE_VALUE);
        // A good guestimate is that if baseChangedFields contains an ID column, it's safe to assume
        // that it is an INSERT operation and thus shouldn't be audited.
        if (in_array('ID', array_keys($baseChangedFields))) {
            return;
        }

        $changedFields = [];
        foreach ($baseChangedFields as $key => $val) {
            $changedFields[$key] = $val['before'];
        }
        $this->createAudit($changedFields, 'update');
    }

    /**
     * Writes the requisite Audit and AuditValue entries, provided they're not excluded of course.
     * @param array $record
     * @param string $mutation
     * @throws \SilverStripe\ORM\ValidationException
     */
    protected function createAudit($record, $mutation = 'delete')
    {
        $needle = get_class($this->owner);
        $hayStack = self::config()->get('audit_exclusions');
        // Early exit on exclusions
        if (in_array($needle, $hayStack)) {
            return;
        }

        $auditId = Audit::create()->update([
            Audit::DB_MODEL_NAME => $this->owner->getField('ClassName'),
            Audit::DB_MODEL_ID => $this->owner->getField('ID'),
            Audit::DB_MUTATION_PEFORMED => $mutation
        ])->write();

        foreach ($record as $key => $value) {
            if (!$value || is_object($value)) {
                continue;
            }
            AuditValue::create()
                ->setField(AuditValue::DB_FIELD, $key)
                ->setField(AuditValue::DB_PREVIOUS_VALUE, $value)
                ->setField(AuditValue::HAS_ONE_AUDIT_ID, $auditId)
                ->write();
        }
    }
}
