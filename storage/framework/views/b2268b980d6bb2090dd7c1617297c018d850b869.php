<?php $__env->startSection('content'); ?>

    <div class="container">
        <?php if(Session::has('status')): ?>
            <div class="alert alert-info">
                <span><?php echo e(Session::get('status')); ?></span>
            </div>
        <?php endif; ?>
        <form action="<?php echo e(route('admin.setting.store')); ?>" method="post">
            <?php echo e(csrf_field()); ?>

            <div class="form-group">
                <label>Url callback для TelegramBot</label>
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Действие<span class="caret"></span></button>
                        <ul class="dropdown-menu">
                            <li><a href="#" onclick="document.getElementById('url_callback_bot').value = '<?php echo e(url('')); ?>'">Вставить url</a></li>
                            <li><a href="#" onclick="event.preventDefault(); document.getElementById('setwebhook').submit();">Отправить url</a></li>
                            <li><a href="#" onclick="event.preventDefault(); document.getElementById('getwebhookinfo').submit();">Получить информацию</a></li>
                        </ul>
                    </div>
                    <input type="url" class="form-control" id="url_callback_bot" name="url_callback_bot" value="<?php echo e(isset($url_callback_bot) ? $url_callback_bot : ''); ?>">
                </div>
            </div>

            <button class="btn btn-primary" type="submit">Сохранить</button>
        </form>

        <form id="setwebhook" action="<?php echo e(route('admin.setting.setwebhook')); ?>" method="POST" style="display: none;">
            <?php echo e(csrf_field()); ?>

            <input type="hidden" name="url" value="<?php echo e(isset($url_callback_bot) ? $url_callback_bot : ''); ?>">
        </form>

        <form id="getwebhookinfo" action="<?php echo e(route('admin.setting.getwebhookinfo')); ?>" method="POST" style="display: none;">
            <?php echo e(csrf_field()); ?>

        </form>
    </div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('backend.layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>