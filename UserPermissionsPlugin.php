<?php
/**
 * User Permissions
 */

/**
 * User Permissions plugin.
 */
class UserPermissionsPlugin extends Omeka_Plugin_AbstractPlugin
{
    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array(
        'install',
        'uninstall',
        'define_acl',
        'after_save_item',
        'admin_items_show_sidebar',
        'admin_items_search',
        'admin_items_batch_edit_form',
        'items_batch_edit_custom',
        'public_items_show',
        'items_browse_sql',
    );

    /**
     * @var array Filters for the plugin.
     */
    protected $_filters = array(
        'admin_items_form_tabs',
    );

    /**
     * Install the plugin.
     */
    public function hookInstall()
    {
        // Create tables.
        $db = $this->_db;

        $sql = "
        CREATE TABLE IF NOT EXISTS `$db->UserPermissionsPermissions` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `item_id` int(10) unsigned NOT NULL,
            `user_id` int(10) unsigned NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        $db->query($sql);
    }

    /**
     * Uninstall the plugin.
     */
    public function hookUninstall()
    {
        $db = $this->_db;

        // Drop the vocabularies table.
        $sql = "DROP TABLE IF EXISTS `$db->UserPermissionsPermissions`";
        $db->query($sql);

        $this->_uninstallOptions();
    }


    /**
     * Define the ACL.
     *
     * @param array $args
     */
    public function hookDefineAcl($args) {
        $acl = $args['acl'];
        $acl->allow('guest',
                    'Items',
                    array('view','showNotPublic'),
                    new PermissionsAccessAclAssertion());
    }


    //if ($acl->isAllowed($user, $resource, $privilege)) {
      // display item on page
    //}

    /**
     * Display item relations on the public items show page.
     */
    public function hookPublicItemsShow() {
        if (get_option('item_relations_public_append_to_items_show')) {
            $item = get_current_record('item');

            echo common('item-relations-show', array(
                'subjectRelations' => self::prepareSubjectRelations($item),
                'objectRelations' => self::prepareObjectRelations($item)
            ));
        }
    }

    /**
     * Display user permissions on the admin items show page.
     *
     * @param Item $item
     */
    public function hookAdminItemsShowSidebar($args)
    {
        $item = $args['item'];

        echo common('item-relations-show', array(
            'subjectRelations' => self::prepareSubjectRelations($item),
            'objectRelations' => self::prepareObjectRelations($item)
        ));
    }

    /**
     * Display the item relations form on the admin advanced search page.
     */
    public function hookAdminItemsSearch()
    {
        echo common('item-relations-advanced-search', array(
            'formSelectProperties' => get_table_options('ItemRelationsProperty'))
        );
    }

    /**
     * Save the item relations after saving an item add/edit form.
     *
     * @param array $args
     */
    public function hookAfterSaveItem($args)
    {
        if (!$args['post']) {
            return;
        }

        $record = $args['record'];
        $post = $args['post'];

        $db = $this->_db;

        // Save item relations.
        if (isset($post['user'])) {
            foreach ($post['user'] as $key => $user_id) {
                self::insertUserPermissions(
                    $user_id,
                    $record
                );
            }
        }

        // Delete item relations.
        if (isset($post['item_relations_relation_delete'])) {
            foreach ($post['item_relations_relation_delete'] as $userPermissionsId) {
                $userPermissions = $db->getTable('ItemRelationsRelation')->find($userPermissionsId);
                // When an item is related to itself, deleting both relations
                // simultaneously will result in an error. Prevent this by
                // checking if the item relation exists prior to deletion.
                if ($userPermissions) {
                    $userPermissions->delete();
                }
            }
        }
    }

    /**
     * Filter for an item relation after search page submission.
     *
     * @param array $args
     */
    public function hookItemsBrowseSql($args)
    {
        $select = $args['select'];
        $params = $args['params'];

        if (isset($params['item_relations_property_id'])
            && is_numeric($params['item_relations_property_id'])
        ) {
            $db = $this->_db;
            // Set the field on which to join.
            if (isset($params['item_relations_clause_part'])
                && $params['item_relations_clause_part'] == 'object'
            ) {
                $onField = 'object_item_id';
            } else {
                $onField = 'subject_item_id';
            }
            $select
                ->join(
                    array('item_relations_relations' => $db->ItemRelationsRelation),
                    "item_relations_relations.$onField = items.id",
                    array()
                )
                ->where('item_relations_relations.property_id = ?',
                    $params['item_relations_property_id']
                );
        }
    }

