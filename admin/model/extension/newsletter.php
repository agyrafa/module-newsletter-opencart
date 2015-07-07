<?php
class ModelExtensionNewsletter extends Model {

    public function editEmail($id, $data) {
        $this->event->trigger('pre.admin.newsletter.edit', $data);

        $this->db->query("UPDATE " . DB_PREFIX . "newsletter SET email = '" . $this->db->escape($data['email']) . "' WHERE id = '" . (int)$id . "'");

        if (isset($data['id'])) {
            foreach ($data['id'] as $id) {
                $this->db->query("SELECT * FROM " . DB_PREFIX . "newsletter WHERE id = '" . $this->db->escape($data['id']));
            }
        }

        $this->event->trigger('post.admin.newsletter.edit', $id);
    }

    public function getNewsletter($id) {

        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "newsletter WHERE id = '" . $this->db->escape($id) . "'");

        return $query->row;
    }

    public function deleteEmail($id) {
        $this->event->trigger('pre.admin.newsletter.delete', $id);

        $this->db->query("DELETE FROM " . DB_PREFIX . "newsletter WHERE id = '" . (int)$id . "'");

        $this->event->trigger('post.admin.newsletter.delete', $id);
    }



    public function getTotalNewsletter() {

        $sql = "SELECT id, email, date_add FROM " . DB_PREFIX . "newsletter";

        $query = $this->db->query($sql);
        return $query->rows;
    }
}