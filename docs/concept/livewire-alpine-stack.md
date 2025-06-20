# Livewire + Alpine.js Stack

## Overview

Project ini menggunakan kombinasi **Livewire** dan **Alpine.js** untuk membangun antarmuka yang interaktif dan modern tanpa kompleksitas framework JavaScript yang berat.

## Architecture

### Livewire (Server-Side)
- **Full-stack framework** yang memungkinkan building dynamic interfaces
- **Real-time interactions** dengan minimal JavaScript
- **Component-based architecture** untuk reusability
- **Automatic state management** dan data binding
- **Built-in form validation** dan error handling

### Alpine.js (Client-Side)
- **Minimal JavaScript framework** untuk client-side interactions
- **Declarative syntax** langsung di HTML
- **Reactive data** dan event handling
- **Smooth transitions** dan animations
- **Small footprint** (~10kb gzipped)

## Component Structure

```
app/Livewire/
├── CampaignsIndex.php      # Campaign listing dan management
├── WhatsAppAccountsIndex.php # WhatsApp account management
├── ContactsManager.php     # Contact management
├── AnalyticsDashboard.php  # Analytics dan reporting
└── ChatInterface.php       # Real-time chat interface

resources/views/livewire/
├── campaigns-index.blade.php
├── whatsapp-accounts-index.blade.php
├── contacts-manager.blade.php
├── analytics-dashboard.blade.php
└── chat-interface.blade.php
```

## Key Features Implementation

### 1. Real-time Updates
```php
// Livewire Component
class CampaignsIndex extends Component
{
    use WithPagination;
    
    public $search = '';
    public $statusFilter = '';
    
    public function render()
    {
        return view('livewire.campaigns-index', [
            'campaigns' => $this->getCampaigns()
        ]);
    }
}
```

```html
<!-- Alpine.js untuk UI interactions -->
<div x-data="{ showModal: false }">
    <button @click="showModal = true" class="btn-primary">
        Create Campaign
    </button>
    
    <div x-show="showModal" x-transition class="modal">
        <!-- Modal content -->
    </div>
</div>
```

### 2. Form Handling
```php
// Livewire form dengan validation
public function save()
{
    $this->validate([
        'name' => 'required|min:3',
        'message' => 'required',
    ]);
    
    Campaign::create([
        'name' => $this->name,
        'message' => $this->message,
        'user_id' => auth()->id(),
    ]);
    
    $this->reset();
    session()->flash('success', 'Campaign created!');
}
```

### 3. Search & Filtering
```html
<!-- Real-time search dengan Livewire -->
<input wire:model.live="search" 
       type="search" 
       placeholder="Search campaigns...">

<!-- Alpine.js untuk advanced filtering -->
<div x-data="{ filters: { status: '', date: '' } }">
    <select x-model="filters.status" 
            @change="$wire.set('statusFilter', filters.status)">
        <option value="">All Status</option>
        <option value="draft">Draft</option>
        <option value="scheduled">Scheduled</option>
    </select>
</div>
```

## Best Practices

### 1. Component Organization
- **Single Responsibility**: Setiap component fokus pada satu fungsi
- **Reusable Components**: Buat component yang dapat digunakan ulang
- **Clean Separation**: Pisahkan logic (Livewire) dari presentation (Alpine.js)

### 2. Performance Optimization
- Gunakan `wire:model.lazy` untuk form inputs yang tidak perlu real-time
- Implementasi `wire:loading` untuk UX yang lebih baik
- Optimize dengan `wire:key` untuk list items

### 3. State Management
- Livewire untuk server-side state dan data persistence
- Alpine.js untuk temporary UI state dan interactions
- Session storage untuk data yang perlu persist antar page

## Development Workflow

### 1. Component Development
```bash
# Create new Livewire component
php artisan make:livewire ComponentName

# Test component
php artisan test --filter ComponentNameTest
```

### 2. Asset Compilation
```bash
# Development dengan hot reload
npm run dev

# Production build
npm run build
```

### 3. Integration Testing
- Test Livewire components dengan Laravel testing tools
- Test Alpine.js interactions dengan browser automation
- Test API endpoints yang digunakan component

## Security Considerations

### 1. Livewire Security
- Semua form validation dilakukan di server-side
- CSRF protection otomatis enabled
- Input sanitization untuk XSS prevention

### 2. Alpine.js Security  
- Hindari eval() dan Function() constructor
- Sanitize user input yang ditampilkan
- Validate data sebelum dikirim ke server

## Performance Monitoring

### 1. Livewire Performance
- Monitor component rendering time
- Track AJAX request frequency
- Optimize query efficiency

### 2. Client-side Performance
- Monitor JavaScript bundle size
- Track Alpine.js initialization time
- Optimize DOM manipulations

## Integration dengan WhatsApp Features

### 1. Real-time Chat
```php
// Livewire component untuk chat
class ChatInterface extends Component
{
    protected $listeners = ['messageReceived' => 'addMessage'];
    
    public function addMessage($message)
    {
        // Handle new message
        $this->messages[] = $message;
    }
}
```

### 2. Campaign Management
```javascript
// Alpine.js untuk campaign builder
Alpine.data('campaignBuilder', () => ({
    campaign: {
        name: '',
        message: '',
        schedule: null
    },
    
    saveCampaign() {
        this.$wire.call('saveCampaign', this.campaign);
    }
}));
```

## Migration dari Filament

Jika ada migration dari Filament, ikuti langkah berikut:

1. **Remove Filament dependencies** dari composer.json
2. **Convert Filament Resources** ke Livewire Components
3. **Migrate form schemas** ke Livewire form handling
4. **Convert table configurations** ke Livewire table components
5. **Update routing** dari Filament ke standard Laravel routes

## Resources

- [Livewire Documentation](https://laravel-livewire.com/docs)
- [Alpine.js Documentation](https://alpinejs.dev/)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Laravel Documentation](https://laravel.com/docs)
