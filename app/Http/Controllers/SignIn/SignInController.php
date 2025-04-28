<?php
namespace App\Http\Controllers\SignIn;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignIn\SignInRequest as SignInSignInRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class SignInController extends Controller
{
    public function index(): InertiaResponse
    {
        return Inertia::render('SignIn/Index');
    }

    public function store(SignInSignInRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('app.dashboard', absolute: false));

    }

    public function signOut(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->regenerate();
        return redirect()->intended(route('sign-in', absolute: false));
    }
}
