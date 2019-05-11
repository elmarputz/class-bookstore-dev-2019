<?php 
use Data\DataManager;
$categories = DataManager::getCategories();

require_once('views/partials/header.php'); ?>

<ul class="nav nav-tabs">
    <?php foreach ($categories as $cat) { ?>
        <li role="presentation">
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?view=list&categoryId=<?php echo urlencode($cat->getId()); ?>"><?php echo $cat->getName(); ?></a> 
        </li>
    <?php } ?>
</ul>


<?php require_once('views/partials/footer.php'); ?>