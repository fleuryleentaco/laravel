<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Liste des locales supportées
        $supportedLocales = array_keys(config('app.supported_locales', ['fr' => [], 'en' => []]));
        
        // 1. Vérifier si une langue est demandée via query string
        if ($request->has('lang')) {
            $requestedLang = $request->get('lang');
            if (in_array($requestedLang, $supportedLocales)) {
                Session::put('locale', $requestedLang);
                App::setLocale($requestedLang);
                return $next($request);
            }
        }
        
        // 2. Vérifier la session
        if (Session::has('locale')) {
            $sessionLang = Session::get('locale');
            if (in_array($sessionLang, $supportedLocales)) {
                App::setLocale($sessionLang);
                return $next($request);
            }
        }
        
        // 3. Vérifier les préférences utilisateur authentifié
        if (auth()->check() && isset(auth()->user()->locale)) {
            $userLang = auth()->user()->locale;
            if (in_array($userLang, $supportedLocales)) {
                Session::put('locale', $userLang);
                App::setLocale($userLang);
                return $next($request);
            }
        }
        
        // 4. Détecter depuis le navigateur (Accept-Language header)
        $browserLang = $request->getPreferredLanguage($supportedLocales);
        if ($browserLang && in_array($browserLang, $supportedLocales)) {
            Session::put('locale', $browserLang);
            App::setLocale($browserLang);
            return $next($request);
        }
        
        // 5. Utiliser la langue par défaut
        $defaultLang = config('app.locale', 'fr');
        App::setLocale($defaultLang);
        
        return $next($request);
    }
}
