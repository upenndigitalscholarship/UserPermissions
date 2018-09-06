<?php
/**
 * User Permissions
 */

/**
 * User Permissions Permissions table.
 */
class Table_UserPermissionsPermissions extends Omeka_Db_Table
{
    /**
     * Get the default select object.
     *
     *
     * @return Omeka_Db_Select
     */
    public function getSelect()
    {
        $db = $this->getDb();
        return parent::getSelect();
    }

    /**
     * Get column names to be used for making a select dropdown.
     *
     * @return array
     */
    protected function _getColumnPairs()
    {
        return array('user_permissions_permissions.id', 'user_permissions_permissions.user_id');
    }

    /**
     * Find user permissions by item id.
     *
     * @return array
     */
    public function findByItemId($itemId)
    {
        $db = $this->getDb();
        $select = $this->getSelect()
            ->where('user_permissions_permissions.item_id = ?', (int) $itemId);
        return $this->fetchObjects($select);
    }

    /**
     * Find user permissions by user id.
     *
     * @return array
     */
    public function findByUserId($userId)
    {
        $db = $this->getDb();
        $select = $this->getSelect()
            ->where('user_permissions_permissions.user_id = ?', (int) $userId);
        return $this->fetchObjects($select);
    }

}
