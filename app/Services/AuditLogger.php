<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    /**
     * Record an audit event.
     */
    public static function log(
        string $action,
        ?Model $target = null,
        ?string $targetLabel = null,
        ?array $changes = null,
        ?string $description = null,
        ?User $actor = null,
        ?string $actorType = null
    ): AuditLog {
        $actor = $actor ?: Auth::user();

        if ($actorType === null) {
            if ($actor && $actor->is_admin) {
                $actorType = 'admin';
            } elseif (str_starts_with($action, 'admin.')) {
                $actorType = 'admin';
            } else {
                $actorType = 'user';
            }
        }

        $targetType = $target ? class_basename($target) : null;
        $targetId = $target ? $target->getKey() : null;

        if (!$targetLabel && $target) {
            $targetLabel = $target->name 
                ?? $target->title 
                ?? $target->username 
                ?? (property_exists($target, 'email') ? $target->email : null)
                ?? ("#{$target->getKey()}");
        }

        $ip = null;
        $userAgent = null;
        try {
            $ip = Request::ip();
            $userAgent = Request::userAgent();
        } catch (\Throwable $e) {
            // CLI / queue context safe fallback
        }

        return AuditLog::create([
            'user_id' => $actor?->id,
            'actor_type' => $actorType,
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'target_label' => $targetLabel ? (string) $targetLabel : null,
            'description' => $description,
            'changes' => $changes,
            'ip_address' => $ip,
            'user_agent' => $userAgent ? substr($userAgent, 0, 500) : null,
            'created_at' => now(),
        ]);
    }
}

