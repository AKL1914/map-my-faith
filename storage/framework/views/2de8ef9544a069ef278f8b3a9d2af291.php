<!-- Navbar (non-collapsible) -->
<nav class="navbar navbar-dark bg-dark px-3">
    <div class="d-flex gap-2">
        <a class="btn btn-outline-light" href="<?php echo e(url('/')); ?>">Home</a>
        <a class="btn btn-outline-light" href="<?php echo e(url('/admin/dashboard')); ?>">Dashboard</a>
        <a class="btn btn-outline-light" href="<?php echo e(url('/admin/leaderboard')); ?>">Leaderboard</a>
        <a class="btn btn-outline-light" href="<?php echo e(url('/admin/campaigns')); ?>">Campaigns</a>
        <a class="btn btn-outline-light" href="<?php echo e(url('/maps')); ?>">Maps</a>
        <div class="d-flex">
            <?php if(auth()->guard()->check()): ?>
                <form action="<?php echo e(route('logout')); ?>" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-outline-light">Logout</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</nav>
<?php /**PATH /var/www/html/resources/views/partials/navigation.blade.php ENDPATH**/ ?>