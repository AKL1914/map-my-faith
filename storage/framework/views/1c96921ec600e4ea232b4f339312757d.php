<?php $__env->startSection('content'); ?>
    <!-- Main Content -->
    <main class="container my-5 flex-grow-1">
        <h1 class="text-center mb-4">Leaderboard</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-striped shadow-sm bg-white">
                <thead class="table-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Pins</th>
                </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $leaderboardData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <th scope="row"><?php echo e($index + 1); ?></th>
                        <td><?php echo e($entry->name); ?></td>
                        <td><?php echo e($entry->pinCount); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/admin/leaderboard/index.blade.php ENDPATH**/ ?>