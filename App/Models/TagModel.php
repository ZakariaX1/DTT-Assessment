<?php

namespace App\Models;

use PDO;

class TagModel extends BaseModel {
    // None of the Base model functions are implemented as the TagModel is not used in the project

    public function create($params) {
        return;
    }

    public function getById($id) {
        return;
    }

    public function getAll() {
        return;
    }

    public function update($id, $params) {
        return;
    }

    public function delete($id) {
        return;
    }

    public function search($params) {
        return;
    }

    /**
     * Function used to process the tags and add them to the database
     * @param array $tags The tags to process
     * @param int $facilityId The ID of the facility to associate the tags with
     * @return int The number of tags processed
     */
    public function processTags($tags, $facilityId): int {
        if (empty($tags)) {
            return 0;
        }
        $sql = "DELETE FROM facility_has_tag WHERE facility_id = ?";
        $this->db->executeQuery($sql, [$facilityId]);
        foreach ($tags as $tag) {
            $tag = trim($tag);
            $sql = "SELECT id FROM tag WHERE name = ?";
            $this->db->executeQuery($sql, [$tag]);

            $tagId = $this->db->getStatement()->fetch(PDO::FETCH_ASSOC)['id'] ?? null;
            if (!$tagId) {
                $sql = "INSERT INTO tag (name) VALUES (?)";
                $this->db->executeQuery($sql, [$tag]);
                $tagId = $this->db->getLastInsertedId();
            }

            $sql = "INSERT INTO facility_has_tag (facility_id, tag_id) VALUES (?, ?)";
            $this->db->executeQuery($sql, [$facilityId, $tagId]);
        }
        return $this->db->getStatement()->rowCount();
    }
}