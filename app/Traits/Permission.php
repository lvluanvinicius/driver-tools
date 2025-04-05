<?php
namespace App\Traits;

use App\Models\User;

trait Permission
{
    /**
     * Checa se uma permissão solicitada está contida nas permissões de usuários.
     * @author Luan Santos <lvluansantos@gmail.com>
     *
     * @param User $user
     * @param string $permission
     * @return void
     */
    public function checkPermission(User $user, string $permission): void
    {
        ! in_array($permission, $this->loadPermissions($user)) && abort(401, "Acesso não autorizado.");
    }

    /**
     * Recupera as permissões nas configurações.
     * @author Luan Santos <lvluansantos@gmail.com>
     *
     * @return void
     */
    public function getPermissions(): array | null
    {
        return config('app-permissions') ? config('app-permissions') : null;
    }

    /**
     * Retorna as permissões de usuário.
     * @author Luan Santos <lvluansantos@gmail.com>
     *
     * @param User $user
     * @return array|null
     */
    public function loadPermissions(User $user): array | null
    {
        return $user->permissions ? json_decode($user->permissions) : null;
    }

    /**
     * Atualiza as permissões de um usuário.
     * @author Luan Santos <lvluansantos@gmail.com>
     *
     * @param User $user
     * @param array $permissions
     * @return boolw
     */
    public function setPermissions(User $user, array $permissions): bool
    {
        return $user->update(['permissions' => json_encode($permissions)]);
    }
}
