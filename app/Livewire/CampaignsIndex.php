<?php

namespace App\Livewire;

use App\Domain\Campaign\Models\Campaign;
use Livewire\Component;
use Livewire\WithPagination;

class CampaignsIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $showDeleteModal = false;
    public $campaignToDelete = null;
    public $statusFilter = '';

    protected $updatesQueryString = ['search', 'sortField', 'sortDirection', 'statusFilter'];

    public function render()
    {
        $campaigns = Campaign::query()
            ->where('user_id', auth()->user()->id)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%')
                      ->orWhere('status', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.campaigns-index', compact('campaigns'));
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

    public function confirmDelete($campaignId)
    {
        $this->campaignToDelete = $campaignId;
        $this->showDeleteModal = true;
    }

    public function deleteCampaign()
    {
        if ($this->campaignToDelete) {
            $campaign = Campaign::where('user_id', auth()->user()->id)
                ->findOrFail($this->campaignToDelete);
            
            $campaign->delete();
            
            $this->dispatch('notification', [
                'type' => 'success',
                'title' => 'Success!',
                'message' => 'Campaign deleted successfully.'
            ]);
        }

        $this->showDeleteModal = false;
        $this->campaignToDelete = null;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }
}
