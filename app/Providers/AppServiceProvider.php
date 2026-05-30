<?php

namespace App\Providers;

use App\Models\Memo;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gate for creating memos (only HOD and Admin)
      Gate::define('create-memo', function (?User $user = null) {
            if (!$user) return false;
            return $user->isHOD() || $user->isAdmin();
        });

        // Gate for editing memos (creator or admin) - Make memo parameter optional
        Gate::define('edit-memo', function (?User $user = null, ?Memo $memo = null) {
            if (!$user) return false;
            if ($user->isAdmin()) return true;
            if ($memo && $user->id === $memo->created_by) return true;
            return false;
        });

        // Gate for publishing memos (only admin)
        Gate::define('publish-memo', function (?User $user = null) {
            return $user && $user->isAdmin();
        });

        // Gate for viewing memos
        Gate::define('view-memo', function (?User $user = null, ?Memo $memo = null) {
            // If no user logged in, deny access
            if (!$user) {
                return false;
            }

            // If no memo provided (like in index listing), allow viewing of list
            if (!$memo) {
                return true;
            }

            // Admin can view all memos
            if ($user->isAdmin()) {
                return true;
            }

            // Only published memos are viewable by non-admins
            if ($memo->status !== 'published') {
                return false;
            }

            // HOD can view memos from their department
            if ($user->isHOD() && $memo->department_id === $user->department_id) {
                return true;
            }

            // Check if user is in the audience
            if ($memo->audience_type === 'all') {
                return true;
            }

            if ($memo->audience_type === 'departments') {
                // Handle both array formats (associative or indexed)
                $departmentIds = [];
                if (is_array($memo->audience_ids)) {
                    foreach ($memo->audience_ids as $audience) {
                        if (is_array($audience) && isset($audience['type']) && $audience['type'] === 'department') {
                            $departmentIds[] = $audience['id'];
                        } elseif (is_numeric($audience)) {
                            $departmentIds[] = $audience;
                        }
                    }
                }
                return in_array($user->department_id, $departmentIds);
            }

            if ($memo->audience_type === 'specific_users') {
                $userIds = [];
                if (is_array($memo->audience_ids)) {
                    foreach ($memo->audience_ids as $audience) {
                        if (is_array($audience) && isset($audience['type']) && $audience['type'] === 'user') {
                            $userIds[] = $audience['id'];
                        } elseif (is_numeric($audience)) {
                            $userIds[] = $audience;
                        }
                    }
                }
                return in_array($user->id, $userIds);
            }

            return false;
        });

        // Gate for deleting memos (only admin)
        Gate::define('delete-memo', function (?User $user = null, ?Memo $memo = null) {
            return $user && $user->isAdmin();
        });

        // Gate for approving memos (only admin)
        Gate::define('approve-memo', function (?User $user = null) {
            return $user && $user->isAdmin();
        });

        // Gate for rejecting memos (only admin)
        Gate::define('reject-memo', function (?User $user = null) {
            return $user && $user->isAdmin();
        });
         // Set UTF-8 for database connection
        DB::statement('SET NAMES utf8mb4');
        
        // Set default string length for older MySQL
        Schema::defaultStringLength(191);
        
        // Set locale for Carbon
        setlocale(LC_TIME, 'en_US.UTF-8');
    }
}
