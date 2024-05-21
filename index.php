<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Distance Calculator</title>
</head>
<body>
  <?php
   $distance = 0.0;
   if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $from = htmlspecialchars($_POST['from']);
    $to = htmlspecialchars($_POST['to']);
   }
  ?>
  <style>
    *{
      font-size: 1.8rem;
    }
  </style>
  <h2>Distance Calculator</h2>
  <form action="" method="post">
    <label for="add1">Address 1: </label>
    <input type="text" id="add1" name="from" value="<?php echo $from; ?>" style="min-width:200px; margin-bottom:5px;" placeholder="Enter 1st Address..." required>
    <br>
    <label for="add2">Address 2: </label>
    <input type="text" id="add2" name="to" value="<?php echo $to; ?>" style="min-width:200px; margin-bottom:5px;" placeholder="Enter 2nd Address..." required>
    <br>
    <input type="submit" style="min-width:200px; margin-bottom:5px; margin-left:2%;" value="Calculate">
  </form>
  <?php
function geocodeAddressOpenCage($address, $apiKey) {
            $url = 'https://api.opencagedata.com/geocode/v1/json?';
            $params = [
                'q' => urlencode($address),
                'key' => $apiKey,
                'limit' => 1,
                'no_annotations' => 1
            ];
            $requestUrl = $url . http_build_query($params);
        
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $requestUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
            $response = curl_exec($ch);
            curl_close($ch);
        
            $data = json_decode($response, true);
            if (!empty($data['results'])) {
                $latitude = $data['results'][0]['geometry']['lat'];
                $longitude = $data['results'][0]['geometry']['lng'];
                return ['latitude' => $latitude, 'longitude' => $longitude];
            } else {
                return null;
            }
        }

 function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000) {
            $latFrom = deg2rad($latitudeFrom);
            $lonFrom = deg2rad($longitudeFrom);
            $latTo = deg2rad($latitudeTo);
            $lonTo = deg2rad($longitudeTo);
        
            $latDelta = $latTo - $latFrom;
            $lonDelta = $lonTo - $lonFrom;
        
            $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
            return $angle * $earthRadius;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $originAddress = $_POST['from'];
            $destinationAddress = $_POST['to'];
            $apiKey = "14616f1d494648e4b6713cfd49196b85";
        
            $originCoordinates = geocodeAddressOpenCage($originAddress, $apiKey);
            $destinationCoordinates = geocodeAddressOpenCage($destinationAddress, $apiKey);
        
            if ($originCoordinates && $destinationCoordinates) {
                $distance = haversineGreatCircleDistance(
                    $originCoordinates['latitude'],
                    $originCoordinates['longitude'],
                    $destinationCoordinates['latitude'],
                    $destinationCoordinates['longitude']
                );
            } else {
                echo "Geocoding failed for one or both addresses.";
            }
        }
?>
  <div><?php echo number_format((float)$distance/1000, 2, '.', '') .' KM'; ?></div>
</body>
</html>