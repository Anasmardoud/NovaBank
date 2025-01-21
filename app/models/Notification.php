<?php
class Notification
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Create a new notification
    public function create($clientId, $message, $type = 'Alert')
    {
        // Validate foreign keys before creating the notification
        $stmt = $this->db->prepare("CALL ValidateForeignKeys(?, NULL, NULL)");
        $stmt->bind_param("i", $clientId);

        try {
            $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            Helper::log("Foreign key validation failed: " . $e->getMessage(), 'ERROR');
            return false;
        }

        // Proceed with creating the notification
        $stmt = $this->db->prepare("INSERT INTO NOTIFICATION (client_id, message, type, status, created_at) VALUES (?, ?, ?, 'Unread', NOW())");
        $stmt->bind_param("iss", $clientId, $message, $type);
        return $stmt->execute();
    }

    // Get all notifications for a client
    public function getByClientId($clientId)
    {
        $stmt = $this->db->prepare("SELECT * FROM NOTIFICATION WHERE client_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get all notifications (for admin)
    public function getAll()
    {
        $result = $this->db->query("SELECT * FROM NOTIFICATION ORDER BY created_at DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Mark a notification as read
    public function markAsRead($notificationId)
    {
        $stmt = $this->db->prepare("UPDATE NOTIFICATION SET status = 'Read' WHERE notification_id = ?");
        $stmt->bind_param("i", $notificationId);
        return $stmt->execute();
    }

    // Delete a notification
    public function delete($notificationId)
    {
        $stmt = $this->db->prepare("DELETE FROM NOTIFICATION WHERE notification_id = ?");
        $stmt->bind_param("i", $notificationId);
        return $stmt->execute();
    }
}
