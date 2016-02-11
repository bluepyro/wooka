
<?php
             
$client_id = "3tR97ji3KeNaEsnCZuOIKb34UA8XUi89";
$client_secret = "u5O5llgpn0uKfQlHcx9dXKxPyRRAv5GRW7qWmVpys76KOiz42XI2xYFEv8q2hKm8";

$extractedAuthCode = $_GET['code']; //I could also get this using $_REQUEST['code'];

//This is my original code which worked on obtaining the athorization code from the URI sent by moves app. Ionnis showed me $_GET[], which natively does it in 1 line of code
//$urlWithReceivedCode = $_SERVER['REQUEST_URI'];
//$urlparse = pathinfo($urlWithReceivedCode);
//$receivedCode = $urlparse['basename'];
//$extractedAuthCode = substr($receivedCode,6,-7);
//echo $extractedAuthCode;

//Below I was checking that all returned access codes were the same size everytime (and they are) because I was using substr() before using $_GET[]
//I1Po1m52NVblY4W7JeTuSHd41E5J6FvNqAph1xRx3nBXkWA5B51rVyUj4i74p0kV
//IJscHm2bzhaFg87w0PktCBphwS11HFxpDw5bBVFFq5w1DXIv7147r2Ku46kl1H5m

$tokenRequestUrl = "https://api.moves-app.com/oauth/v1/access_token?grant_type=authorization_code&code=$extractedAuthCode&client_id=$client_id&client_secret=$client_secret";
//echo $tokenRequestUrl; --->testing that URL was formed properly with variables (successful)

$curlPostSession = curl_init($tokenRequestUrl);

curl_setopt($curlPostSession, CURLOPT_POST, 1);
curl_setopt($curlPostSession, CURLOPT_RETURNTRANSFER, true);
$tokenRequestJsonResponse = curl_exec($curlPostSession);
curl_close($curlPostSession);

$tokenObject = json_decode($tokenRequestJsonResponse); //obtained help from Ioannis creating a JSON object from the JSON in string form sent by moves app
													   //This is the help I needed in being able to validate the token. 
$accessToken = $tokenObject->access_token;


//echo $tokenRequestResponse; ---> testing to see that the token was being received (successful)

$validateTokenUrl = "https://api.moves-app.com/oauth/v1/tokeninfo?access_token=$accessToken";

//echo $validateTokenUrl ---> testing that URL was valid

$curlGetSession = curl_init();
curl_setopt($curlGetSession, CURLOPT_URL, $validateTokenUrl);
curl_setopt($curlGetSession, CURLOPT_RETURNTRANSFER, true);
$tokenValidation = curl_exec($curlGetSession);
curl_close($curlGetSession);

//saving my token in a session var so I can use later
session_start();
$_SESSION["session_access_token"] = $accessToken;

//echo $tokenValidation; -->testing token was validated (received JSON successfully)

//I'm done getting the token. Already put it in the session. Now I can redirect to my profile page
//where I will get the data fromm the profile.

$baseURL="https://api.moves-app.com/api/1.1"; //base url for all api requests

//GET request for user profile with token in http header as per moves app docs
$chGetUserProfile = curl_init("$baseURL/user/profile");
curl_setopt($chGetUserProfile, CURLOPT_HTTPHEADER, array( 'Authorization: Bearer ' . $accessToken ) );
curl_setopt($chGetUserProfile, CURLOPT_RETURNTRANSFER, true);
$userProfileJsonResponse = curl_exec($chGetUserProfile);
curl_close($chGetUserProfile);

$userProfileObject  = json_decode($userProfileJsonResponse);

echo $userProfileJsonResponse; //testing that user profile JSON was obtained with GET request
