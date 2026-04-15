<?php
require_once __DIR__ . '/includes/auth.php';

// Logout
if (($_GET['action'] ?? '') === 'logout') {
    doLogout();
    header('Location: /');
    exit;
}

// Already logged in
if (isLoggedIn()) {
    header('Location: /');
    exit;
}

// Login attempt
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if (doLogin($username, $password)) {
        header('Location: /');
        exit;
    }
    $error = 'Neteisingas vartotojo vardas arba slaptažodis';
}

$pageTitle = 'Prisijungti — VisiSkelbimai';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/head.php';
?>

<div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-gray-900 dark:bg-white flex items-center justify-center text-2xl font-bold text-white dark:text-gray-900 shadow-lg mx-auto mb-4">
                V
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">VisiSkelbimai</h1>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Prisijunkite prie paieškos valdymo</p>
        </div>

        <div class="card p-6">
            <form method="post" class="space-y-4">
                <?php if ($error): ?>
                <div class="p-3 rounded-lg bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-600 dark:text-red-400 text-sm">
                    <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>

                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-slate-400 mb-1.5">Vartotojas</label>
                    <input type="text" name="username" required autofocus
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                           class="w-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100
                                  focus:outline-none focus:border-gray-400 dark:focus:border-slate-500
                                  placeholder:text-gray-400 dark:placeholder:text-slate-500"
                           placeholder="egopb">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-slate-400 mb-1.5">Slaptažodis</label>
                    <input type="password" name="password" required
                           class="w-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100
                                  focus:outline-none focus:border-gray-400 dark:focus:border-slate-500
                                  placeholder:text-gray-400 dark:placeholder:text-slate-500"
                           placeholder="••••••••">
                </div>

                <button type="submit"
                        class="w-full bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-gray-900 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    Prisijungti
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-gray-400 dark:text-slate-500 mt-6">
            Testas: egopb / egopb@egopb
        </p>
    </div>
</div>

</body>
</html>
