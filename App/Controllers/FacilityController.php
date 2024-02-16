<?php

namespace App\Controllers;

use App\Plugins\Http\Response as Status;
use App\Models\FacilityModel;

class FacilityController extends BaseController {
    private $facilityModel;

    public function __construct() {
        $this->facilityModel = new FacilityModel();
    }

    /**
     * Controller function used to create a new facility and its tags and associate them with each other.
     * @return void
     */
    public function create() {
        // Check if the required fields are present
        if (!isset($_POST['name']) || !isset($_POST['locationId'])) {
            (new Status\BadRequest(['message' => 'Missing required field(s). Required fields: name and locationId']))->send();
            return;
        }

        // Get the data from the POST request and make the query
        $facilityName = $_POST['name'];
        $facilityLocationId = $_POST['locationId'];
        $facilityCreationDate = $_POST['creationDate'] ?? date('Y-m-d');
        // Create an empty array for the later foreach loop to be able to continue, if no tags are present
        $tags = $_POST['tags'] ?? array(); 
        if ($tags) $tags = explode(',', $tags);

        // Call the create function in the model
        $result = $this->facilityModel->create([
            'name' => $facilityName,
            'creation_date' => $facilityCreationDate,
            'location_id' => $facilityLocationId,
            'tags' => $tags
        ]);

        // Check for any errors
        if ($result instanceof \Exception) {
            (new Status\InternalServerError(['message' => 'An error has occured', 'error' => $result]))->send();
            return;
        }

        // Send a Created message with the ID of the newly created facility
        (new Status\Created(['message' => 'Facility created with ID of ' . $result]))->send();
    }

    /**
     * Controller function used to get all facilities.
     * @return void
     */
    public function getAll() {
        // Call the getAll function in the model
        $result = $this->facilityModel->getAll();

        // Check if the database has more than 0 items
        if (count($result) == 0) { 
            (new Status\NoContent())->send(); 
            return;
        }

        // Send an OK message with the content if all checks pass
        (new Status\Ok([
            'message' => "Got facilities:",
            'content' => $result
            ]))->send();
    }

    /**
     * Controller function used to get a facility by its ID.
     * @param int $id The ID of the facility to get
     * @return void
     */
    public function getById($id) {

        // Call the getById function in the model
        $result = $this->facilityModel->getById($id);

        // Check if the database had the item
        if (!$result) {
            (new Status\NotFound(['message' => 'No facility associated with provided ID']))->send(); 
            return;
        }

        // Send an OK message with the content if all checks pass
        (new Status\Ok([
            'message' => "Got facility:",
            'content' => $result
            ]))->send();
    }

    /**
     * Controller function used to update a facility by its ID.
     * @param int $id The ID of the facility to update
     * @return void
     */
    public function update($id) {
        parse_str(file_get_contents('php://input'), $PUT);
        // Get the data from the POST request and make the query
        $facilityName = $PUT['name'] ?? null;
        $facilityCreationDate = $PUT['creationDate'] ?? null;
        $facilityLocationId = $PUT['locationId'] ?? null;
        $tags = $PUT['tags'] ?? array(); // Create an empty array for the later foreach loop to be able to continue, if no tags are present
        if ($tags) $tags = explode(',', $tags);

        // Call the update function in the model
        $result = $this->facilityModel->update($id, [
            'facility_name' => $facilityName,
            'creation_date' => $facilityCreationDate,
            'location_id' => $facilityLocationId,
            'tags' => $tags
        ]);

        // Check for any errors
        if ($result instanceof \Exception) {
            (new Status\InternalServerError(['message' => 'An error has occured', 'error' => $result]))->send();
            return;
        }

        // Make sure that a row was updated (so not 0 rows)
        if ($result == 0) {
            (new Status\NotFound(['message' => 'No facility associated with provided ID or there was nothing to change']))->send(); 
            return;
        }

        (new Status\Ok(['message' => "Facility {$id} updated"]))->send();
    }

    /**
     * Controller function used to delete a facility by its ID.
     * @param int $id The ID of the facility to delete
     * @return void
     */
    public function delete($id) {
        // Call the delete function in the model
        $result = $this->facilityModel->delete($id);

        // Check if the database has more than 0 items
        if ($result == 0) { 
            (new Status\NotFound(['message' => 'No facility associated with provided ID']))->send(); 
            return;
        }

        // Send an OK message with the content if all checks pass
        (new Status\Ok([
            'message' => "Deleted facility {$id}"
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

        // Call the search function in the model
        $result = $this->facilityModel->search([
            'facilityName' => $facilityName,
            'tagNames' => $tagNames,
            'city' => $city
        ]);

        // Check if the database has more than 0 items
        if (count($result) == 0) { 
            (new Status\NotFound(['message' => 'No facility associated with provided search query']))->send(); 
            return;
        }

        // Send an OK message with the content if all checks pass
        (new Status\Ok([
            'message' => "Got facilities:",
            'content' => $result
            ]))->send();
    }
}