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
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-2xl font-bold shadow-lg shadow-violet-500/25 mx-auto mb-4">
                V
            </div>
            <h1 class="text-2xl font-bold">VisiSkelbimai</h1>
            <p class="text-sm text-slate-500 mt-1">Prisijunkite prie paieškos valdymo</p>
        </div>

        <!-- Login Form -->
        <form method="post" class="space-y-4">
            <?php if ($error): ?>
            <div class="p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <div>
                <label class="block text-sm font-medium text-slate-400 mb-1.5">Vartotojas</label>
                <input type="text" name="username" required autofocus
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                       class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-sm
                              focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500/50
                              placeholder:text-slate-600"
                       placeholder="admin">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-400 mb-1.5">Slaptažodis</label>
                <input type="password" name="password" required
                       class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-sm
                              focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500/50
                              placeholder:text-slate-600"
                       placeholder="••••••••">
            </div>

            <button type="submit"
                    class="w-full bg-violet-600 hover:bg-violet-500 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                Prisijungti
            </button>
        </form>

        <p class="text-center text-xs text-slate-600 mt-6">
            Testas: egopb / egopb@egopb
        </p>
    </div>
</div>

</body>
</html>