    /**
     * Add custom fields to the item batch edit form.
     */
    public function hookAdminItemsBatchEditForm()
    {
        $formSelectProperties = get_table_options('ItemRelationsProperty');
?>
<fieldset id="item-relation-fields">
<h2><?php echo __('User Permissions'); ?></h2>
<table>
    <thead>
    <tr>
        <th><?php echo __('Subjects'); ?></th>
        <th><?php echo __('Relation'); ?></th>
        <th><?php echo __('Object');  ?></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td><?php echo __('These Items'); ?></td>
        <td><?php echo get_view()->formSelect('custom[item_relations_property_id]', null, array(), $formSelectProperties); ?></td>
        <td>
            <?php echo __('Item ID'); ?>
            <?php echo get_view()->formText('custom[item_relations_item_relation_object_item_id]', null, array('size' => 6)); ?>
        </td>
    </tr>
    </tbody>
</table>
</fieldset>
<?php
    }

    /**
     * Process the item batch edit form.
     *
     * @param array $args
     */
    public function hookItemsBatchEditCustom($args)
    {
        $item = $args['item'];
        $custom = $args['custom'];

        self::insertUserPermissions(
            $item,
            $custom['item_relations_property_id'],
            $custom['item_relations_item_relation_object_item_id']
        );
    }

    /**
     * Add the "User Permissions" tab to the admin items add/edit page.
     *
     * @return array
     */
    public function filterAdminItemsFormTabs($tabs, $args)
    {
        $item = $args['item'];

        $formSelectProperties = get_table_options('UserPermissionsPermissions');
        $userPermissions = self::prepareUserPermissions($item);
        $guest_users = self::prepareGuestUsers($item);

        ob_start();
        include 'user_permissions_form.php';
        $content = ob_get_contents();
        ob_end_clean();

        $tabs['User Permissions'] = $content;
        return $tabs;
    }

    /**
     * Prepare user permissions for display.
     *
     * @param Item $item
     * @return array
     */
    public static function prepareUserPermissions(Item $item)
    {
        $permissions = get_db()->getTable('UserPermissionsPermissions')->findByItemId($item->id);
        $permitted_users = array();
        foreach ($permissions as $permission) {
          $user = get_db()->getTable('User')->findActiveById($permission->user_id);
            if (!$user) {
                continue;
            }
            $permitted_users[] = array(
                'permission_id' => $permission->id,
                'user_id' => $permission->user_id,
                'username' => $user->name,
            );
            _log($permission->id . $permission->user_id . $user->name, $priority = Zend_Log::ERR);
        }
        return $permitted_users;
    }

    /**
     * Prepare list of unassigned guest users for display.
     *
     * @param Item $item
     * @return array
     */
    public static function prepareGuestUsers(Item $item)
    {
        $permissions = get_db()->getTable('UserPermissionsPermissions')->findByItemId($item->id);
        $guest_users = get_table_options('User');
        $permitted_users = array();
        foreach ($permissions as $permission) {
          $user = get_db()->getTable('User')->findActiveById($permission->user_id);
            if (!$user) {
                continue;
            }
            if (in_array($user, $guest_users)) {
                break;
            }
            $permitted_users[] = array(
                'permission_id' => $permission->id,
                'user_id' => $permission->user_id,
                'username' => $user->name,
            );
            _log($permission->id . $permission->user_id . $user->name, $priority = Zend_Log::ERR);
        }
        return $guest_users;
    }

    /**
     * Return a item's title.
     *
     * @param Item $item The item.
     * @return string
     */
    public static function getItemTitle($item)
    {
        $title = metadata($item, array('Dublin Core', 'Title'), array('no_filter' => true));
        if (!trim($title)) {
            $title = '#' . $item->id;
        }
        return $title;
    }

    /**
     * Insert an item relation.
     *
     * @param Item|int $subjectItem
     * @param int $propertyId
     * @param Item|int $objectItem
     * @return bool True: success; false: unsuccessful
     */
    public static function insertUserPermissions($user, $item)
    {
        // Only numeric item IDs are valid.
      //  if (!is_numeric($item_id)) {
        //    return false;
        //}

        // Set the item id.
        if (!($item instanceOf Item)) {
            $item = get_db()->getTable('items')->find($item);
        }

        // Set the user id.
        if (!($user instanceOf User)) {
            $user = get_db()->getTable('user')->find($user);
        }

        // Don't save the relation if the subject or object items don't exist.
        if (!$item || !$user) {
            return false;
        }

        $userPermission = new UserPermissionsPermissions;
        $userPermission->user_id = $user->id;
        $userPermission->item_id = $item->id;
        $userPermission->save();

        return true;
    }
}
