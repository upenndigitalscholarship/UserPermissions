<?php

class PermissionsAccessAclAssertion implements Zend_Acl_Assert_Interface {
    public function assert(
        Zend_Acl $acl,
        Zend_Acl_Role_Interface $role = null,
        Zend_Acl_Resource_Interface $resource = null,
        $privilege = null)
    {

        // it looks like without this always being true,
        // the second check doesn't get made
        // for this privilege, the exhibit object isn't passed in
        // so I can't check on that
        // whomp whomp
        if ($privilege == 'showNotPublic') {
//            return true;
            _log("privcheck2 : ".get_class($resource), $priority = Zend_Log::ERR);
            if (($role instanceof User) && get_class($resource) == 'Items') {
              _log("got inside acl if check: ".$role, $priority = Zend_Log::ERR);
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
