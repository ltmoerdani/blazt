<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Navigation extends Component
{
    public $currentRoute;
    
    public function mount()
    {
        $this->currentRoute = request()->route()->getName();
    }
    
    public function logout()
    {
        Auth::logout();
        
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        
        return redirect('/login');
    }

    public function render()
    {
        return view('livewire.components.navigation');
    }
}
