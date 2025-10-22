<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ config('app.name','Laravel') }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body{font-family:'Inter',sans-serif;}
    .glass-effect{background:rgba(255,255,255,0.05);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,0.08);}
  </style>
</head>
<body class="bg-slate-900 text-white min-h-screen">

  <!-- Navbar -->
  <nav class="w-full py-4 px-6 lg:px-12 bg-transparent fixed top-0 left-0 z-20 backdrop-blur-md border-b border-white/10">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
      <a href="{{ url('/') }}" class="text-xl font-semibold">{{ config('app.name','AntiPlag') }}</a>
      <div class="flex items-center gap-4">
        <a href="{{ route('documents.index') }}" class="text-sm text-indigo-200 hover:text-white">Documents</a>
        <a href="{{ route('admin.users') }}" class="text-sm text-indigo-200 hover:text-white">Utilisateurs</a>
        <a href="{{ route('admin.errors') }}" class="text-sm text-indigo-200 hover:text-white">Erreurs</a>
        <a href="{{ route('admin.reports') }}" class="text-sm text-indigo-200 hover:text-white">Rapports</a>
        <button onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="text-sm text-red-300 hover:text-white">Déconnexion</button>
      </div>
    </div>
  </nav>

  <!-- Contenu principal -->
  <main class="pt-20 px-6 lg:px-12 pb-12 max-w-7xl mx-auto w-full">
    <div class="glass-effect rounded-2xl p-8 md:p-10 shadow-xl">
      @yield('content')
    </div>
  </main>

  <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
</body>
</html>
