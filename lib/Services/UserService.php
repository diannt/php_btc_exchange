<?php

namespace lib\Services;

use lib\Logging\Logger;

class UserService
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger();
    }

    public function createUser(array $userData)
    {
        try {
            if (!$this->validateUserData($userData)) {
                throw new \Exception('Invalid user data provided');
            }

            $user = new \User();
            $user->setEmail($userData['email'] ?? '');
            $user->setUsername($userData['username'] ?? '');
            $user->setPassword(password_hash($userData['password'] ?? '', PASSWORD_BCRYPT));

            $user->insert();

            $this->logger->info("User created successfully", [
                'user_id' => $user->getId(),
                'email' => $userData['email'],
            ]);

            return ['id' => $user->getId(), 'email' => $userData['email']];
        } catch (\Exception $e) {
            $this->logger->error("User creation failed: " . $e->getMessage());
            return false;
        }
    }

    public function getUserById(int $userId)
    {
        try {
            $user = new \User();
            if ($user->findById($userId)) {
                return $user;
            }
            return null;
        } catch (\Exception $e) {
            $this->logger->error("Error fetching user: " . $e->getMessage());
            return null;
        }
    }

    public function updateUser(int $userId, array $updateData): bool
    {
        try {
            $user = new \User();
            if (!$user->findById($userId)) {
                throw new \Exception("User not found");
            }

            $allowedFields = ['email', 'username', 'phone'];
            foreach ($allowedFields as $field) {
                if (isset($updateData[$field])) {
                    $setter = 'set' . ucfirst($field);
                    if (method_exists($user, $setter)) {
                        $user->$setter($updateData[$field]);
                    }
                }
            }

            $user->save();

            $this->logger->info("User updated", ['user_id' => $userId]);
            return true;
        } catch (\Exception $e) {
            $this->logger->error("User update failed: " . $e->getMessage());
            return false;
        }
    }

    private function validateUserData(array $userData): bool
    {
        $required = ['email', 'username', 'password'];
        foreach ($required as $field) {
            if (empty($userData[$field])) {
                return false;
            }
        }

        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;
    }
}
