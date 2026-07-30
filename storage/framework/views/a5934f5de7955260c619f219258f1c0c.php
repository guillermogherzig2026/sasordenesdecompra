<?php $__env->startSection('body'); ?>
    <main class="login-shell">
        <section class="login-hero">
            <div class="brand-mark">OC</div>
            <p class="eyebrow">Sistema web interno</p>
            <h1>Sistema de Ordenes de Compra y Pagos</h1>
            <p>
                Administra la creacion, autorizacion, pago y recepcion de ordenes
                de compra con paneles separados para Finanzas, Compras e Inventarios.
            </p>
        </section>

        <section class="login-card" aria-label="Inicio de sesion">
            <div>
                <p class="eyebrow">ordenes.empresa.com</p>
                <h2>Iniciar sesion</h2>
            </div>

            <form method="POST" action="<?php echo e(route('login.store')); ?>" class="stack">
                <?php echo csrf_field(); ?>
                <label>
                    Correo
                    <input name="email" type="email" value="<?php echo e(old('email')); ?>" autocomplete="username" required autofocus>
                </label>
                <label>
                    Contrasena
                    <input name="password" type="password" autocomplete="current-password" required>
                </label>
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="form-error"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <button class="button primary" type="submit">Entrar</button>
            </form>
            <p class="hint">Acceso restringido a usuarios autorizados.</p>
        </section>
    </main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\sasordenesdecompra\resources\views/auth/login.blade.php ENDPATH**/ ?>