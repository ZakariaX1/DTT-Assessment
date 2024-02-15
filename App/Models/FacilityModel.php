<?php

namespace App\Models;

use PDO;

class FacilityModel extends BaseModel{

    /**
     * Function used to create a new facility in the database
     * @param array $params The parameters to use when creating the facility, contains: name, creation_date, location_id and array of tags (empty array if no tags are present)
     * @return int The ID of the newly created facility
     */
    public function create($params): int | \Exception{
        var_dump($params);
        $this->db->beginTransaction();
        try {
        $sql = "INSERT INTO facility (name, creation_date, location_id) VALUES (?, ?, ?)";
        $stmt = $this->db->executeQuery($sql, [$params['name'], $params['creation_date'], $params['location_id']]);

        $facilityId = $this->db->getLastInsertedId();
        $this->processTags($params['tags'], $facilityId);
        
        $this->db->commit();
        return $facilityId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return $e;
        }
    }

    public function getById($id){
        $sql = "SELECT f.name facility, f.creation_date facility_creation, GROUP_CONCAT(t.name SEPARATOR ', ') tags, 
        l.city, l.zip_code, l.country_code, l.phone_number FROM facility_has_tag ft 
        LEFT JOIN tag t ON t.id=ft.tag_id 
        RIGHT JOIN facility f ON f.id=ft.facility_id 
        LEFT JOIN location l ON f.location_id=l.id 
        WHERE f.id = ? 
        GROUP BY f.id;";
        $this->db->executeQuery($sql, [$id]);
        $result = $this->db->getStatement()->fetch(PDO::FETCH_ASSOC);
        if (empty($result['tags'])) {
            $result['tags'] = [];
        } else {
            $result['tags'] = explode(', ', $result['tags']);
        }
        return $result;
    }

    public function getAll(){
        $sql = "SELECT f.id, f.name facility, f.creation_date facility_creation, GROUP_CONCAT(t.name SEPARATOR ', ') tags, 
        l.city, l.zip_code, l.country_code, l.phone_number FROM facility_has_tag ft 
        LEFT JOIN tag t ON t.id=ft.tag_id 
        RIGHT JOIN facility f ON f.id=ft.facility_id 
        LEFT JOIN location l ON f.location_id=l.id 
        GROUP BY f.id;";
        $this->db->executeQuery($sql);
        $result = $this->db->getStatement()->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $key => $value) {
            if (empty($value['tags'])) {
                $result[$key]['tags'] = [];
                continue;
            }
            $result[$key]['tags'] = explode(', ', $value['tags']);
        }
        return $result;
    }

    public function update($id, $params){
        
        $this->db->beginTransaction();
        // Try to execute the query, if it fails, send an error message
        try {
            $sql = "UPDATE facility SET `name` = COALESCE(?, `name`), `creation_date` = COALESCE(?, `creation_date`), `location_id` = COALESCE(?, `location_id`) WHERE id = ?";
            $executed = $this->db->executeQuery($sql, [$params['facility_name'], $params['creation_date'], $params['location_id'], $id]);
            if (!$executed) {
                $this->db->rollBack();
                return null;
            }

            $result = $this->db->getStatement()->rowCount();

            $sql = "DELETE FROM facility_has_tag WHERE facility_id = ?";
            $this->db->executeQuery($sql, [$id]);

            $this->processTags($params['tags'], $id);

            $this->db->commit();
            return $result;
        } catch (\Throwable $th) {
            $this->db->rollBack();
            return $th;
        }
    }

    public function delete($id){
        // Prepare the query and execute it
        $sql = "DELETE FROM facility WHERE id = ?";
        $executed = $this->db->executeQuery($sql, [$id]);
        if (!$executed) {
            return null;
        }
        $result = $this->db->getStatement()->rowCount();
        return $result;
    }

    public function search($params){
        $sql = "SELECT f.id, f.name facility, f.creation_date facility_creation, GROUP_CONCAT(t.name SEPARATOR ', ') tags, 
        l.city, l.zip_code, l.country_code, l.phone_number FROM facility_has_tag ft 
        LEFT JOIN tag t ON t.id=ft.tag_id 
        RIGHT JOIN facility f ON f.id=ft.facility_id 
        LEFT JOIN location l ON f.location_id=l.id 
        WHERE f.id IN (
        SELECT f.id FROM facility_has_tag ft 
          LEFT JOIN tag t ON t.id=ft.tag_id 
          RIGHT JOIN facility f ON f.id=ft.facility_id 
          WHERE f.name LIKE ? AND
          (t.name LIKE ? OR ? = '') AND
          l.city LIKE ?
        )
        GROUP BY f.id;";
        $this->db->executeQuery($sql, ["%{$params['facilityName']}%", "%{$params['tagNames']}%", $params['tagNames'], "%{$params['city']}%"]);

        $result = $this->db->getStatement()->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $key => $value) {
            if (empty($value['tags'])) {
                $result[$key]['tags'] = [];
                continue;
            }
            $result[$key]['tags'] = explode(', ', $value['tags']);
        }
        return $result;

    }


    // TODO I might want to move this to TagModel for better separation of concerns
    /**
     * Function used to process the tags and add them to the database
     * @param array $tags The tags to process
     * @param int $facilityId The ID of the facility to associate the tags with
     * @return void
     */
    private function processTags($tags, $facilityId) {
        if (empty($tags)) {
            return;
        }
        foreach ($tags as $tag) {
            $tag = trim($tag);
            $sql = "SELECT id FROM tag WHERE name = ?";
            $this->db->executeQuery($sql, [$tag]);

            $tagId = $this->db->getStatement()->fetch(PDO::FETCH_ASSOC)['id'] ?? null;
            if (!$tagId) {
                $sql = "INSERT INTO tag (name) VALUES (?)";
                $stmt = $this->db->executeQuery($sql, [$tag]);
                $tagId = $this->db->getLastInsertId();
            }

            $sql = "INSERT INTO facility_has_tag (facility_id, tag_id) VALUES (?, ?)";
            $stmt = $this->db->executeQuery($sql, [$facilityId, $tagId]);
        }
    }

}