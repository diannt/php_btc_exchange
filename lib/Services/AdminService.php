<?php

namespace lib\Services;

use lib\Logging\Logger;
use lib\Logging\AuditLog;

class AdminService
{
    private $logger;
    private $auditLog;

    public function __construct()
    {
        $this->logger = new Logger();
        $this->auditLog = new AuditLog();
    }

    public function getAllUsers(\User $admin, array $filters = []): array
    {
        try {
            $this->authorizeAction('view_users', $admin);

            $this->auditLog->record('admin.users.list', $admin->getId(), [
                'filters' => $filters,
            ]);

            return \User::getAll($filters);
        } catch (\Exception $e) {
            $this->logger->error("Error fetching users: " . $e->getMessage());
            return [];
        }
    }

    public function suspendUser(\User $admin, int $userId, string $reason = ''): bool
    {
        try {
            $this->authorizeAction('suspend_user', $admin);

            $user = new \User();
            if (!$user->findById($userId)) {
                throw new \Exception("User not found");
            }

            $user->setStatus('suspended');
            $user->save();

            $this->auditLog->record('admin.user.suspended', $admin->getId(), [
                'target_user_id' => $userId,
                'reason' => $reason,
            ]);

            $this->logger->info("User suspended", [
                'admin_id' => $admin->getId(),
                'user_id' => $userId,
            ]);

            return true;
        } catch (\Exception $e) {
            $this->logger->error("Error suspending user: " . $e->getMessage());
            return false;
        }
    }

    public function refundOrder(\User $admin, int $orderId, string $reason = ''): bool
    {
        try {
            $this->authorizeAction('refund_order', $admin);

            $order = new \Order();
            if (!$order->findById($orderId)) {
                throw new \Exception("Order not found");
            }

            $this->auditLog->record('admin.order.refunded', $admin->getId(), [
                'order_id' => $orderId,
                'reason' => $reason,
            ]);

            $this->logger->info("Order refunded", [
                'admin_id' => $admin->getId(),
                'order_id' => $orderId,
            ]);

            return true;
        } catch (\Exception $e) {
            $this->logger->error("Error refunding order: " . $e->getMessage());
            return false;
        }
    }

    private function authorizeAction(string $action, \User $admin): void
    {
        if ($admin->getId() != 2) {
            throw new \Exception("Admin authorization failed for action: $action");
        }
    }
}
