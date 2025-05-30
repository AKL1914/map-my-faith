<?php $__env->startSection('head'); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/admindashboard.js']); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <!-- Main Content -->
    <div class="container mt-4">
        <div id="app"></div> <!-- Vue app mounts here -->
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/admin/dashboard.blade.php ENDPATH**/ ?>