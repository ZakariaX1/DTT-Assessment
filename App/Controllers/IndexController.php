<?php

namespace App\Controllers;

use App\Plugins\Http\Response as Status;
use App\Plugins\Http\Exceptions;
use App\Plugins;
use App\Plugins\Di\Factory;
use PDO;
use PDOException;
use PDOStatement;

class IndexController extends BaseController {
    private $db;

    public function __construct() {
        $di = Factory::getDi();
        $this->db = $di->getShared("db");
    }

    /**
     * Controller function used to test whether the project was set up properly.
     * @return void
     */
    public function test() {
        // Respond with 200 (OK):
        (new Status\Ok(['message' => 'Hello world!']))->send();
    }


    // NOTE: I will be using this provided class for the facilities as I don't want to make an unnecessary extra class
    // and I am not sure if I am allowed to do so.
    
    /**
     * Controller function used to create a new facility and its tags and associate them with each other.
     * @return void
     */
    public function create() {
        // Check if the required fields are present
        if (!isset($_POST['name']) || !isset($_POST['locationId']) || !isset($_POST['creationDate'])) {
            (new Status\BadRequest(['message' => 'Missing required field(s). Required fields: name, locationId and creationDate']))->send();
            return;
        }

        // Get the data from the POST request and make the query
        $facilityName = $_POST['name'];
        $facilityLocationId = $_POST['locationId'];
        $facilityCreationDate = $_POST['creationDate'] ?? date('Y-m-d H:i:s');
        // Create an empty array for the later foreach loop to be able to continue, if no tags are present
        $tags = $_POST['tags'] ?? array(); 
        if ($tags) $tags = explode(',', $tags);

        $sql = "INSERT INTO facility(`name`, `location_id`, creation_date) VALUES (?, ?, ?)";

        // Try to execute the query, if it fails, send an error message
        try {
            $executed = $this->db->executeQuery($sql, [$facilityName, $facilityLocationId, $facilityCreationDate]);
            if (!$executed) {
                (new Status\InternalServerError(['message' => 'An error has occured']))->send(); 
                return;
            }
        } catch (\Throwable $th) {
            (new Status\BadRequest(['message' => 'Could not execute query', 'error' => $th]))->send();
            return;
        }

        // Get the ID of the newly created facility and process the tags using the function
        $facilityId = $this->db->getLastInsertedId();
        $this->processTags($tags, $facilityId);

        (new Status\Created(['message' => 'Query executed!']))->send();
    }

    /**
     * Controller function used to get all facilities.
     * @return void
     */
    public function getAll() {
        // Prepare the query and execute it
        $sql = "SELECT f.name facility, f.creation_date facility_creation, GROUP_CONCAT(t.name SEPARATOR ', ') tags, 
        l.city, l.zip_code, l.country_code, l.phone_number FROM facility_has_tag ft 
        LEFT JOIN tag t ON t.id=ft.tag_id 
        RIGHT JOIN facility f ON f.id=ft.facility_id 
        LEFT JOIN location l ON f.location_id=l.id 
        GROUP BY f.id;";
        $executed = $this->db->executeQuery($sql);

        // Check if the database query was executed
        if (!$executed) {
            (new Status\InternalServerError(['message' => "An error has occured"]))->send(); 
            return;
        }

        // Fetch all the results from the query
        $result = $this->db->getStatement()->fetchAll(PDO::FETCH_ASSOC);

        // Check if the database has more than 0 items
        if (count($result) == 0) { 
            (new Status\NoContent())->send(); 
            return;
        }

        // Send an OK message with the content if all checks pass
        (new Status\Ok([
            'message' => "Got item(s)",
            'content' => $result
            ]))->send();
    }

    /**
     * Controller function used to get a facility by its ID.
     * @param int $id The ID of the facility to get
     * @return void
     */
    public function getById($id) {
        $sql = "SELECT f.name facility, f.creation_date facility_creation, GROUP_CONCAT(t.name SEPARATOR ', ') tags, 
        l.city, l.zip_code, l.country_code, l.phone_number FROM facility_has_tag ft 
        LEFT JOIN tag t ON t.id=ft.tag_id 
        RIGHT JOIN facility f ON f.id=ft.facility_id 
        LEFT JOIN location l ON f.location_id=l.id 
        WHERE f.id = ? 
        GROUP BY f.id;";
        $executed = $this->db->executeQuery($sql, [$id]);

        // Check if the database query was executed
        if (!$executed) {
            (new Status\InternalServerError(['message' => "An error has occured"]))->send(); 
            return;
        }

        // Fetch all the results from the query
        $result = $this->db->getStatement()->fetchAll(PDO::FETCH_ASSOC);

        // Check if the database has more than 0 items
        if (count($result) == 0) { 
            (new Status\NotFound(['message' => 'No facility associated with provided ID']))->send(); 
            return;
        }

        // Send an OK message with the content if all checks pass
        (new Status\Ok([
            'message' => "Got item",
            'content' => $result[0]
            ]))->send();
    }

