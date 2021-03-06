<?php

namespace Controllers;

use PDO;
use Exception;

require_once('Core/BaseController.php');
use Core\BaseController as BaseController;
require_once('Models/Property.php');
use Models\Property as PropertyModel;
require_once('Models/PropertyImages.php');
use Models\PropertyImages as PropertyImages;

require_once('Core/DB/DB.php');
use Core\DB\DB as DB;

class Property extends BaseController {

    public function __construct($params, $secureParams) {
        parent::__construct($params, $secureParams);
        new PropertyModel();
        new PropertyImages();
    }

    public function get() {
        try {
            if (isset($this->params[0], $this->params[1])) {
                switch ($this->params[0]) {
                    case 'all':
                        if($this->params[1] == 'overview') $stmt = DB::execute(PropertyModel::getAll(['_id', 'title', 'price', 'description']));
                        else $stmt = DB::execute(PropertyModel::getAll());
                        break;
                    default:
                        http_response_code(200);
                        die($reject = '{
                                "status": "400",
                                "message": "Invalid request."
                        }');
                        break;
                }
            }
            
            http_response_code(200);
            echo $resolve = json_encode($stmt->fetchAll());

        } catch(Exception $err) {
            http_response_code(200);
            die($reject = '{
                    "status": "500",
                    "message": "' . $err->getMessage() . '"
            }');
        }
            
    }//End of GET

    public function post() {
        try {

            $id = $this->uniqueKey($this->secureParams['userId']);

            $data = [
            '_id' => $id,
            'title' => $this->secureParams['title'],
            'rental_period' => $this->secureParams['rentalperiod'],
            'price' => $this->secureParams['price'],
            'key_money' => $this->secureParams['keyMoney'],
            'minimum_period' => $this->secureParams['minimumPeriod'],
            'available_from' => $this->secureParams['availableFrom'],
            'property_type_id' => $this->secureParams['propertyType'],
            'description' => $this->secureParams['description'],
            'district_id' => $this->secureParams['district'],
            'city_id' => $this->secureParams['city'],
            'facilities' => $this->secureParams['facilities']
            ];

            $stmt = DB::execute(PropertyModel::save($data));

            if(isset($this->secureParams['images'])) {
                foreach ($this->secureParams['images'] as $img) {
                    $stmt = DB::execute(PropertyImages::save(['image' => $img, 'property_id' => $id ]));
                }
            }
            
            http_response_code(201);
            echo $resolve = '{
                "action": "true",
                "message": "The advertisement saved successfully."
            }
            ';

        } catch(Exception $err) {
            http_response_code(200);
            die($reject = '{
                "status": "500",
                "error": "true",
                "message": "' . $err->getMessage() . '"
                }
            }');
        }
            
    }//End of GET

}//End of Class
