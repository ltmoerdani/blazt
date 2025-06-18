<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Contact\Services\ContactService;
use App\Domain\Contact\Services\ImportService;
use App\Domain\Contact\Models\Contact;
use App\Domain\Contact\Models\ContactGroup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class ContactController extends Controller
{
    private const VALIDATION_GROUP_ID = 'nullable|exists:contact_groups,id,user_id,';

    protected $contactService;
    protected $importService;

    public function __construct(ContactService $contactService, ImportService $importService)
    {
        $this->contactService = $contactService;
        $this->importService = $importService;
    }

    public function index()
    {
        $user = Auth::user();
        /** @var \App\Domain\User\Models\User $user */
        $contacts = $user->contacts()->paginate(10);
        return view('dashboard.contacts.index', compact('contacts'));
    }

    public function create()
    {
        $user = Auth::user();
        /** @var \App\Domain\User\Models\User $user */
        $contactGroups = $user->contactGroups()->get();
        return view('dashboard.contacts.create', compact('contactGroups'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        /** @var \App\Domain\User\Models\User $user */

        $request->validate([
            'phone_number' => 'required|string|max:20|unique:contacts,phone_number,NULL,id,user_id,' . $user->id,
            'name' => 'nullable|string|max:255',
            'group_id' => self::VALIDATION_GROUP_ID . $user->id,
            'is_active' => 'boolean',
        ]);

        try {
            $contact = $this->contactService->createContact($user, $request->all());
            return redirect()->route('dashboard.contacts.show', $contact->id)->with('success', 'Contact created successfully.');
        } catch (Exception $e) {
            Log::error('Error creating contact: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to create contact: ' . $e->getMessage()]);
        }
    }

    public function show(Contact $contact)
    {
        if (Auth::id() !== $contact->user_id) {
            abort(403);
        }
        return view('dashboard.contacts.show', compact('contact'));
    }

    public function edit(Contact $contact)
    {
        if (Auth::id() !== $contact->user_id) {
            abort(403);
        }
        $user = Auth::user();
        /** @var \App\Domain\User\Models\User $user */
        $contactGroups = $user->contactGroups()->get();
        return view('dashboard.contacts.edit', compact('contact', 'contactGroups'));
    }

    public function update(Request $request, Contact $contact)
    {
        if (Auth::id() !== $contact->user_id) {
            abort(403);
        }

        $user = Auth::user();
        /** @var \App\Domain\User\Models\User $user */

        $request->validate([
            'phone_number' => 'sometimes|required|string|max:20|unique:contacts,phone_number,' . $contact->id . ',id,user_id,' . $user->id,
            'name' => 'nullable|string|max:255',
            'group_id' => self::VALIDATION_GROUP_ID . $user->id,
            'is_active' => 'boolean',
        ]);

        try {
            $this->contactService->updateContact($contact, $request->all());
            return redirect()->route('dashboard.contacts.show', $contact->id)->with('success', 'Contact updated successfully.');
        } catch (Exception $e) {
            Log::error('Error updating contact: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to update contact: ' . $e->getMessage()]);
        }
    }

    public function destroy(Contact $contact)
    {
        if (Auth::id() !== $contact->user_id) {
            abort(403);
        }

        try {
            $this->contactService->deleteContact($contact);
            return redirect()->route('dashboard.contacts.index')->with('success', 'Contact deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error deleting contact: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete contact: ' . $e->getMessage()]);
        }
    }

    public function indexGroups()
    {
        $user = Auth::user();
        /** @var \App\Domain\User\Models\User $user */
        $groups = $user->contactGroups()->paginate(10);
        return view('dashboard.contacts.groups.index', compact('groups'));
    }

    public function createGroup()
    {
        return view('dashboard.contacts.groups.create');
    }

    public function storeGroup(Request $request)
    {
        $user = Auth::user();
        /** @var \App\Domain\User\Models\User $user */

        $request->validate([
            'name' => 'required|string|max:255|unique:contact_groups,name,NULL,id,user_id,' . $user->id,
            'description' => 'nullable|string',
        ]);

        try {
            $group = $this->contactService->createGroup($user, $request->all());
            return redirect()->route('dashboard.contacts.groups.show', $group->id)->with('success', 'Contact group created successfully.');
        } catch (Exception $e) {
            Log::error('Error creating contact group: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to create contact group: ' . $e->getMessage()]);
        }
    }

    public function showGroup(ContactGroup $group)
    {
        if (Auth::id() !== $group->user_id) {
            abort(403);
        }
        return view('dashboard.contacts.groups.show', compact('group'));
    }

    public function editGroup(ContactGroup $group)
    {
        if (Auth::id() !== $group->user_id) {
            abort(403);
        }
        return view('dashboard.contacts.groups.edit', compact('group'));
    }

    public function updateGroup(Request $request, ContactGroup $group)
    {
        if (Auth::id() !== $group->user_id) {
            abort(403);
        }

        $user = Auth::user();
        /** @var \App\Domain\User\Models\User $user */

        $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:contact_groups,name,' . $group->id . ',id,user_id,' . $user->id,
            'description' => 'nullable|string',
        ]);

        try {
            $this->contactService->updateGroup($group, $request->all());
            return redirect()->route('dashboard.contacts.groups.show', $group->id)->with('success', 'Contact group updated successfully.');
        } catch (Exception $e) {
            Log::error('Error updating contact group: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to update contact group: ' . $e->getMessage()]);
        }
    }

    public function destroyGroup(ContactGroup $group)
    {
        if (Auth::id() !== $group->user_id) {
            abort(403);
        }

        try {
            $this->contactService->deleteGroup($group);
            return redirect()->route('dashboard.contacts.groups.index')->with('success', 'Contact group deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error deleting contact group: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete contact group: ' . $e->getMessage()]);
        }
    }

    public function importContacts(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }

        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240', // Max 10MB
            'group_id' => 'nullable|exists:contact_groups,id,user_id,' . $user->id,
        ]);

        try {
            $path = $request->file('file')->getRealPath();
            $rows = array_map('str_getcsv', file($path));
            $header = array_shift($rows);

            $contactsData = [];
            foreach ($rows as $row) {
                if (count($row) === count($header)) {
                    $contactsData[] = array_combine($header, $row);
                }
            }
            
            $importedCount = count($this->importService->importContacts($user, $contactsData, $request->group_id));
            return redirect()->route('dashboard.contacts.index')->with('success', $importedCount . ' contacts imported successfully.');
        } catch (Exception $e) {
            Log::error('Error importing contacts: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to import contacts: ' . $e->getMessage()]);
        }
    }
}

