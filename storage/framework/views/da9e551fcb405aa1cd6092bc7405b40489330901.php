<?php
    $_menu = config('os_dashboard.menu');
?>
<?php if($_menu): ?>
    <ul class="uk-navbar-nav"
        uk-nav>
        <?php $__currentLoopData = $_menu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(isset($_item['children']) && count($_item['children'])): ?>
                <?php
                    $_access_item = FALSE;
                    if(isset($_item['permission']) && $_item['permission']){
                        foreach ($_item['permission'] as $_permission_item) {
                            if($_wrap['user']->can($_permission_item)) {
                                $_access_item = TRUE;
                                break;
                            }
                        }
                    }else{
                        $_access_item = TRUE;
                    }
                ?>
                <?php if($_access_item): ?>
                    <?php
                        $_children = collect($_item['children']);
                        $_children_routes = $_children->pluck('route');
                    ?>
                    <li class="uk-parent<?php echo e(_ar($_children_routes->all())); ?>">
                        <a href="javascript:void(0);"
                           rel="nofollow">
                            <?php echo $_item['link']; ?>

                            <span uk-icon="keyboard_arrow_down"></span>
                        </a>
                        <div class="uk-navbar-dropdown"
                             uk-dropdown="mode: click">
                            <ul class="uk-nav uk-navbar-dropdown-nav">
                                <?php $__currentLoopData = $_children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_item_children): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if((!isset($_item_children['route']) || is_null($_item_children['route'])) && isset($_item_children['children']) && count($_item_children['children'])): ?>
                                        <?php
                                            $_access_item_children = FALSE;
                                            if(isset($_item_children['permission']) && $_item_children['permission']){
                                                foreach ($_item_children['permission'] as $_permission_item) {
                                                    if($_wrap['user']->can($_permission_item)) {
                                                        $_access_item_children = TRUE;
                                                        break;
                                                    }
                                                }
                                            }else{
                                                $_access_item_children = TRUE;
                                            }
                                        ?>
                                        <?php if($_access_item): ?>
                                            <?php
                                                $_children_2 = collect($_item_children['children']);
                                                $_children_routes_2 = $_children_2->pluck('route');
                                            ?>
                                            <li class="uk-parent<?php echo e(_ar($_children_routes_2->all())); ?>">
                                                <a href="javascript:void(0);"
                                                   rel="nofollow">
                                                    <?php echo $_item_children['link']; ?>

                                                </a>
                                                <ul class="uk-nav-sub">
                                                    <?php $__currentLoopData = $_children_2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_item_children_2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php if($_wrap['user']->can($_item_children_2['permission'])): ?>
                                                            <li class="<?php echo e(_ar($_item_children_2['route'], ($_item_children_2['params'] ?? []))); ?>">
                                                                <a href="<?php echo e(_r($_item_children_2['route'], ($_item_children_2['params'] ?? []))); ?>">
                                                                    <?php echo $_item_children_2['link']; ?>

                                                                </a>
                                                            </li>
                                                        <?php endif; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </ul>
                                            </li>
                                        <?php endif; ?>
                                    <?php elseif($_item_children['route']): ?>
                                        <?php if((isset($_item_children['permission']) && $_item_children['permission'] && $_wrap['user']->can($_item_children['permission'])) || (!isset($_item_children['permission']) || is_null($_item_children['permission']))): ?>
                                            <li class="<?php echo e(_ar($_item_children['route'], ($_item_children['params'] ?? []))); ?>">
                                                <a href="<?php echo e(_r($_item_children['route'], ($_item_children['params'] ?? []))); ?>">
                                                    <?php echo $_item_children['link']; ?>

                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>
            <?php elseif($_item['route']): ?>
                <?php if($_wrap['user']->can($_item['permission'])): ?>
                    <li class="<?php echo e(_ar($_item['route'])); ?>">
                        <a href="<?php echo e(_r($_item['route'], ($_item['params'] ?? []))); ?>"
                           class="<?php echo e(_ar($_item['route'])); ?>">
                            <?php echo $_item['link']; ?>

                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
<?php endif; ?><?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/backend/menus/admin_menu.blade.php ENDPATH**/ ?>