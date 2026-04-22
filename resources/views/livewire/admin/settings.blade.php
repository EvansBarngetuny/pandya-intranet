{{-- resources/views/livewire/admin/settings.blade.php --}}
<div class="py-12">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">System Settings</h1>
            <p class="text-gray-600 mt-1">Configure hospital information and system preferences</p>
        </div>

        @if (session()->has('message'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Settings Tabs -->
        <div class="mb-6 border-b border-gray-200">
            <nav class="flex flex-wrap gap-2">
                <button wire:click="$set('activeTab', 'general')" 
                        class="px-4 py-2 rounded-t-lg transition {{ $activeTab === 'general' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    🏥 General
                </button>
                <button wire:click="$set('activeTab', 'system')" 
                        class="px-4 py-2 rounded-t-lg transition {{ $activeTab === 'system' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    ⚙️ System
                </button>
                <button wire:click="$set('activeTab', 'security')" 
                        class="px-4 py-2 rounded-t-lg transition {{ $activeTab === 'security' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    🔒 Security
                </button>
                <button wire:click="$set('activeTab', 'notifications')" 
                        class="px-4 py-2 rounded-t-lg transition {{ $activeTab === 'notifications' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    🔔 Notifications
                </button>
                <button wire:click="$set('activeTab', 'password')" 
                        class="px-4 py-2 rounded-t-lg transition {{ $activeTab === 'password' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    🔐 Password
                </button>
                <button wire:click="$set('activeTab', 'maintenance')" 
                        class="px-4 py-2 rounded-t-lg transition {{ $activeTab === 'maintenance' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    🛠️ Maintenance
                </button>
            </nav>
        </div>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- General Settings -->
            @if($activeTab === 'general')
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">General Settings</h2>
                    <form wire:submit.prevent="saveGeneralSettings" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hospital Name</label>
                            <input type="text" wire:model="hospital_name" class="w-full rounded-lg border-gray-300">
                            @error('hospital_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hospital Tagline</label>
                            <input type="text" wire:model="hospital_tagline" class="w-full rounded-lg border-gray-300">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hospital Logo</label>
                            @if($logoUrl)
                                <div class="mb-2">
                                    <img src="{{ $logoUrl }}" class="h-16 w-auto">
                                </div>
                            @endif
                            <input type="file" wire:model="hospital_logo" accept="image/*">
                            @error('hospital_logo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="text" wire:model="hospital_phone" class="w-full rounded-lg border-gray-300">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" wire:model="hospital_email" class="w-full rounded-lg border-gray-300">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <textarea wire:model="hospital_address" rows="2" class="w-full rounded-lg border-gray-300"></textarea>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                                Save General Settings
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- System Settings -->
            @if($activeTab === 'system')
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">System Settings</h2>
                    <form wire:submit.prevent="saveSystemSettings" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">System Timezone</label>
                            <select wire:model="system_timezone" class="w-full rounded-lg border-gray-300">
                                @foreach($timezones as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Items Per Page</label>
                            <select wire:model="items_per_page" class="w-full rounded-lg border-gray-300">
                                <option value="10">10 items</option>
                                <option value="15">15 items</option>
                                <option value="25">25 items</option>
                                <option value="50">50 items</option>
                                <option value="100">100 items</option>
                            </select>
                            @error('items_per_page') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                                Save System Settings
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- Security Settings -->
            @if($activeTab === 'security')
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Security Settings</h2>
                    <form wire:submit.prevent="saveSecuritySettings" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password Expiry (days)</label>
                            <input type="number" wire:model="password_expiry_days" class="w-full rounded-lg border-gray-300">
                            <p class="text-xs text-gray-500 mt-1">0 = never expires</p>
                            @error('password_expiry_days') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Session Timeout (minutes)</label>
                            <input type="number" wire:model="session_timeout_minutes" class="w-full rounded-lg border-gray-300">
                            @error('session_timeout_minutes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Max Login Attempts</label>
                            <input type="number" wire:model="max_login_attempts" class="w-full rounded-lg border-gray-300">
                            @error('max_login_attempts') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" wire:model="require_mfa" class="form-checkbox">
                                <span class="ml-2 text-sm text-gray-700">Require Multi-Factor Authentication for Admins</span>
                            </label>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                                Save Security Settings
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- Notification Settings -->
            @if($activeTab === 'notifications')
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Notification Settings</h2>
                    <form wire:submit.prevent="saveNotificationSettings" class="space-y-4">
                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" wire:model="email_notifications" class="form-checkbox">
                                <span class="ml-2 text-sm text-gray-700">Enable Email Notifications</span>
                            </label>
                        </div>
                        
                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" wire:model="push_notifications" class="form-checkbox">
                                <span class="ml-2 text-sm text-gray-700">Enable Push Notifications</span>
                            </label>
                        </div>
                        
                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" wire:model="daily_digest" class="form-checkbox">
                                <span class="ml-2 text-sm text-gray-700">Send Daily Digest Email</span>
                            </label>
                        </div>
                        
                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" wire:model="weekly_report" class="form-checkbox">
                                <span class="ml-2 text-sm text-gray-700">Send Weekly Report</span>
                            </label>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                                Save Notification Settings
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- Password Change -->
            @if($activeTab === 'password')
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Change Password</h2>
                    <form wire:submit.prevent="changePassword" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                            <input type="password" wire:model="currentPassword" class="w-full rounded-lg border-gray-300">
                            @error('currentPassword') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                            <input type="password" wire:model="newPassword" class="w-full rounded-lg border-gray-300">
                            @error('newPassword') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                            <input type="password" wire:model="newPasswordConfirmation" class="w-full rounded-lg border-gray-300">
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                                Change Password
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- Maintenance -->
            @if($activeTab === 'maintenance')
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Maintenance</h2>
                    
                    <div class="space-y-6">
                        <!-- Clear Cache -->
                        <div class="border rounded-lg p-4">
                            <h3 class="font-semibold text-gray-800 mb-2">Clear System Cache</h3>
                            <p class="text-sm text-gray-600 mb-3">Clear all cached data including views, routes, and configuration.</p>
                            <button wire:click="clearCache" wire:confirm="Clear all system cache?"
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">
                                Clear Cache
                            </button>
                        </div>
                        
                        <!-- Database Backup -->
                        <div class="border rounded-lg p-4">
                            <h3 class="font-semibold text-gray-800 mb-2">Database Backup</h3>
                            <p class="text-sm text-gray-600 mb-3">Create a manual backup of the database.</p>
                            <button wire:click="runBackup" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                                Run Backup Now
                            </button>
                        </div>
                        
                        <!-- Auto Backup Settings -->
                        <div class="border rounded-lg p-4">
                            <h3 class="font-semibold text-gray-800 mb-2">Auto Backup Settings</h3>
                            <div class="space-y-3">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model="auto_backup" class="form-checkbox">
                                    <span class="ml-2 text-sm text-gray-700">Enable Automatic Backups</span>
                                </label>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Backup Frequency</label>
                                    <select wire:model="backup_frequency" class="rounded-lg border-gray-300">
                                        <option value="daily">Daily</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Retention (days)</label>
                                    <input type="number" wire:model="backup_retention_days" class="w-32 rounded-lg border-gray-300">
                                </div>
                                
                                <button wire:click="saveBackupSettings" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                                    Save Backup Settings
                                </button>
                            </div>
                        </div>
                        
                        <!-- System Info -->
                        <div class="border rounded-lg p-4 bg-gray-50">
                            <h3 class="font-semibold text-gray-800 mb-2">System Information</h3>
                            <div class="space-y-1 text-sm">
                                <p><strong>Laravel Version:</strong> {{ app()->version() }}</p>
                                <p><strong>PHP Version:</strong> {{ phpversion() }}</p>
                                <p><strong>Environment:</strong> {{ app()->environment() }}</p>
                                <p><strong>Server Time:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>