    /**
     * Controller function used to update a facility by its ID.
     * @param int $id The ID of the facility to update
     * @return void
     */
    public function update($id) {
        // Get the data from the POST request and make the query
        $facilityName = $_POST['name'];
        $facilityLocationId = $_POST['locationId'];
        $tags = $_POST['tags'] ?? array(); // Create an empty array for the later foreach loop to be able to continue, if no tags are present
        if ($tags) $tags = explode(',', $tags);

        $sql = "UPDATE facility SET `name` = ?, `location_id` = ? WHERE id = ?";

        // Try to execute the query, if it fails, send an error message
        try {
            $executed = $this->db->executeQuery($sql, [$facilityName, $facilityLocationId, $id]);
            if (!$executed) {
                (new Status\InternalServerError(['message' => 'An error has occured']))->send(); 
                return;
            }
        } catch (\Throwable $th) {
            (new Status\BadRequest(['message' => 'Could not execute query', 'error' => $th]))->send();
            return;
        }

        // First delete all the tags associated with the facility
        $sql = "DELETE FROM facility_has_tag WHERE facility_id = ?";
        $this->db->executeQuery($sql, [$id]);

        // Then add the new tags, if any
        $this->processTags($tags, $id);

        (new Status\Ok(['message' => 'Query executed!']))->send();
    }

    /**
     * Controller function used to delete a facility by its ID.
     * @param int $id The ID of the facility to delete
     * @return void
     */
    public function delete($id) {
        // Prepare the query and execute it
        $sql = "DELETE FROM facility WHERE id = ?";
        $executed = $this->db->executeQuery($sql, [$id]);

        // Check if the database query was executed
        if (!$executed) {
            (new Status\InternalServerError(['message' => "An error has occured"]))->send(); 
            return;
        }

        // Check if the database has more than 0 items
        if ($this->db->getStatement()->rowCount() == 0) { 
            (new Status\NotFound(['message' => 'No facility associated with provided ID']))->send(); 
            return;
        }

        // Send an OK message with the content if all checks pass
        (new Status\Ok([
            'message' => "Deleted item"
            ]))->send();
    }

    /**
     * Controller function used to get all facilities by their facility name, tag names or city.
     * The route is /facility/search?name=...&tag=...&city=...
     * @return void
     */
    public function search() {
        // Get the query parameters, if none was provided, set it to an empty string so that the LIKE query will work
        $facilityName = $_GET['name'] ?? "";
        $tagNames = $_GET['tag'] ?? "";
        $city = $_GET['city'] ?? "";

        $sql = "SELECT f.name facility, f.creation_date facility_creation, GROUP_CONCAT(t.name SEPARATOR ', ') tags, 
        l.city, l.zip_code, l.country_code, l.phone_number FROM facility_has_tag ft 
        LEFT JOIN tag t ON t.id=ft.tag_id 
        RIGHT JOIN facility f ON f.id=ft.facility_id 
        LEFT JOIN location l ON f.location_id=l.id 
        WHERE f.name LIKE ? AND IFNULL(t.name, '') LIKE ? AND l.city LIKE ?
        GROUP BY f.id;";
        $executed = $this->db->executeQuery($sql, ["%$facilityName%", "%$tagNames%", "%$city%"]);

        // Check if the database query was executed
        if (!$executed) {
            (new Status\InternalServerError(['message' => "An error has occured"]))->send(); 
            return;
        }
        
        // Fetch all the results from the query
        $result = $this->db->getStatement()->fetchAll(PDO::FETCH_ASSOC);

        // Check if the database has more than 0 items
        if (count($result) == 0) { 
            (new Status\NotFound(['message' => 'No facility associated with provided search query']))->send(); 
            return;
        }

        // Send an OK message with the content if all checks pass
        (new Status\Ok([
            'message' => "Got item(s)",
            'content' => $result
            ]))->send();
    }

    /**
     * Function used to process the tags and add them to the database
     * @param array $tags The tags to process
     * @param int $facilityId The ID of the facility to associate the tags with
     * @return void
     */
    private function processTags($tags, $facilityId) {
        foreach ($tags as $tag) {
            $tag = trim($tag);
            $sql = "SELECT id FROM tag WHERE name = ?";
            $this->db->executeQuery($sql, [$tag]);
            $tagId = $this->db->getStatement()->fetch(PDO::FETCH_ASSOC)['id'] ?? null;

            if (!$tagId) {
                $sql = "INSERT INTO tag(`name`) VALUES (?)";
                $this->db->executeQuery($sql, [$tag]);
                $tagId = $this->db->getLastInsertedId();
            }

            $sql = "INSERT INTO Facility_has_tag(`facility_id`, `tag_id`) VALUES (?, ?)";
            $this->db->executeQuery($sql, [$facilityId, $tagId]);
        }
    }
}