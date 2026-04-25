{{-- resources/views/livewire/events/create-event.blade.php --}}
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-purple-600 to-indigo-600">
                <h1 class="text-2xl font-bold text-white">Create New Event</h1>
                <p class="text-purple-100 text-sm mt-1">Add a training, meeting, or hospital event</p>
            </div>

            <div class="p-6">
                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form wire:submit.prevent="save" class="space-y-6">
                    <!-- Event Title -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Event Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               wire:model="title"
                               class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                               placeholder="Enter event title">
                        @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Event Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Event Type <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="type" class="w-full rounded-lg border-gray-300 focus:border-purple-500">
                            <option value="training">📚 Training</option>
                            <option value="meeting">💼 Meeting</option>
                            <option value="cme">🩺 CME (Continuing Medical Education)</option>
                            <option value="social">🎉 Social Event</option>
                            <option value="other">📌 Other</option>
                        </select>
                        @error('type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Venue -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Venue <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               wire:model="venue"
                               class="w-full rounded-lg border-gray-300 focus:border-purple-500"
                               placeholder="Conference Room, Online, etc.">
                        @error('venue') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Date and Time -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Start Date & Time <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local"
                                   wire:model="start_datetime"
                                   class="w-full rounded-lg border-gray-300 focus:border-purple-500">
                            @error('start_datetime') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                End Date & Time <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local"
                                   wire:model="end_datetime"
                                   class="w-full rounded-lg border-gray-300 focus:border-purple-500">
                            @error('end_datetime') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <textarea wire:model="description" rows="5"
                                  class="w-full rounded-lg border-gray-300 focus:border-purple-500"
                                  placeholder="Describe the event, agenda, speakers, etc."></textarea>
                        @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Target Departments -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Target Departments (Optional)</label>
                        <div class="grid grid-cols-2 gap-2 p-3 border rounded-lg max-h-40 overflow-y-auto">
                            @foreach($departments as $dept)
                                <label class="inline-flex items-center">
                                    <input type="checkbox" value="{{ $dept->name }}"
                                           wire:model="target_departments" class="form-checkbox text-purple-600">
                                    <span class="ml-2 text-sm">{{ $dept->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Leave empty to target all departments</p>
                    </div>

                    <!-- Contact Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contact Person</label>
                            <input type="text"
                                   wire:model="contact_person"
                                   class="w-full rounded-lg border-gray-300"
                                   placeholder="Name of contact person">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contact Phone</label>
                            <input type="text"
                                   wire:model="contact_phone"
                                   class="w-full rounded-lg border-gray-300"
                                   placeholder="Phone number">
                        </div>
                    </div>

                    <!-- Registration Settings -->
                    <div class="border-t pt-4">
                        <div class="mb-3">
                            <label class="inline-flex items-center">
                                <input type="checkbox" wire:model="requires_registration" class="form-checkbox text-purple-600">
                                <span class="ml-2 text-sm text-gray-700">Require registration for this event</span>
                            </label>
                        </div>

                        @if($requires_registration)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Maximum Attendees</label>
                                <input type="number"
                                       wire:model="max_attendees"
                                       class="w-48 rounded-lg border-gray-300"
                                       placeholder="Leave empty for unlimited">
                                <p class="text-xs text-gray-500 mt-1">Maximum number of people who can register</p>
                            </div>
                        @endif
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <a href="{{ route('events.index') }}"
                           class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                            Create Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
