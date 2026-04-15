<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'VisiSkelbimai' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                    colors: {
                        cream: '#f5f0eb',
                        sand: '#ebe5de',
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .score-bar { transition: width 0.6s ease-out; }
        .card { background: #fff; border: 1px solid #e8e2db; border-radius: 12px; }
        .card-hover { transition: all 0.15s ease; }
        .card-hover:hover { box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
        .sidebar-link { transition: all 0.12s ease; border-radius: 8px; }
        .sidebar-link:hover { background: rgba(0,0,0,0.04); }
        .sidebar-link.active { background: rgba(0,0,0,0.06); font-weight: 600; }
        @keyframes pulse-dot { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
        .pulse-dot { animation: pulse-dot 2s ease-in-out infinite; }
    </style>
</head>
<body class="bg-cream text-gray-900 min-h-screen flex">
