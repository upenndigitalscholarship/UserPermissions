<?php echo head(array('title' => __('Browse Permissions'))); ?>
<?php echo flash(); ?>
<table>
    <thead>
    <tr>
        <th><?php echo __('User'); ?></th>
        <th><?php echo __('Permission Class'); ?></th>
        <th><?php echo __('Permission ID'); ?></th>
        <th><?php echo __('Available Documents'); ?></th>
    </tr>
    </thead>
    <tbody>
<?php foreach ($this->item_relations_vocabularies as $vocabulary): ?>
    <tr>
        <td><a href="<?php echo html_escape($this->url("item-relations/vocabularies/show/id/{$vocabulary->id}")); ?>"><?php echo $vocabulary->name; ?></a></td>
        <td><?php echo __($vocabulary->description); ?></td>
        <td><?php echo $vocabulary->custom ? '<span style="color:#ccc;">n/a</span>' : $vocabulary->namespace_prefix; ?></td>
        <td><?php echo $vocabulary->custom ? '<span style="color:#ccc;">n/a</span>' : $vocabulary->namespace_uri; ?></td>
    </tr>
<?php endforeach; ?>
    </tbody>
</table>
<?php echo foot(); ?>
