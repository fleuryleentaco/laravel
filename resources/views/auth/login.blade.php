<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | Espace Immersif</title>
    
    <!-- Tailwind CSS pour un design rapide et moderne -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts: Inter, pour une typographie épurée -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Définition de la police par défaut */
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Effet de fond animé subtil */
        .animated-gradient {
            background-size: 200% 200%;
            animation: gradient-animation 15s ease infinite;
        }

        @keyframes gradient-animation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Style pour l'effet de verre dépoli (glassmorphism) */
        .glass-effect {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Placeholder personnalisé pour le thème sombre */
        .form-input::placeholder {
            color: #9ca3af;
        }
        
        /* Amélioration du focus pour l'accessibilité et le style */
        .form-input:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.5); /* Couleur Indigo-500 avec opacité */
        }
    </style>
</head>
<body class="bg-slate-900 text-white">

    <div class="flex min-h-screen">
        <!-- Section gauche : Visuel et Branding -->
        <div class="hidden lg:flex flex-1 items-center justify-center p-12 bg-gradient-to-br from-indigo-600 via-purple-600 to-fuchsia-500 animated-gradient">
            <div class="text-center max-w-md">
                <h1 class="text-4xl font-bold tracking-tight mb-4">Bienvenue à Nouveau.</h1>
                <p class="text-lg text-indigo-100 opacity-90">
                    Connectez-vous pour accéder à une expérience utilisateur réinventée. Votre univers digital vous attend.
                </p>
                <!-- Élément de design abstrait -->
                <div class="mt-10 w-32 h-32 mx-auto border-4 border-white/20 rounded-full flex items-center justify-center">
                    <div class="w-20 h-20 bg-white/20 rounded-full"></div>
                </div>
            </div>
        </div>

        <!-- Section droite : Formulaire de connexion -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-md">
                <div class="glass-effect p-8 md:p-10 rounded-2xl shadow-2xl">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-gray-100">Accès Compte</h2>
                        <p class="text-gray-400 mt-2">Heureux de vous revoir !</p>
                    </div>

                    <form action="{{ route('login') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Champ Email -->
                        <div>
                            <div class="relative">
                                <label for="email" class="sr-only">Adresse Email</label>
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                      <path d="M3 4a2 2 0 00-2 2v1.161l8.441 4.221a1.25 1.25 0 001.118 0L19 7.162V6a2 2 0 00-2-2H3z" />
                                      <path d="M19 8.839l-7.77 3.885a2.75 2.75 0 01-2.46 0L1 8.839V14a2 2 0 002 2h14a2 2 0 002-2V8.839z" />
                                    </svg>
                                </div>
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    autocomplete="email"
                                    required
                                    autofocus
                                    value="{{ old('email') }}"
                                    class="form-input block w-full pl-10 pr-3 py-3 bg-white/5 border rounded-lg placeholder-gray-400 transition duration-300 ease-in-out focus:border-indigo-400 @error('email') border-red-500 @else border-white/10 @enderror"
                                    placeholder="votre.email@exemple.com"
                                >
                            </div>
                            @error('email')
                                <span class="block mt-1.5 text-xs text-red-400" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Champ Mot de passe -->
                        <div>
                            <div class="relative">
                                <label for="password" class="sr-only">Mot de passe</label>
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    autocomplete="current-password"
                                    required
                                    class="form-input block w-full pl-10 pr-3 py-3 bg-white/5 border rounded-lg placeholder-gray-400 transition duration-300 ease-in-out focus:border-indigo-400 @error('password') border-red-500 @else border-white/10 @enderror"
                                    placeholder="Mot de passe"
                                >
                            </div>
                             @error('password')
                                <span class="block mt-1.5 text-xs text-red-400" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <!-- Options supplémentaires -->
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center">
                                <input id="remember" name="remember" type="checkbox" class="h-4 w-4 bg-transparent border-gray-500 text-indigo-500 focus:ring-indigo-500 rounded" {{ old('remember') ? 'checked' : '' }}>
                                <label for="remember" class="ml-2 block text-gray-300">Se souvenir de moi</label>
                            </div>
                             @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="font-medium text-indigo-400 hover:text-indigo-300 transition duration-300">
                                    Mot de passe oublié ?
                                </a>
                            @endif
                        </div>
                        
                        <!-- Bouton de connexion -->
                        <div>
                            <button
                                type="submit"
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-fuchsia-600 to-indigo-600 hover:from-fuchsia-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-900 focus:ring-indigo-500 transform hover:scale-105 transition duration-300"
                            >
                                Se connecter
                            </button>
                        </div>
                    </form>

                    <!-- Séparateur et connexions sociales -->
                    <div class="mt-8">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-700"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-slate-800 text-gray-400 rounded-md">Ou continuer avec</span>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-3 gap-3">
                            <!-- Bouton Google -->
                            <a href="#" class="w-full inline-flex justify-center py-3 px-4 border border-gray-700 rounded-lg shadow-sm bg-white/5 text-sm font-medium text-gray-300 hover:bg-white/10 transition duration-300">
                                <span class="sr-only">Se connecter avec Google</span>
                                <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                                </svg>
                            </a>
                             <!-- Bouton GitHub -->
                            <a href="#" class="w-full inline-flex justify-center py-3 px-4 border border-gray-700 rounded-lg shadow-sm bg-white/5 text-sm font-medium text-gray-300 hover:bg-white/10 transition duration-300">
                                <span class="sr-only">Se connecter avec GitHub</span>
                                <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.477 2 12c0 4.418 2.865 8.168 6.839 9.492.5.092.682-.217.682-.482 0-.237-.009-.868-.014-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.031-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.378.203 2.398.1 2.651.64.7 1.03 1.595 1.03 2.688 0 3.848-2.338 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.001 10.001 0 0022 12c0-5.523-4.477-10-10-10z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            <!-- Bouton Twitter (X) -->
                            <a href="#" class="w-full inline-flex justify-center py-3 px-4 border border-gray-700 rounded-lg shadow-sm bg-white/5 text-sm font-medium text-gray-300 hover:bg-white/10 transition duration-300">
                                <span class="sr-only">Se connecter avec X</span>
                               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865l8.875 11.633Z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                 <!-- Lien d'inscription -->
                <p class="mt-8 text-center text-sm text-gray-400">
                    Pas encore de compte ?
                    <a href="{{ route('register') }}" class="font-medium text-indigo-400 hover:text-indigo-300 transition duration-300">
                        S'inscrire
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>

