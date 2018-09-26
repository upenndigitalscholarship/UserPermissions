<p>
<?php

echo __('Assign viewing permissions to users with in the Guest role. User can only view this item on the public interface, not through the admin dashboard.'
);
?>
</p>
<table>
    <thead>
    <tr>
        <th><?php echo __('Guest User'); ?></th>
        <th><?php echo __('Delete --> ...'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($userPermissions as $permitted_user): ?>
    <tr>
        <td><?php echo $permitted_user['username']; ?></td>
        <td><input type="checkbox" name="user_permissions_permission_delete[]" value="<?php echo $permitted_user['id']; ?>" /></td>
    </tr>
    <?php endforeach; ?>
    <tr class="permissions-entry">
        <td><?php echo get_view()->formSelect('user', @$_REQUEST['user'], array('id' => 'user-search'), get_table_options('User')); ?></td>
        <td><span style="color:#ccc;">n/a</span></td>
    </tr>
    </tbody>
</table>
<button type="button" class="item-relations-add-relation"><?php echo __('Add a Relation'); ?></button>
<script type="text/javascript">
jQuery(document).ready(function () {
    jQuery('.item-relations-add-relation').click(function () {
        var oldRow = jQuery('.item-relations-entry').last();
        var newRow = oldRow.clone();
        oldRow.after(newRow);
        var inputs = newRow.find('input, select');
        inputs.val('');
    });
});
</script>
