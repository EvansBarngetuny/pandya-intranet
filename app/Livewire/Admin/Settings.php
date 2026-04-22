<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class Settings extends Component
{
    use WithFileUploads;

    // General Settings
    public $hospital_name = 'Pandya Memorial Hospital';
    public $hospital_tagline = 'Excellence in Patient Care';
    public $hospital_logo;
    public $hospital_phone = '+254734600663';
    public $hospital_email = 'care@pandyahospital.org';
    public $hospital_address = 'Mombasa, Kenya';
    
    // System Settings
    public $system_timezone = 'Africa/Nairobi';
    public $date_format = 'Y-m-d';
    public $time_format = 'H:i';
    public $items_per_page = 15;
    
    // Security Settings
    public $password_expiry_days = 90;
    public $session_timeout_minutes = 30;
    public $max_login_attempts = 5;
    public $require_mfa = false;
    
    // Notification Settings
    public $email_notifications = true;
    public $push_notifications = true;
    public $daily_digest = true;
    public $weekly_report = true;
    
    // Admin Settings
    public $currentPassword;
    public $newPassword;
    public $newPasswordConfirmation;
    
    // Backup Settings
    public $auto_backup = true;
    public $backup_frequency = 'daily';
    public $backup_retention_days = 30;
    
    public $activeTab = 'general';
    public $showPasswordForm = false;
    public $isSaving = false;

    protected $rules = [
        'hospital_name' => 'required|string|max:255',
        'hospital_tagline' => 'nullable|string|max:255',
        'hospital_logo' => 'nullable|image|max:2048',
        'hospital_phone' => 'nullable|string|max:20',
        'hospital_email' => 'nullable|email|max:255',
        'hospital_address' => 'nullable|string|max:500',
        'system_timezone' => 'required|string',
        'items_per_page' => 'required|integer|min:5|max:100',
        'password_expiry_days' => 'required|integer|min:0|max:365',
        'session_timeout_minutes' => 'required|integer|min:5|max:480',
        'max_login_attempts' => 'required|integer|min:1|max:10',
    ];

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        // Load from config or database
        $this->hospital_name = config('app.name', 'Pandya Memorial Hospital');
        $this->system_timezone = config('app.timezone', 'Africa/Nairobi');
        
        // Load from cache or use defaults
        $this->items_per_page = Cache::get('settings.items_per_page', 15);
        $this->email_notifications = Cache::get('settings.email_notifications', true);
        $this->push_notifications = Cache::get('settings.push_notifications', true);
        $this->daily_digest = Cache::get('settings.daily_digest', true);
        $this->weekly_report = Cache::get('settings.weekly_report', true);
    }

    public function saveGeneralSettings()
    {
        $this->validate([
            'hospital_name' => 'required|string|max:255',
            'hospital_tagline' => 'nullable|string|max:255',
            'hospital_phone' => 'nullable|string|max:20',
            'hospital_email' => 'nullable|email|max:255',
            'hospital_address' => 'nullable|string|max:500',
        ]);

        // Save logo if uploaded
        if ($this->hospital_logo) {
            $logoPath = $this->hospital_logo->store('settings', 'public');
            Cache::put('settings.hospital_logo', $logoPath, 86400 * 365);
        }

        // Save to cache/database
        Cache::put('settings.hospital_name', $this->hospital_name, 86400 * 365);
        Cache::put('settings.hospital_tagline', $this->hospital_tagline, 86400 * 365);
        Cache::put('settings.hospital_phone', $this->hospital_phone, 86400 * 365);
        Cache::put('settings.hospital_email', $this->hospital_email, 86400 * 365);
        Cache::put('settings.hospital_address', $this->hospital_address, 86400 * 365);

        // Update config (for current session)
        config(['app.name' => $this->hospital_name]);

        session()->flash('message', 'General settings saved successfully!');
        $this->dispatch('settings-saved');
    }

    public function saveSystemSettings()
    {
        $this->validate([
            'system_timezone' => 'required|string',
            'items_per_page' => 'required|integer|min:5|max:100',
        ]);

        Cache::put('settings.items_per_page', $this->items_per_page, 86400 * 365);
        
        // Update timezone in config (would need to update .env for persistence)
        config(['app.timezone' => $this->system_timezone]);
        
        session()->flash('message', 'System settings saved successfully!');
    }

    public function saveSecuritySettings()
    {
        $this->validate([
            'password_expiry_days' => 'required|integer|min:0|max:365',
            'session_timeout_minutes' => 'required|integer|min:5|max:480',
            'max_login_attempts' => 'required|integer|min:1|max:10',
        ]);

        Cache::put('settings.password_expiry_days', $this->password_expiry_days, 86400 * 365);
        Cache::put('settings.session_timeout_minutes', $this->session_timeout_minutes, 86400 * 365);
        Cache::put('settings.max_login_attempts', $this->max_login_attempts, 86400 * 365);
        Cache::put('settings.require_mfa', $this->require_mfa, 86400 * 365);

        session()->flash('message', 'Security settings saved successfully!');
    }

    public function saveNotificationSettings()
    {
        Cache::put('settings.email_notifications', $this->email_notifications, 86400 * 365);
        Cache::put('settings.push_notifications', $this->push_notifications, 86400 * 365);
        Cache::put('settings.daily_digest', $this->daily_digest, 86400 * 365);
        Cache::put('settings.weekly_report', $this->weekly_report, 86400 * 365);

        session()->flash('message', 'Notification settings saved successfully!');
    }

    public function saveBackupSettings()
    {
        Cache::put('settings.auto_backup', $this->auto_backup, 86400 * 365);
        Cache::put('settings.backup_frequency', $this->backup_frequency, 86400 * 365);
        Cache::put('settings.backup_retention_days', $this->backup_retention_days, 86400 * 365);

        session()->flash('message', 'Backup settings saved successfully!');
    }

    public function changePassword()
    {
        $this->validate([
            'currentPassword' => 'required|string',
            'newPassword' => 'required|string|min:8|confirmed',
            'newPasswordConfirmation' => 'required|string',
        ]);

        $user = auth()->user();

        if (!Hash::check($this->currentPassword, $user->password)) {
            session()->flash('error', 'Current password is incorrect.');
            return;
        }

        $user->update([
            'password' => Hash::make($this->newPassword)
        ]);

        $this->reset(['currentPassword', 'newPassword', 'newPasswordConfirmation', 'showPasswordForm']);
        session()->flash('message', 'Password changed successfully!');
    }

    public function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        
        session()->flash('message', 'System cache cleared successfully!');
    }

    public function runBackup()
    {
        // Implement backup logic here
        session()->flash('message', 'Backup initiated successfully!');
    }

    public function getLogoUrlProperty()
    {
        $logoPath = Cache::get('settings.hospital_logo');
        if ($logoPath && file_exists(storage_path('app/public/' . $logoPath))) {
            return asset('storage/' . $logoPath);
        }
        return null;
    }

    public function render()
    {
        $timezones = [
            'Africa/Nairobi' => 'Africa/Nairobi (EAT)',
            'UTC' => 'UTC',
            'Africa/Johannesburg' => 'Africa/Johannesburg (SAST)',
            'Africa/Cairo' => 'Africa/Cairo (EET)',
            'Europe/London' => 'Europe/London (GMT)',
        ];

        return view('livewire.admin.settings', [
            'timezones' => $timezones,
            'logoUrl' => $this->logoUrl,
        ])->layout('layouts.app');
    }
}