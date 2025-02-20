<?php

namespace App\Services;

use App\Exceptions\ErrorJsonException;
use App\Mail\UserRegistered;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UserService
{
    /**
     * @throws ErrorJsonException
     */
    public function create($name, $email, $password)
    {
        $user = new User();
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->name = $name;
        $user->save();
        Mail::to($user->email)->queue(new UserRegistered($user));
        return $user;
    }

    public function get($limit = 100, $page = 1, $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = User::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, 'LIKE', '%' . $value . '%');
            }
        }

        return $query->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * @throws ErrorJsonException
     */
    public function getById($id)
    {
        $currentUser = Auth::user();

        if (!$this->isAuthorizeToAccess($currentUser, $id)) {
            throw new ErrorJsonException('Unauthorized to to access this user', 403);
        }
        return User::findOrFail($id);
    }

    /**
     * @throws ErrorJsonException
     */
    public function update($id, array $data)
    {
        $currentUser = Auth::user();

        if (!$this->isAuthorizeToAccess($currentUser, $id)) {
            throw new ErrorJsonException('unauthorized to update this user', 403);
        }

        $user = User::findOrFail($id);

        if (isset($data['role'])) {
            if ($currentUser->isAdmin()) {
                $user->role = $data['role'];
                $user->save();
            } else {
                throw new ErrorJsonException('unauthorized to update own role', 403);
            }
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        return $user;
    }

    /**
     * @throws ErrorJsonException
     */
    public function delete($id): void
    {
        $currentUser = Auth::user();

        if (!$this->isAuthorizeToAccess($currentUser, $id)) {
            throw new ErrorJsonException('Unauthorized to delete this user', 403);
        }

        $user = User::findOrFail($id);
        $user->delete();
    }

    private function isAuthorizeToAccess($currentUser, $id): bool
    {
        return $currentUser->isAdmin() || $this->isCurrentUser($currentUser, $id);
    }

    private function isCurrentUser($currentUser, int $id): bool
    {
        return $currentUser->id === $id;
    }

    public function getMe()
    {
        return Auth::user();
    }
}
