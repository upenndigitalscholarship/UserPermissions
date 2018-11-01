<?php

class PermissionsAccessAclAssertion implements Zend_Acl_Assert_Interface {
    public function assert(
        Zend_Acl $acl,
        Zend_Acl_Role_Interface $role = null,
        Zend_Acl_Resource_Interface $resource = null,
        $privilege = null)
    {

        if ($privilege == 'showNotPublic') {
//            return true;
            if (($role instanceof User) && $resource->getResourceId() == 'Items') {
              _log("got inside acl if check: ".var_dump($resource), $priority = Zend_Log::ERR);
                if ($privilege == 'view') {
                    $db = get_db();
                    $accessTable = $db->getTable('UserPermissionsPermsissions');
                    $accessRecords = $accessTable->findBy(array('user_id' => $role->id,
                                                                'item_id' => $resource->id, ));

                    return !empty($accessRecords);
    //                _log($id, $priority = Zend_Log::ERR);
                }
            }

        }

        return false;
    }
}
