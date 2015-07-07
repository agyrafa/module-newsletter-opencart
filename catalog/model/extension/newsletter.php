<?php
class ModelExtensionNewsletter extends Model {

    public function addNewsletter($data) {
        $this->event->trigger('pre.admin.newsletter.edit');

        $query = $this->db->query('
                                    INSERT INTO '  . DB_PREFIX . 'newsletter (
                                        email,
                                        date_add
                                    )
                                    VALUES(
                                        '."'".$this->db->escape($data["email"])."'".',
                                        '."'".$this->db->escape(date("Y-m-d H:i:s"))."'".'
                                    )');

        $this->event->trigger('post.admin.newsletter.edit', $query);
    }

}