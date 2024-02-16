<?php

namespace App\Models;

use PDO;
use App\Models\TagModel;

class FacilityModel extends BaseModel{
    private $tagsModel;

    public function __construct () {
        parent::__construct();
        $this->tagsModel = new TagModel();
    }

    /**
     * Function used to create a new facility in the database
     * @param array $params The parameters to use when creating the facility, contains: name, creation_date, location_id and array of tags (empty array if no tags are present)
     * @return int The ID of the newly created facility
     */
    public function create($params): int | \Exception{
        $this->db->beginTransaction();
        try {
        $sql = "INSERT INTO facility (name, creation_date, location_id) VALUES (?, ?, ?)";
        $stmt = $this->db->executeQuery($sql, [$params['name'], $params['creation_date'], $params['location_id']]);

        $facilityId = $this->db->getLastInsertedId();
        $this->tagsModel->processTags($params['tags'], $facilityId);
        
        $this->db->commit();
        return $facilityId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return $e;
        }
    }

    /**
     * Function used to get a facility by its ID
     * @param int $id The ID of the facility to get
     * @return array The facility with the provided ID
     */
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

    /**
     * Function used to get all facilities
     * @return array All facilities in the database
     */
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

    /**
     * Function used to update a facility
     * @param int $id The ID of the facility to update
     * @param array $params The parameters to use when updating the facility, contains: name, creation_date, location_id and array of tags (empty array if no tags are present)
     * @return int The amount of rows affected by the update
     */
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

            $result += $this->tagsModel->processTags($params['tags'], $id);

            $this->db->commit();
            return $result;
        } catch (\Throwable $th) {
            $this->db->rollBack();
            return $th;
        }
    }

    /**
     * Function used to delete a facility
     * @param int $id The ID of the facility to delete
     * @return int The amount of rows affected by the delete
     */
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

    /**
     * Function used to search for facilities
     * @param array $params The parameters to use when searching for facilities, contains: facilityName, tagNames and city
     * @return array The facilities that match the search parameters
     */
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
}