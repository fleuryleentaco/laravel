<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name','Laravel') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
      body{font-family: 'Inter', sans-serif}
      .animated-gradient{background-size:200% 200%;animation:gradient-animation 15s ease infinite}
      @keyframes gradient-animation{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
      .glass-effect{background:rgba(255,255,255,0.05);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,0.08)}
    </style>
</head>
<body class="bg-slate-900 text-white min-h-screen">

  <nav class="w-full py-4 px-6 lg:px-12 bg-transparent absolute top-0 left-0 z-20">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
      <a href="{{ url('/') }}" class="text-xl font-semibold">{{ config('app.name','AntiPlag') }}</a>
      <div class="flex items-center gap-4">
        @guest
          <a href="{{ route('login') }}" class="text-sm text-indigo-200 hover:text-white">Se connecter</a>
          <a href="{{ route('register') }}" class="text-sm text-indigo-200 hover:text-white">S'inscrire</a>
        @else
          <a href="{{ route('documents.index') }}" class="text-sm text-indigo-200 hover:text-white">Mes documents</a>
          @if((auth()->user()->id_role_user ?? 0)==1)
            <div class="relative">
              <button id="adminMenuBtn" class="text-sm text-indigo-200 hover:text-white">Admin ▾</button>
              <div id="adminMenu" class="hidden absolute right-0 mt-2 w-48 bg-white/5 glass-effect p-2 rounded-md">
                <a href="{{ route('admin.users') }}" class="block px-3 py-2 text-sm text-white hover:bg-white/5 rounded">Utilisateurs</a>
                <a href="{{ route('admin.documents') }}" class="block px-3 py-2 text-sm text-white hover:bg-white/5 rounded">Fichiers</a>
                <a href="{{ route('admin.errors') }}" class="block px-3 py-2 text-sm text-white hover:bg-white/5 rounded">Erreurs</a>
                <a href="{{ route('admin.reports') }}" class="block px-3 py-2 text-sm text-white hover:bg-white/5 rounded">Rapports</a>
              </div>
            </div>
          @endif
          <div class="relative">
            <button id="userMenuBtn" class="text-sm text-indigo-200 hover:text-white">{{ auth()->user()->name ?? auth()->user()->email }}</button>
            <div id="userMenu" class="hidden absolute right-0 mt-2 w-56 bg-white/5 glass-effect p-2 rounded-md">
              <a href="{{ route('profile.show') }}" class="block px-3 py-2 text-sm text-white hover:bg-white/5 rounded">Mon profil</a>
              <a href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="block px-3 py-2 text-sm text-white hover:bg-white/5 rounded">Logout</a>
            </div>
          </div>
        @endguest
      </div>
    </div>
  </nav>

  <div class="flex min-h-screen pt-16">
  @unless(View::hasSection('no_hero'))
  <div class="hidden lg:flex lg:w-2/5 items-center justify-center p-12 bg-gradient-to-br from-indigo-600 via-purple-600 to-fuchsia-500 animated-gradient">
      <div class="text-center max-w-md">
        <h1 class="text-4xl font-bold tracking-tight mb-4">Bienvenue</h1>
        <p class="text-lg text-indigo-100 opacity-90">AntiPlag — analyse et gestion des documents.</p>
      </div>
    </div>
  @endunless

  <main class="w-full @unless(View::hasSection('no_hero')) lg:w-3/5 @else lg:w-full @endunless flex items-center justify-center p-6 sm:p-12">
      <div class="w-full @unless(View::hasSection('no_hero')) max-w-md @else max-w-4xl @endunless">
        <div class="glass-effect p-8 md:p-12 lg:p-16 rounded-2xl shadow-2xl">
          @yield('content')
        </div>
      </div>
    </main>
  </div>

  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>

  <script>
    document.getElementById('userMenuBtn')?.addEventListener('click', function(e){
      const menu = document.getElementById('userMenu');
      if(!menu) return;
      menu.classList.toggle('hidden');
    });
    // admin menu toggle
    document.getElementById('adminMenuBtn')?.addEventListener('click', function(e){
      e.stopPropagation();
      const menu = document.getElementById('adminMenu');
      if(!menu) return;
      menu.classList.toggle('hidden');
    });

    // close menus when clicking outside
    document.addEventListener('click', function(e){
      const userMenu = document.getElementById('userMenu');
      const adminMenu = document.getElementById('adminMenu');
      if(userMenu && !userMenu.classList.contains('hidden')){
        // if click outside userMenu and userMenuBtn
        const btn = document.getElementById('userMenuBtn');
        if(btn && !btn.contains(e.target) && !userMenu.contains(e.target)){
          userMenu.classList.add('hidden');
        }
      }
      if(adminMenu && !adminMenu.classList.contains('hidden')){
        const btnA = document.getElementById('adminMenuBtn');
        if(btnA && !btnA.contains(e.target) && !adminMenu.contains(e.target)){
          adminMenu.classList.add('hidden');
        }
      }
    });
  </script>
</body>
</html>
