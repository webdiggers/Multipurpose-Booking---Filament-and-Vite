<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if maintenance mode is enabled
        $maintenanceMode = Setting::get('maintenance_mode', false);

        // Allow admin users to bypass maintenance mode
        // Also allow access to login routes and admin panel routes so admins can log in
        if ($maintenanceMode && 
            (!auth()->check() || !auth()->user()->isAdmin()) && 
            !$request->is('admin*') && 
            !$request->is('login') && 
            !$request->is('verify-phone') && 
            !$request->is('livewire/*')) {
            
            $message = Setting::get('maintenance_message', 'We are currently performing maintenance. Please check back soon.');
            
            return response()->view('maintenance', [
                'message' => $message
            ], 503);
        }

        return $next($request);
    }
}
