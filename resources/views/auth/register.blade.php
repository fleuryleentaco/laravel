<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription | Espace Immersif</title>
    
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
                <h1 class="text-4xl font-bold tracking-tight mb-4">Rejoignez la Communauté.</h1>
                <p class="text-lg text-indigo-100 opacity-90">
                    Créez votre compte pour débloquer un monde de possibilités. L'aventure ne fait que commencer.
                </p>
                <!-- Élément de design abstrait -->
                <div class="mt-10 w-32 h-32 mx-auto border-4 border-white/20 rounded-full flex items-center justify-center">
                    <div class="w-20 h-20 bg-white/20 rounded-full"></div>
                </div>
            </div>
        </div>

        <!-- Section droite : Formulaire d'inscription -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-md">
                <div class="glass-effect p-8 md:p-10 rounded-2xl shadow-2xl">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-gray-100">Créer un Compte</h2>
                        <p class="text-gray-400 mt-2">C'est simple et rapide !</p>
                    </div>

                    <form action="{{ route('register') }}" method="POST" class="space-y-5">
                        @csrf

                        <!-- Champ Nom -->
                        <div>
                            <div class="relative">
                                <label for="name" class="sr-only">Nom</label>
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                      <path d="M10 8a3 3 0 100-6 3 3 0 000 6zM3.465 14.493a1.23 1.23 0 00.41 1.412A9.957 9.957 0 0010 18c2.31 0 4.438-.784 6.131-2.095a1.23 1.23 0 00.41-1.412A9.99 9.99 0 0010 12a9.99 9.99 0 00-6.535 2.493z" />
                                    </svg>
                                </div>
                                <input id="name" name="name" type="text" value="{{ old('name') }}" required autocomplete="name" autofocus class="form-input block w-full pl-10 pr-3 py-3 bg-white/5 border rounded-lg placeholder-gray-400 transition duration-300 ease-in-out focus:border-indigo-400 @error('name') border-red-500 @else border-white/10 @enderror" placeholder="Votre nom complet">
                            </div>
                             @error('name')
                                <span class="block mt-1.5 text-xs text-red-400" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

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
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" class="form-input block w-full pl-10 pr-3 py-3 bg-white/5 border rounded-lg placeholder-gray-400 transition duration-300 ease-in-out focus:border-indigo-400 @error('email') border-red-500 @else border-white/10 @enderror" placeholder="votre.email@exemple.com">
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
                                <input id="password" name="password" type="password" required autocomplete="new-password" class="form-input block w-full pl-10 pr-3 py-3 bg-white/5 border rounded-lg placeholder-gray-400 transition duration-300 ease-in-out focus:border-indigo-400 @error('password') border-red-500 @else border-white/10 @enderror" placeholder="Mot de passe">
                            </div>
                             @error('password')
                                <span class="block mt-1.5 text-xs text-red-400" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Champ Confirmation Mot de passe -->
                        <div>
                            <div class="relative">
                                <label for="password-confirm" class="sr-only">Confirmer le mot de passe</label>
                                 <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                      <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password" class="form-input block w-full pl-10 pr-3 py-3 bg-white/5 border border-white/10 rounded-lg placeholder-gray-400 transition duration-300 ease-in-out focus:border-indigo-400" placeholder="Confirmer le mot de passe">
                            </div>
                        </div>
                        
                        <!-- Bouton d'inscription -->
                        <div class="pt-2">
                            <button
                                type="submit"
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-fuchsia-600 to-indigo-600 hover:from-fuchsia-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-900 focus:ring-indigo-500 transform hover:scale-105 transition duration-300"
                            >
                                S'inscrire
                            </button>
                        </div>
                    </form>
                </div>
                 <!-- Lien de connexion -->
                <p class="mt-8 text-center text-sm text-gray-400">
                    Vous avez déjà un compte ?
                    <a href="{{ route('login') }}" class="font-medium text-indigo-400 hover:text-indigo-300 transition duration-300">
                        Connectez-vous ici
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
