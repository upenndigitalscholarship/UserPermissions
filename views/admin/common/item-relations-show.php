<div class="item-relations panel">
  <h1>where will you go?;</h1>
    <h4><?php echo __('User Permissions**'); ?></h4>
    <div>
        <?php if (!$subjectRelations && !$objectRelations): ?>
        <p><?php echo __('This item has no relations.'); ?></p>
        <?php else: ?>
        <ul>
            <?php foreach ($subjectRelations as $subjectRelation): ?>
            <li>
                <?php echo __('This Item'); ?>
                <strong><?php echo $subjectRelation['relation_text']; ?></strong>
                <a href="<?php echo url('items/show/' . $subjectRelation['object_item_id']); ?>"><?php echo $subjectRelation['object_item_title']; ?></a>
            </li>
            <?php endforeach; ?>
            <?php foreach ($objectRelations as $objectRelation): ?>
            <li>
                <a href="<?php echo url('items/show/' . $objectRelation['subject_item_id']); ?>"><?php echo $objectRelation['subject_item_title']; ?></a>
                <strong><?php echo $objectRelation['relation_text']; ?></strong>
                <?php echo __('This Item'); ?>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </div>
</div>
