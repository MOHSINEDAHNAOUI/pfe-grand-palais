<?php
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Notifications\DeleteAccountNotification;
class AuthController extends Controller
{
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker()->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? response()->json(['message' => __($status)])
                    : response()->json(['message' => __($status)], 400);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? response()->json(['message' => __($status)])
                    : response()->json(['message' => __($status)], 400);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
            'phone'                 => 'nullable|string|max:20',
            'cni'                   => 'nullable|string|max:20',
        ]);
 
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'phone'    => $data['phone'] ?? null,
            'cni'      => $data['cni'] ?? null,
        ]);
 
        $user->assignRole('client');
 
        // Send verification email
        event(new Registered($user));
 
        $token = $user->createToken('auth_token')->plainTextToken;
 
        return response()->json([
            'user'              => $user->load('roles'),
            'token'             => $token,
            'email_verified'    => false,
            'message'           => 'Compte créé ! Vérifiez votre email pour activer votre compte.',
        ], 201);
    }
 
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);
 
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Identifiants incorrects'], 401);
        }
 
        $user = Auth::user();
 
        if (!$user->is_active) {
            return response()->json(['message' => 'Compte désactivé'], 403);
        }
 
        $user->update(['last_login_at' => now()]);
        $token = $user->createToken('auth_token')->plainTextToken;
 
        return response()->json([
            'user'           => $user->load('roles'),
            'token'          => $token,
            'email_verified' => $user->hasVerifiedEmail(),
        ]);
    }
 
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Déconnecté avec succès']);
    }
 
    public function me(Request $request)
    {
        return response()->json([
            'user'           => $request->user()->load('roles'),
            'email_verified' => $request->user()->hasVerifiedEmail(),
        ]);
    }
 
    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);
 
        if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return response()->json(['message' => 'Lien de vérification invalide'], 400);
        }
 
        if ($user->hasVerifiedEmail()) {
            // Redirect to frontend with success
            return redirect(env('FRONTEND_URL', 'http://localhost:5173') . '/email-verified?status=already');
        }
 
        $user->markEmailAsVerified();
 
        return redirect(env('FRONTEND_URL', 'http://localhost:5173') . '/email-verified?status=success');
    }
 
    public function resendVerification(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email déjà vérifié'], 400);
        }
 
        $request->user()->sendEmailVerificationNotification();
 
        return response()->json(['message' => 'Email de vérification renvoyé !']);
    }
 
    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name'    => 'sometimes|string|max:255',
            'email'   => 'sometimes|email|unique:users,email,' . auth()->id(),
            'phone'   => 'nullable|string|max:20',
            'city'    => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'cni'     => 'nullable|string|max:20',
        ]);
 
        $request->user()->update($data);
        return response()->json($request->user()->fresh()->load('roles'));
    }
 
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'      => 'required|string',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);
 
        if (!Hash::check($request->current_password, $request->user()->password)) {
            return response()->json(['message' => 'Mot de passe actuel incorrect'], 422);
        }
 
        $request->user()->update(['password' => Hash::make($request->password)]);
        return response()->json(['message' => 'Mot de passe mis à jour']);
    }
 
    public function uploadAvatar(Request $request)
    {
        $request->validate(['avatar' => 'required|image|max:2048']);
        $user = $request->user();
        if ($user->avatar) Storage::disk('public')->delete($user->avatar);
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);
        return response()->json($user->fresh()->load('roles'));
    }
 
    public function redirectToProvider($provider)
{
    return \Laravel\Socialite\Facades\Socialite::driver($provider)->stateless()->redirect();
}
 
    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = \Laravel\Socialite\Facades\Socialite::driver($provider)->stateless()->user();
            $user = User::firstOrCreate(
                ['email' => $socialUser->getEmail()],
                [
                    'name'              => $socialUser->getName(),
                    'avatar'            => $socialUser->getAvatar(),
                    'provider'          => $provider,
                    'provider_id'       => $socialUser->getId(),
                    'password'          => null,
                    'email_verified_at' => now(), // Social login = email verified
                ]
            );

            // Mettre à jour l'avatar s'il est vide (pour les utilisateurs existants)
            if (empty($user->avatar) && $socialUser->getAvatar()) {
                $user->update(['avatar' => $socialUser->getAvatar()]);
            }

            if (!$user->hasAnyRole(['admin','manager','receptionist','client'])) {
                $user->assignRole('client');
            }
            $token = $user->createToken('auth_token')->plainTextToken;
            return redirect(env('FRONTEND_URL', 'http://localhost:5173') . '/auth/callback?token=' . $token);
        } catch (\Exception $e) {
            Log::error('Social Auth Error: ' . $e->getMessage(), [
                'provider' => $provider,
                'exception' => $e
            ]);
            return redirect(env('FRONTEND_URL', 'http://localhost:5173') . '/login?error=social_auth_failed');
        }
    }

    public function requestDeleteAccount(Request $request)
    {
        $user = $request->user();
        
        $url = URL::temporarySignedRoute(
            'account.confirm-deletion',
            now()->addMinutes(30),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $user->notify(new DeleteAccountNotification($url));

        return response()->json(['message' => 'Un email de confirmation a été envoyé à votre adresse.']);
    }

    public function confirmDeleteAccount(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals(sha1($user->email), $hash)) {
            return redirect(env('FRONTEND_URL', 'http://localhost:5173') . '/login?error=invalid_deletion_link');
        }

        // Action: Anonymiser l'email pour libérer l'original pour une future inscription
        $originalEmail = $user->email;
        $user->update([
            'email' => 'deleted_' . time() . '_' . $originalEmail,
            'is_active' => false
        ]);

        $user->delete();

        return redirect(env('FRONTEND_URL', 'http://localhost:5173') . '/account-deleted');
    }
}