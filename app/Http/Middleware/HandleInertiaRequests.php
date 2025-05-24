<?php
namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = null;

        if ($request->session()->has('user')) {
            $user = $request->session()->get('user');
        }

        return [
             ...parent::share($request),
            'auth'  => [
                'user' => $user,
            ],
            'flash' => function () use ($request) {
                return [
                    'success' => fn() => $request->session()->get('success'),
                    'error'   => fn()   => $request->session()->get('error'),
                ];
            },
        ];
    }
}
