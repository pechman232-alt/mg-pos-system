<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$roles // 🌟 ទទួលយកលេខ Role ច្រើនដែលបងបាញ់មកពី Route
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // ១. ឆែកមើលថាតើគាត់បាន Login ហើយឬនៅ?
        if (!$request->user()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        // ២. ទាញយក Role ID របស់គាត់
        $userRole = (string)$request->user()->role_id; // បម្លែងជា String ដើម្បីប្រៀបធៀបស្រួល

        // ៣. បើគាត់ជា Admin កំពូល (ឧទាហរណ៍ Role 1 ឬ 3) ឱ្យគាត់ចូលប្រើបានគ្រប់កន្លែង (បើបងចង់)
        // តែបើយើងចង់រឹតបន្តឹងតាម Route ម្នាក់ៗ មិនបាច់ដាក់លក្ខខណ្ឌ Admin នេះទេ

        // ៤. ឆែកមើលថាតើ Role របស់គាត់ មាននៅក្នុងបញ្ជី Role ដែល Route ទាមទារឬអត់?
        if (in_array($userRole, $roles)) {
            return $next($request); // 🌟 ត្រូវ! អនុញ្ញាតឱ្យឆ្លងកាត់ទៅ Controller
        }

        // ៥. បើខុសសិទ្ធិ បិទផ្លូវភ្លាមៗ
        return response()->json([
            'success' => false,
            'message' => 'Access Denied: លោកអ្នកមិនមានសិទ្ធិប្រើប្រាស់មុខងារនេះទេ!'
        ], 403);
    }
}