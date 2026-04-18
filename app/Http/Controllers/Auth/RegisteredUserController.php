<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Rules\StrongPassword;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(Request $request): Response
    {
        // Generate math CAPTCHA
        $ops = ['+', '-', '*'];
        $op = $ops[array_rand($ops)];
        $n1 = random_int(1, 20);
        $n2 = random_int(1, 20);
        $correct = match($op) {
            '+' => $n1 + $n2,
            '-' => $n1 - $n2,
            '*' => $n1 * $n2,
        };
        $question = "$n1 $op $n2";
        $request->session()->put([
            'captcha_answer' => $correct,
            'captcha_question' => $question,
        ]);

        return Inertia::render('auth/Register', [
            'captcha_question' => $question,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', new StrongPassword()],
            'captcha_answer' => ['required', 'numeric', new \App\Rules\MathCaptcha()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        $request->session()->forget('captcha_answer');

        return to_route('dashboard');
    }
}
