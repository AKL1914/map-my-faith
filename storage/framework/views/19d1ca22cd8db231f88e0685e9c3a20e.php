<!-- resources/views/welcome.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Map My Faith</title>

    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Map My Faith</a>
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

<div class="container text-center flex-grow-1 d-flex flex-column justify-content-center align-items-center">
    <h1 class="mb-4">Welcome to Map My Faith</h1>

    <?php if(auth()->guard()->check()): ?>
        <div id="app" class="w-100"></div> <!-- Vue mounts here -->

        <div>
            <a href="<?php echo e(route('maps.index')); ?>" class="btn btn-secondary">View Maps</a>
        </div>
    <?php else: ?>
        <div class="mb-3">
            <a href="<?php echo e(route('login.google')); ?>" class="btn btn-primary btn-lg">Login with Gmail</a>
        </div>

    <?php endif; ?>
</div>

<?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</html>

<?php /**PATH /var/www/html/resources/views/welcome.blade.php ENDPATH**/ ?>