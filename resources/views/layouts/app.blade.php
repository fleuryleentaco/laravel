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

  <nav class="w-full py-4 px-6 lg:px-12 bg-slate-900/95 backdrop-blur-md fixed top-0 left-0 z-50 border-b border-white/10">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
      <a href="{{ url('/') }}" class="text-xl font-semibold">{{ config('app.name','AntiPlag') }}</a>
      
      <!-- Mobile menu button -->
      <button id="mobileMenuBtn" class="lg:hidden text-white">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
      </button>
      
      <div id="desktopMenu" class="hidden lg:flex items-center gap-4">
        <!-- Language Selector -->
        <div class="relative group">
          <button onclick="document.getElementById('lang-dropdown').classList.toggle('hidden')" class="text-sm text-indigo-200 hover:text-white flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
            </svg>
            <span class="uppercase">{{ app()->getLocale() }}</span>
          </button>
          <div id="lang-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-gray-900 border border-indigo-700 rounded-lg shadow-lg z-50">
            @foreach(config('app.supported_locales', ['fr' => ['native' => 'Français'], 'en' => ['native' => 'English']]) as $code => $locale)
              <a href="{{ url()->current() }}?lang={{ $code }}" class="block px-4 py-2 text-sm text-indigo-200 hover:bg-indigo-900/30 {{ app()->getLocale() === $code ? 'bg-indigo-900/50 font-semibold' : '' }}">
                {{ $locale['native'] }}
              </a>
            @endforeach
          </div>
        </div>
        
        @guest
          <a href="{{ route('login') }}" class="text-sm text-indigo-200 hover:text-white">Se connecter</a>
          <a href="{{ route('register') }}" class="text-sm text-indigo-200 hover:text-white">S'inscrire</a>
        @else
          <a href="{{ route('documents.index') }}" class="text-indigo-200 hover:text-white">Mes documents</a>
          <!-- <a href="{{ route('profile.show') }}" class="text-indigo-200 hover:text-white">{{ Auth::user()->name ?? 'Profil' }}</a> -->
          <div class="relative group">
                <button onclick="document.getElementById('notif-dropdown').classList.toggle('hidden')" class="focus:outline-none relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    @php $notifCount = Auth::user()->unreadNotifications->count(); @endphp
                    @if($notifCount > 0)
                        <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold rounded-full px-2 py-0.5">{{ $notifCount }}</span>
                    @endif
                </button>
                <div id="notif-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-gray-900 border border-indigo-700 rounded-lg shadow-lg z-50 max-h-96 overflow-y-auto">
                    <div class="p-4 text-indigo-200 font-semibold border-b border-indigo-700">Notifications</div>
                    @foreach(Auth::user()->unreadNotifications as $notification)
                        <div class="px-4 py-3 border-b border-gray-800 text-sm text-indigo-100">
                            {{ $notification->data['message'] ?? '' }}
                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="inline">
                                @csrf
                                <button class="ml-2 text-xs text-yellow-400 hover:underline">Marquer comme lu</button>
                            </form>
                        </div>
                    @endforeach
                    @if(Auth::user()->unreadNotifications->isEmpty())
                        <div class="px-4 py-6 text-center text-gray-400">Aucune notification</div>
                    @endif
                </div>
            </div>
          @if((auth()->user()->id_role_user ?? 0)==1)
            <div class="relative">
              <button id="adminMenuBtn" class="text-sm text-indigo-200 hover:text-white">Admin ▾</button>
              <div id="adminMenu" class="hidden absolute right-0 mt-2 w-48 bg-white/5 glass-effect p-2 rounded-md">
                <a href="{{ route('admin.users') }}" class="block px-3 py-2 text-sm text-white hover:bg-white/5 rounded">Utilisateurs</a>
                <a href="{{ route('admin.documents') }}" class="block px-3 py-2 text-sm text-white hover:bg-white/5 rounded">Fichiers</a>
                <a href="{{ route('admin.errors') }}" class="block px-3 py-2 text-sm text-white hover:bg-white/5 rounded">Erreurs</a>
                <a href="{{ route('admin.reports') }}" class="block px-3 py-2 text-sm text-white hover:bg-white/5 rounded">Rapports</a>
                <a href="{{ route('admin.incoming') }}" class="block px-3 py-2 text-sm text-white hover:bg-white/5 rounded">Docs externes</a>
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
    
    <!-- Mobile menu -->
    <div id="mobileMenu" class="hidden lg:hidden bg-slate-900 border-t border-white/10 py-4">
      <div class="flex flex-col gap-3 px-6">
        @guest
          <a href="{{ route('login') }}" class="text-sm text-indigo-200 hover:text-white py-2">Se connecter</a>
          <a href="{{ route('register') }}" class="text-sm text-indigo-200 hover:text-white py-2">S'inscrire</a>
        @else
          <a href="{{ route('documents.index') }}" class="text-indigo-200 hover:text-white py-2">Mes documents</a>
          @if((auth()->user()->id_role_user ?? 0)==1)
            <div class="border-t border-white/10 pt-2 mt-2">
              <div class="text-xs text-gray-400 mb-2">Administration</div>
              <a href="{{ route('admin.users') }}" class="block text-sm text-white hover:text-indigo-300 py-2">Utilisateurs</a>
              <a href="{{ route('admin.documents') }}" class="block text-sm text-white hover:text-indigo-300 py-2">Fichiers</a>
              <a href="{{ route('admin.errors') }}" class="block text-sm text-white hover:text-indigo-300 py-2">Erreurs</a>
              <a href="{{ route('admin.reports') }}" class="block text-sm text-white hover:text-indigo-300 py-2">Rapports</a>
              <a href="{{ route('admin.incoming') }}" class="block text-sm text-white hover:text-indigo-300 py-2">Docs externes</a>
            </div>
          @endif
          <div class="border-t border-white/10 pt-2 mt-2">
            <a href="{{ route('profile.show') }}" class="block text-sm text-white hover:text-indigo-300 py-2">Mon profil</a>
            <a href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="block text-sm text-white hover:text-indigo-300 py-2">Déconnexion</a>
          </div>
        @endguest
      </div>
    </div>
  </nav>

  <div class="flex min-h-screen pt-20">
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
    // Mobile menu toggle
    document.getElementById('mobileMenuBtn')?.addEventListener('click', function(e){
      e.stopPropagation();
      const menu = document.getElementById('mobileMenu');
      if(!menu) return;
      menu.classList.toggle('hidden');
    });
    
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
      const mobileMenu = document.getElementById('mobileMenu');
      
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
      if(mobileMenu && !mobileMenu.classList.contains('hidden')){
        const btnM = document.getElementById('mobileMenuBtn');
        if(btnM && !btnM.contains(e.target) && !mobileMenu.contains(e.target)){
          mobileMenu.classList.add('hidden');
        }
      }
    });
  </script>
</body>
</html>
