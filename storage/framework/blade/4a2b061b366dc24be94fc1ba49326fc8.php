<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; }
        .header { background: #5b4cdb; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 24px; background: #fff; border: 1px solid #e0e0e0; }
        .footer { padding: 16px 24px; background: #f5f5f5; font-size: 12px; color: #999; border-radius: 0 0 8px 8px; }
        .btn { display: inline-block; padding: 12px 24px; background: #5b4cdb; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header"><h1><?php echo e($companyName ?? 'Wellness Behavioral Health'); ?></h1></div>
    <div class="content">
        <?php echo nl2br(e($body)); ?>

        <?php if(!empty($actionUrl)): ?>
        <p style="text-align:center;margin-top:24px"><a href="<?php echo e($actionUrl); ?>" class="btn"><?php echo e($actionText ?? 'Take Action'); ?></a></p>
        <?php endif; ?>
    </div>
    <div class="footer">&copy; <?php echo e(date('Y')); ?> <?php echo e($companyName ?? 'Wellness Behavioral Health'); ?></div>
</body>
</html>
<?php /**PATH F:\laravel projects\hrportal\resources\views\emails\candidate-notification.blade.php ENDPATH**/ ?>