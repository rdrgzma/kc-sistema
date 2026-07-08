<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class Login extends Component
{
    /**
     * Authenticate instantly as the specified user.
     *
     * @return RedirectResponse|Redirector|void
     */
    public function loginAs(string $email)
    {
        // Critical safety check: Only allow in local environment
        abort_if(! app()->isLocal(), 403, 'Ação permitida apenas em ambiente local.');

        $user = User::where('email', $email)->first();

        if ($user) {
            Auth::login($user);
            session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        session()->flash('error', "Usuário com o e-mail {$email} não foi encontrado.");
    }

    /**
     * Render the component view.
     *
     * @return View
     */
    public function render()
    {
        return view('livewire.auth.login');
    }
}
