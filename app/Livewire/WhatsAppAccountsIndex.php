<?php

namespace App\Livewire;

use App\Domain\WhatsApp\Models\WhatsAppAccount;
use Livewire\Component;
use Livewire\WithPagination;

class WhatsAppAccountsIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $showDeleteModal = false;
    public $accountToDelete = null;

    protected $updatesQueryString = ['search', 'sortField', 'sortDirection'];

    public function render()
    {
        $accounts = WhatsAppAccount::query()
            ->where('user_id', auth()->id())
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('phone_number', 'like', '%' . $this->search . '%')
                      ->orWhere('status', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.whatsapp-accounts-index', compact('accounts'));
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function confirmDelete($accountId)
    {
        $this->accountToDelete = $accountId;
        $this->showDeleteModal = true;
    }

    public function deleteAccount()
    {
        if ($this->accountToDelete) {
            $account = WhatsAppAccount::where('user_id', auth()->id())
                ->findOrFail($this->accountToDelete);
            
            $account->delete();
            
            $this->dispatch('notification', [
                'type' => 'success',
                'title' => 'Success!',
                'message' => 'WhatsApp account deleted successfully.'
            ]);
        }

        $this->showDeleteModal = false;
        $this->accountToDelete = null;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
}
