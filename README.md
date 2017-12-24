## SilverStripe DataObject auditor

A SilverStripe module which audits updates to and deletions from DataObject subclasses.

## Installation

```composer require silverstripe/dataobject-auditor```

## Setup
No setup is required as the `AuditExtension` is automatically added to DataObject.

Changes are added to two tables viz. `DataObjectAuditor_Audit` which has a `$has_many` to `DataObjectAuditor_AuditValues`.
To prevent duplication only changed records are added to the `DataObjectAuditor_AuditValues` table to as to prevent duplication.
In the case of deletion though, the entire records is written.

## Configuration/Usage instructions
By default all DataObjects, except `Audit` and `AuditValue` are audited. 

You can exclude some items by adding them to the `AuditExtension::audit_exclusions` config setting:

```yaml
SilverStripe\DataObjectAuditor\Extensions\AuditExtension:
  audit_exclusions:
    - Namespaced\ClassName1
    - Namespaced\ClassName2
``` 

## Bugtracker ##

Bugs are tracked on [github.com](https://github.com/fspringveldt/silverstripe-dataobject-auditor/issues).