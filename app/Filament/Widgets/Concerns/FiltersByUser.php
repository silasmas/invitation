<?php

namespace App\Filament\Widgets\Concerns;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

trait FiltersByUser
{
    /**
     * Applique le filtre : ne garder que les enregistrements dont l'événement lié appartient
     * à l'utilisateur courant ET a status != 'termine'.
     *
     * @param Builder $query
     * @param string $relationDot chaîne de relations en notation "dot", ex: 'ceremonies.event' ou 'event'
     * @return Builder
     */
    protected function applyUserEventFilter(Builder $query, string $relationDot): Builder
    {
        $user = Auth::user();

        $isSuperAdmin = $user && (method_exists($user, 'hasRole')
            ? $user->hasRole('super_admin')
            : optional($user->role)->name === 'super_admin');

        if ($isSuperAdmin) {
            return $query;
        }

        return $query->whereHas($relationDot, function (Builder $q) use ($user) {
            $q->where('user_id', $user->id)
              ->where('status', '!=', 'termine');
        });
    }
}