<?php

namespace App\Http\Controllers\API;

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
    private const MESSAGE_UNAUTHENTICATED = 'Unauthenticated.';
    private const MESSAGE_UNAUTHORIZED = 'Unauthorized.';
    private const MESSAGE_GROUP_NOT_FOUND = 'Contact group not found or unauthorized.';
    private const MESSAGE_CREATE_FAILED = 'Failed to create contact.';
    private const MESSAGE_UPDATE_FAILED = 'Failed to update contact.';
    private const MESSAGE_DELETE_FAILED = 'Failed to delete contact.';
    private const MESSAGE_GROUP_CREATE_FAILED = 'Failed to create contact group.';
    private const MESSAGE_GROUP_UPDATE_FAILED = 'Failed to update contact group.';
    private const MESSAGE_GROUP_DELETE_FAILED = 'Failed to delete contact group.';
    private const VALIDATION_GROUP_ID = 'nullable|exists:contact_groups,id';

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
        if (!$user) {
            return response()->json(['message' => self::MESSAGE_UNAUTHENTICATED], 401);
        }
        /** @var \App\Domain\User\Models\User $user */

        $contacts = $user->contacts()->paginate(10);
        return response()->json($contacts, 200);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        /** @var \App\Domain\User\Models\User $user */

        $request->validate([
            'phone_number' => 'required|string|max:20',
            'name' => 'nullable|string|max:255',
            'group_id' => self::VALIDATION_GROUP_ID,
            'is_active' => 'boolean',
        ]);

        $response = null;

        try {
            if ($request->filled('group_id') && !$user->contactGroups()->find($request->group_id)) {
                $response = response()->json(['message' => self::MESSAGE_GROUP_NOT_FOUND], 404);
            } else {
                $contact = $this->contactService->createContact($user, $request->all());
                $response = response()->json(['message' => 'Contact created successfully.', 'data' => $contact], 201);
            }
        } catch (Exception $e) {
            Log::error('Error creating contact: ' . $e->getMessage());
            $response = response()->json(['message' => self::MESSAGE_CREATE_FAILED, 'error' => $e->getMessage()], 500);
        }

        return $response;
    }

    public function show(Contact $contact)
    {
        if (Auth::id() !== $contact->user_id) {
            return response()->json(['message' => self::MESSAGE_UNAUTHORIZED], 403);
        }
        return response()->json($contact, 200);
    }

    public function update(Request $request, Contact $contact)
    {
        // Authorization should be handled by a policy or middleware
        if (Auth::id() !== $contact->user_id) {
            return response()->json(['message' => self::MESSAGE_UNAUTHORIZED], 403);
        }

        $request->validate([
            'phone_number' => 'sometimes|required|string|max:20',
            'name' => 'nullable|string|max:255',
            'group_id' => self::VALIDATION_GROUP_ID,
            'is_active' => 'boolean',
        ]);

        $response = null;

        try {
            if ($request->filled('group_id') && (!($group = ContactGroup::find($request->group_id)) || $group->user_id !== Auth::id())) {
                $response = response()->json(['message' => self::MESSAGE_GROUP_NOT_FOUND], 404);
            } else {
                $this->contactService->updateContact($contact, $request->all());
                $response = response()->json(['message' => 'Contact updated successfully.', 'data' => $contact], 200);
            }
        } catch (Exception $e) {
            Log::error('Error updating contact: ' . $e->getMessage());
            $response = response()->json(['message' => self::MESSAGE_UPDATE_FAILED, 'error' => $e->getMessage()], 500);
        }

        return $response;
    }

    public function destroy(Contact $contact)
    {
        if (Auth::id() !== $contact->user_id) {
            return response()->json(['message' => self::MESSAGE_UNAUTHORIZED], 403);
        }

        $response = null;

        try {
            $this->contactService->deleteContact($contact);
            $response = response()->json(['message' => 'Contact deleted successfully.'], 204);
        } catch (Exception $e) {
            Log::error('Error deleting contact: ' . $e->getMessage());
            $response = response()->json(['message' => self::MESSAGE_DELETE_FAILED, 'error' => $e->getMessage()], 500);
        }

        return $response;
    }

    public function indexGroups()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => self::MESSAGE_UNAUTHENTICATED], 401);
        }
        /** @var \App\Domain\User\Models\User $user */

        $groups = $user->contactGroups()->paginate(10);
        return response()->json($groups, 200);
    }

    public function storeGroup(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => self::MESSAGE_UNAUTHENTICATED], 401);
        }
        /** @var \App\Domain\User\Models\User $user */

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $response = null;

        try {
            $group = $this->contactService->createGroup($user, $request->all());
            $response = response()->json(['message' => 'Contact group created successfully.', 'data' => $group], 201);
        } catch (Exception $e) {
            Log::error('Error creating contact group: ' . $e->getMessage());
            $response = response()->json(['message' => self::MESSAGE_GROUP_CREATE_FAILED, 'error' => $e->getMessage()], 500);
        }

        return $response;
    }

    public function showGroup(ContactGroup $group)
    {
        if (Auth::id() !== $group->user_id) {
            return response()->json(['message' => self::MESSAGE_UNAUTHORIZED], 403);
        }
        return response()->json($group, 200);
    }

    public function updateGroup(Request $request, ContactGroup $group)
    {
        if (Auth::id() !== $group->user_id) {
            return response()->json(['message' => self::MESSAGE_UNAUTHORIZED], 403);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $response = null;

        try {
            $this->contactService->updateGroup($group, $request->all());
            $response = response()->json(['message' => 'Contact group updated successfully.', 'data' => $group], 200);
        } catch (Exception $e) {
            Log::error('Error updating contact group: ' . $e->getMessage());
            $response = response()->json(['message' => self::MESSAGE_GROUP_UPDATE_FAILED, 'error' => $e->getMessage()], 500);
        }

        return $response;
    }

    public function destroyGroup(ContactGroup $group)
    {
        if (Auth::id() !== $group->user_id) {
            return response()->json(['message' => self::MESSAGE_UNAUTHORIZED], 403);
        }

        $response = null;

        try {
            $this->contactService->deleteGroup($group);
            $response = response()->json(['message' => 'Contact group deleted successfully.'], 204);
        } catch (Exception $e) {
            Log::error('Error deleting contact group: ' . $e->getMessage());
            $response = response()->json(['message' => self::MESSAGE_GROUP_DELETE_FAILED, 'error' => $e->getMessage()], 500);
        }

        return $response;
    }

    public function importContacts(Request $request)
    {
        $user = Auth::user();
        /** @var \App\Domain\User\Models\User $user */

        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240', // Max 10MB
            'group_id' => self::VALIDATION_GROUP_ID,
        ]);

        $response = null;

        try {
            if ($request->filled('group_id') && !$user->contactGroups()->find($request->group_id)) {
                $response = response()->json(['message' => self::MESSAGE_GROUP_NOT_FOUND], 404);
            } else {
                $this->importService->importContacts($user, $request->file('file'), $request->group_id);
                $response = response()->json(['message' => 'Contacts imported successfully.'], 200);
            }
        } catch (Exception $e) {
            Log::error('Error importing contacts: ' . $e->getMessage());
            $response = response()->json(['message' => 'Failed to import contacts.', 'error' => $e->getMessage()], 500);
        }

        return $response;
    }
}
