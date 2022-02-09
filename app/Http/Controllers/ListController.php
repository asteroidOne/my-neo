<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class ListController extends Controller
{
	public function index(){
		return view('welcome');
    }

    public function get(Request $request){
    	try {
    		$dates = []; 
	        $astroids = []; 
	        $fastestAstroid =[];
	        $closetAstroid =[];
	        $astriodSizeCollection =[];

	    	$productAppUrl = 'https://api.nasa.gov/neo/rest/v1/feed?detailed=true&api_key=PysexB9MDTqTJjA30Uwcm4Vg7Iw2WUdIwppQtEnC&start_date='.$request->start_date.'&end_date='.$request->end_date;
			$client = new Client();
			$response = $client->request('GET', $productAppUrl);
			$data = json_decode($response->getBody()->getContents());
			
			$nearEarthObjects = $data->near_earth_objects;
			$dates = array_keys(get_object_vars($nearEarthObjects));

			$fastestAstroidSpeed = 0;
	        $closetAstroidDistance = 0;
			foreach ($nearEarthObjects as $key => $object) {
				$astroids[]	 = count($object);

				foreach ($object as $key => $objectValues) {
					// GET FASTEST ASTERIOD
					$currentAstroidSpeed = $objectValues->close_approach_data[0]->relative_velocity->kilometers_per_hour;
					if ($currentAstroidSpeed > $fastestAstroidSpeed) {
	                    $fastestAstroidSpeed = $currentAstroidSpeed;
	                    $fastestAstroid = [];
	                    $fastestAstroid = [
	                    	"AsteroidName" => $objectValues->name, 
	                        "ID" => $objectValues->id, 
	                        "Speed" => $currentAstroidSpeed
	                    ];
	                }

					// CLOSEST ASTERIOD
	                $currentAstroidDistance = $objectValues->close_approach_data[0]->miss_distance->kilometers;
	                if ($closetAstroidDistance == 0 || $currentAstroidDistance < $closetAstroidDistance) {
	                    $closetAstroidDistance = $currentAstroidDistance;
	                    $closetAstroid = [];
	                    $closetAstroid = [
	                    	"AsteroidName" => $objectValues->name, 
	                        "ID" => $objectValues->id, 
	                        "Distance" => $currentAstroidDistance
	                    ];
	                }

	                // ASTERIOD SIZE COLLECTION
		            $currentAstroiAvgSize = ($objectValues->estimated_diameter->kilometers->estimated_diameter_max + $objectValues->estimated_diameter->kilometers->estimated_diameter_min)/2;

					$astriodSizeCollection[] =  [
						"AsteroidName" => $objectValues->name, 
					    "ID" => $objectValues->id, 
					    "AvgSizeInKm" => $currentAstroiAvgSize
					]; 
				}
			}

			return response()->json([
				'dates' => $dates,
				'astroids' => $astroids,
				'fastestAstroid' => $fastestAstroid,
				'closetAstroid' => $closetAstroid,
				'astriodSizeCollection' => $astriodSizeCollection
			]);
    	} catch(\Exception $e) {
    		return response()->json([
				'dates' => $dates,
				'astroids' => $astroids,
				'fastestAstroid' => $fastestAstroid,
				'closetAstroid' => $closetAstroid,
				'astriodSizeCollection' => $astriodSizeCollection
			], 500);
    	}
    }
}
