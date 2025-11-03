<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecureSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Régénérer l'ID de session périodiquement pour éviter la fixation de session
        if (!$request->session()->has('last_regeneration')) {
            $request->session()->put('last_regeneration', now());
            $request->session()->regenerate();
        } else {
            $lastRegeneration = $request->session()->get('last_regeneration');
            if (now()->diffInMinutes($lastRegeneration) > 30) {
                $request->session()->put('last_regeneration', now());
                $request->session()->regenerate();
            }
        }

        // Vérifier l'adresse IP pour détecter les changements suspects
        if (auth()->check()) {
            $currentIp = $request->ip();
            $sessionIp = $request->session()->get('user_ip');

            if (!$sessionIp) {
                $request->session()->put('user_ip', $currentIp);
            } elseif ($sessionIp !== $currentIp) {
                // IP a changé - potentiel hijacking
                \Log::warning('Session IP changed', [
                    'user_id' => auth()->id(),
                    'old_ip' => $sessionIp,
                    'new_ip' => $currentIp,
                ]);
                
                // Optionnel: déconnecter l'utilisateur
                // auth()->logout();
                // return redirect()->route('login')->with('error', 'Session invalide détectée');
                
                // Ou simplement mettre à jour l'IP
                $request->session()->put('user_ip', $currentIp);
            }
        }

        $response = $next($request);

        // Ajouter des en-têtes de sécurité
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        return $response;
    }
}
