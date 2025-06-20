<?php

namespace App\Livewire;

use App\Domain\Contact\Models\Contact;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ContactsManager extends Component
{
    use WithPagination, WithFileUploads;

    // Search and filtering
    public $search = '';
    public $statusFilter = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    
    // Contact form
    public $showModal = false;
    public $editingContact = null;
    public $name = '';
    public $phone = '';
    public $email = '';
    public $tags = '';
    public $notes = '';
    
    // Bulk operations
    public $selectedContacts = [];
    public $selectAll = false;
    
    // Import
    public $showImportModal = false;
    public $importFile;
    
    protected $listeners = [
        'contactCreated' => 'refreshContacts',
        'contactUpdated' => 'refreshContacts',
        'contactDeleted' => 'refreshContacts',
    ];

    protected $updatesQueryString = ['search', 'sortField', 'sortDirection', 'statusFilter'];

    public function rules()
    {
        return [
            'name' => 'required|min:2',
            'phone' => [
                'required',
                'regex:/^[0-9+\-\s()]+$/',
                Rule::unique('contacts')->ignore($this->editingContact?->id)->where('user_id', Auth::id())
            ],
            'email' => 'nullable|email',
            'tags' => 'nullable|string',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function mount()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function openModal($contactId = null)
    {
        $this->resetValidation();
        
        if ($contactId) {
            $this->editingContact = Contact::where('user_id', Auth::id())->findOrFail($contactId);
            $this->name = $this->editingContact->name;
            $this->phone = $this->editingContact->phone;
            $this->email = $this->editingContact->email;
            $this->tags = $this->editingContact->tags ? implode(', ', $this->editingContact->tags) : '';
            $this->notes = $this->editingContact->notes;
        } else {
            $this->editingContact = null;
            $this->resetForm();
        }
        
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'tags' => $this->tags ? array_map('trim', explode(',', $this->tags)) : null,
            'notes' => $this->notes,
            'user_id' => Auth::id(),
        ];

        if ($this->editingContact) {
            $this->editingContact->update($data);
            $this->dispatch('contact-updated', name: $this->name);
        } else {
            Contact::create($data);
            $this->dispatch('contact-created', name: $this->name);
        }

        $this->closeModal();
        $this->dispatch('contactSaved');
    }

    public function deleteContact($contactId)
    {
        $contact = Contact::where('user_id', Auth::id())->findOrFail($contactId);
        $contact->delete();
        
        $this->dispatch('contact-deleted', name: $contact->name);
        $this->selectedContacts = array_diff($this->selectedContacts, [$contactId]);
    }

    public function bulkDelete()
    {
        if (!empty($this->selectedContacts)) {
            Contact::whereIn('id', $this->selectedContacts)
                   ->where('user_id', Auth::id())
                   ->delete();
            
            $count = count($this->selectedContacts);
            $this->selectedContacts = [];
            $this->selectAll = false;
            
            $this->dispatch('contacts-bulk-deleted', count: $count);
        }
    }

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedContacts = $this->getContacts()->pluck('id')->toArray();
        } else {
            $this->selectedContacts = [];
        }
    }

    public function openImportModal()
    {
        $this->showImportModal = true;
    }

    public function closeImportModal()
    {
        $this->showImportModal = false;
        $this->importFile = null;
    }

    public function importContacts()
    {
        $this->validate([
            'importFile' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        // Handle CSV import here
        // This would involve parsing the CSV and creating contacts
        
        $this->dispatch('contacts-imported');
        $this->closeImportModal();
    }

    private function resetForm()
    {
        $this->name = '';
        $this->phone = '';
        $this->email = '';
        $this->tags = '';
        $this->notes = '';
    }

    private function getContacts()
    {
        return Contact::where('user_id', Auth::id())
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function refreshContacts()
    {
        // This method can be called to refresh the contact list
        $this->resetPage();
    }

    public function render()
    {
        $contacts = $this->getContacts()->paginate(15);
        
        return view('livewire.contacts-manager', [
            'contacts' => $contacts,
            'totalContacts' => Contact::where('user_id', Auth::id())->count(),
            'activeContacts' => Contact::where('user_id', Auth::id())->where('status', 'active')->count(),
        ]);
    }
}
