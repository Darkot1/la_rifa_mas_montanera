<?php

namespace App\Http\Controllers\Users;

use App\Events\ErrorOccurred;
use App\Models\User;
use App\Models\GithubUser;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\EmailHelperGlobal;
use App\Services\DiscordWebhookService;
use App\Events\UserLogin;
use App\Events\UserCreated;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;

class GithubController extends Controller
{
    protected $emailHelper;
    protected $discordWebhookService;

    public function __construct(EmailHelperGlobal $emailHelper, DiscordWebhookService $discordWebhookService)
    {
        $this->emailHelper = $emailHelper;
        $this->discordWebhookService = $discordWebhookService;
    }

    public function login()
    {
        return Socialite::driver('github')->redirect();
    }

    public function callback(Request $request)
    {
        try {
            $user_github = Socialite::driver('github')->user();
            $user = User::where('email', $user_github->email)->first();

            if ($user) {
                Auth::login($user);
                event(new UserLogin($user));

                $this->emailHelper::sendLoginNotification($user);
            } else {
                $user = User::create([
                    'names' => $user_github->name,
                    'email' => $user_github->email,
                ]);

                GithubUser::create([
                    'email' => $user_github->email,
                    'name' => $user_github->name,
                    'user_id' => $user->id,
                ]);

                Auth::login($user);
                $this->emailHelper::sendWelcomeEmail($user);
                event(new UserCreated($user));
            }

            return redirect()->route('raffles.index')->with('success', 'Has iniciado sesión correctamente.');
          
        } catch (\Exception $e) {
            Log::error('Github login error:', ['message' => $e->getMessage()]);
            event(new ErrorOccurred($user));
            return redirect()->route('auth.github')->with('error', 'Error al iniciar sesión con Github.');
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        return redirect()->route('welcome')->with('success', 'Has cerrado sesión correctamente.');
    }
}
