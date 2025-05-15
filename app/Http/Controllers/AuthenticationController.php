<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;


class AuthenticationController extends Controller
{
    /**
     * Exibe o formulário de login básico
     */
    public function login_basic()
    {
        $pageConfigs = ['blankPage' => true];
        return view('/content/authentication/auth-login-basic', ['pageConfigs' => $pageConfigs]);
    }

    /**
     * Exibe o formulário de login com capa
     */
    public function login_cover()
    {
        $pageConfigs = ['blankPage' => true];
        return view('/content/authentication/auth-login-cover', ['pageConfigs' => $pageConfigs]);
    }



/**
 * Processa o login via JWT (para aplicação MVC)
 */
public function login(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|max:255',
        'password' => 'required|string|min:8|max:32',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    try {
        $credentials = $request->only('email', 'password');

        // Tentativa de autenticação com JWT
        if (!$token = JWTAuth::attempt($credentials)) {
            dd(2);
            return redirect()->back()
                ->with('error', 'Credenciais inválidas.')
                ->withInput();
        }

        // Autentica o usuário na sessão do Laravel também (opcional)
        Auth::attempt($credentials);

        // Armazena o token JWT na sessão ou cookie
        session(['jwt_token' => $token]);

        return redirect()->route('dashboard-ecommerce')
            ->with('success', 'Login realizado com sucesso!');

    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Erro ao processar login: ' . $e->getMessage())
            ->withInput();
    }
}

    /**
     * Processa o registro de novo usuário
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Erro de validação.'
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);



        return response()->json([
            'success' => true,
            'message' => 'Registro realizado com sucesso!',
            'user' => $user,
            'token' => $token,
            'redirect' => route('.dashboard-ecommerce'),
        ], 201);
    }

    /**
     * Processa o pedido de recuperação de senha
     */
    public function forgot_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Erro de validação.'
            ], 422);
        }

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['success' => true, 'message' => __($status)])
            : response()->json(['success' => false, 'message' => __($status)], 400);
    }

    /**
     * Processa o reset de senha
     */
    public function reset_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Erro de validação.'
            ], 422);
        }

        $status = Password::reset(
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
            ? response()->json(['success' => true, 'message' => __($status), 'redirect' => route('login')])
            : response()->json(['success' => false, 'message' => __($status)], 400);
    }

    /**
     * Processa o logout
     */
    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'success' => true,
                'message' => 'Logout realizado com sucesso',
                'redirect' => route('login.cover')
            ]);

        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao fazer logout'
            ], 500);
        }
    }

    // =============================================
    // Métodos de Visualização (mantidos como antes)
    // =============================================

    public function register_basic()
    {
        $pageConfigs = ['blankPage' => true];
        return view('/content/authentication/auth-register-basic', ['pageConfigs' => $pageConfigs]);
    }

    public function register_cover()
    {
        $pageConfigs = ['blankPage' => true];
        return view('/content/authentication/auth-register-cover', ['pageConfigs' => $pageConfigs]);
    }

    public function forgot_password_basic()
    {
        $pageConfigs = ['blankPage' => true];
        return view('/content/authentication/auth-forgot-password-basic', ['pageConfigs' => $pageConfigs]);
    }

    public function forgot_password_cover()
    {
        $pageConfigs = ['blankPage' => true];
        return view('/content/authentication/auth-forgot-password-cover', ['pageConfigs' => $pageConfigs]);
    }

    public function reset_password_basic()
    {
        $pageConfigs = ['blankPage' => true];
        return view('/content/authentication/auth-reset-password-basic', ['pageConfigs' => $pageConfigs]);
    }

    public function reset_password_cover()
    {
        $pageConfigs = ['blankPage' => true];
        return view('/content/authentication/auth-reset-password-cover', ['pageConfigs' => $pageConfigs]);
    }

    public function verify_email_basic()
    {
        $pageConfigs = ['blankPage' => true];
        return view('/content/authentication/auth-verify-email-basic', ['pageConfigs' => $pageConfigs]);
    }

    public function verify_email_cover()
    {
        $pageConfigs = ['blankPage' => true];
        return view('/content/authentication/auth-verify-email-cover', ['pageConfigs' => $pageConfigs]);
    }

    public function two_steps_basic()
    {
        $pageConfigs = ['blankPage' => true];
        return view('/content/authentication/auth-two-steps-basic', ['pageConfigs' => $pageConfigs]);
    }

    public function two_steps_cover()
    {
        $pageConfigs = ['blankPage' => true];
        return view('/content/authentication/auth-two-steps-cover', ['pageConfigs' => $pageConfigs]);
    }

    public function register_multi_steps()
    {
        $pageConfigs = ['blankPage' => true];
        return view('/content/authentication/auth-register-multisteps', ['pageConfigs' => $pageConfigs]);
    }

}
