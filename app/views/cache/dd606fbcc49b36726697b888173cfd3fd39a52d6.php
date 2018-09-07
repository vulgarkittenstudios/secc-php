 
<?php $__env->startSection('title'); ?>Home <?php $__env->stopSection(); ?> 
<?php $__env->startSection('content'); ?> 
<h1>Hello There</h1>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('templates.frontend.main', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>