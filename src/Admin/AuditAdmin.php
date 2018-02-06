<?php
namespace SilverStripe\DataObjectAuditor\Admin;

use SilverStripe\Admin\ModelAdmin;
use SilverStripe\DataObjectAuditor\Models\Audit;

class AuditAdmin extends ModelAdmin
{
    private static $url_segment = 'audit-admin';

    private static $managed_models = [
        Audit::class,
    ];

    private static $menu_title = 'Audit Admin';

    public function getList()
    {
        $list = parent::getList();

        $list = $list->sort('Created DESC');
        return $list;
    }
}
