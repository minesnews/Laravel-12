<?php

namespace App\Http\Controllers\Auth;


use App\Mail\Welcome;
use App\Models\User;
use App\Http\Controllers\Controller;
//use App\Http\Controllers\Telegram;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        Mail::to($request->email)->send(new Welcome($user));


        Telegram::sendMessage([
            'chat_id' => env('TELEGRAM_CHAT_ID', ''),
            'parse_mode' => 'Markdown',
            'text' => "*Привет, {$user->name}* \xF0\x9F\x98\x8A \nТвоя регистрация прошла успешно! \n[Ссылка на вашу страницу](https://example.com/dashboard)",
            //'text' => 'Произошло тестовое событие'
        ]);

        return redirect(route('dashboard', absolute: false));
    }
}
