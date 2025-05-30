<?php $__env->startSection('head'); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/js/app.js'); ?> <!-- Important for Vue -->
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <main class="container text-center my-1 flex-grow-1">
        <div id="app"></div> <!-- Vue mounts here -->
    </main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/maps/index.blade.php ENDPATH**/ ?>