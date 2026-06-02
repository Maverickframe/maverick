<?php if(!empty($args['department_link'])): ?>
    <a class="department-item" href="<?php echo $args['department_link']; ?>">
<?php else: ?>
    <div class="department-item">
<?php endif; ?>
        <?php lazy_attachment($args['photo'], 'medium'); ?>
        <h3 class="department-item__name"><?php echo $args['name']; ?></h3>
        <p class="department-item__position"><?php echo $args['position']; ?></p>
        <span class="department-item__link"><?php echo $args['department']; ?></span>
        <p class="department-item__description"><?php echo $args['description']; ?></p>
<?php if(!empty($args['department_link'])): ?>
    </a>
<?php else: ?>
    </div>
<?php endif; ?>