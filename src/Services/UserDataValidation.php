<?php

declare(strict_types=1);

namespace App\Services;

class UserDataValidation
{
    public function isUserRegistrationDataValid(array $data): bool
    {
        if (empty($data['username'])) {
            return false;
        }
        if (empty($data['email'])) {
            return false;
        }
        if (empty($data['password'])) {
            return false;
        }

        return true;
    }

    public function isUserLoginDataValid(array $data): bool
    {
        if (empty($data['email'])) {
            return false;
        }
        if (empty($data['password'])) {
            return false;
        }

        return true;
    }
}
