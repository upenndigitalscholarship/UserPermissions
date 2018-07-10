<?php
/**
 * User Permissions
 */

/**
 * User Permissions Users model.
 */
class UserPermissionsUsers extends Omeka_Record_AbstractRecord {
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $item_id;

    /**
     * @var int
     */
    public $user_id;
}
