<?php
namespace App\Http\Middleware;

use App\Traits\JsonResponseTrait;
use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticate;
use Illuminate\Http\Request;

class Authenticate extends BaseAuthenticate
{
    use JsonResponseTrait;

    protected function redirectTo(Request $request)
    {
        return $request->expectsJson() ? $this->errorResponse('Você não tem autorização.', 401) : route('sign-in');
    }
}
