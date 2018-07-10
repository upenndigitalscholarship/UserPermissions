<?php
echo head(array('title' => __('Permission Properties')));
$vocabulary = $this->item_relations_vocabulary;
$properties = $vocabulary->getProperties();
?>
<?php if ($vocabulary->custom): ?>
<a class="button" href="<?php echo html_escape($this->url("item-relations/vocabularies/edit/id/{$vocabulary->id}")); ?>" class="edit"><?php echo __('Edit Permissions'); ?></a>
<?php endif; ?>

<h2><?php echo $vocabulary->name; ?></h2>
<p><?php echo url_to_link(html_escape($vocabulary->description)); ?></p>
<?php if (!$properties): ?>
<p>
    <?php echo __('This user has no special permissions.'); ?>
    <?php if ($vocabulary->custom): ?>
    <a href="<?php echo html_escape($this->url("item-relations/vocabularies/edit/id/{$vocabulary->id}")); ?>"><?php echo __("Why don't you add some?"); ?></a>
    <?php endif; ?>
</p>
<?php else: ?>
<table>
    <thead>
    <tr>
        <th><?php echo __('User'); ?></th>
        <th><?php echo __('Permission ID'); ?></th>
        <th><?php echo __('Viewable Items'); ?></th>
    </tr>
    </thead>
    <tbody>
<?php foreach ($properties as $property): ?>
    <tr>
        <td><?php echo $vocabulary->custom ? '<span style="color:#ccc;">n/a</span>' : $property->local_part; ?></td>
        <td><?php echo __($property->label); ?></td>
        <td><?php echo __($property->description); ?></td>
    </tr>
<?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
<a class="button" href="<?php echo html_escape($this->url("item-relations/vocabularies/edit/id/{$vocabulary->id}")); ?>" class="edit"><?php echo __('Delete Permissions*'); ?></a>
<?php echo foot(); ?>
