<?php
class Pixlee_Pixlee {
  private $apiKey;
  private $baseURL;
  private $secretKey;

  public function __construct($accountApiKey, $accountSecretKey){
    if(is_null($accountApiKey) || empty($accountApiKey)) {
      throw new Exception("An API Key is required");
    }
    if(is_null($accountSecretKey) || empty($accountSecretKey)) {
      throw new Exception("An API Secret is required");
    }
    $this->apiKey   = $accountApiKey;
    $this->secretKey = $accountSecretKey;
    $this->baseURL  = "https://distillery.pixlee.com/api/v2";

    $this->getAlbums(); // Throws an exception if the API call is not successful
  }

  public function getAlbums(){
    return $this->getFromAPI("/albums");
  }

  // The following functions don't seem to be used anywhere
  // The only functions I seem to NEED are:
  //    1) getAlbums, to make sure that the config is right, and we can hit the API
  //    2) createProduct, which doubles as "updateProduct," via POST
  // Not going to spend time figuring out the distillery versions of the following
  // v1 API calls, but leaving the original declarations here, in case I'm wrong
  /*
  public function getPhotos($albumID, $options = NULL ){
    return $this->getFromAPI( "/albums/$albumID/photos", $options);
  }
  public function getPhoto($albumID, $photoID, $options = NULL ){
    return $this->getFromAPI( "/albums/$albumID/photos/$photoID", $options);
  }
  // ex of $media = array('photo_url' => $newPhotoURL, 'email_address' => $email_address, 'type' => $type);
  public function createPhoto($albumID, $media){
    // assign media to the data key
    $data           = array('media' => $media);
    $payload        = $this->signedData($data);
    return $this->postToAPI( "/albums/$albumID/photos", $payload );
  }
  */

  public function createProduct($product) {
    /*
        Converted from Rails API format to distillery API format
        Also, now sending _account_ 'api_key' instead of _user_ 'api_key'
        Instead of:
        {
            'album': {
                 'album_name': <VAL>
             }
            'product: {
                 'name': <VAL>,
                 'sku': <VAL>,
                 'buy_now_link_url': <VAL>,
                 'product_photo': <VAL>
             }
        }
        Is now:
        {
            'title': <VAL>,
            'album_type': <VAL>,
            'num_photo': <VAL>,
            'num_inbox_photo': <VAL>,
            'product':
                'sku': <VAL>,
                ...
                'product_photo': <VAL>
                'regional_info': [
                  {
                    'buy_now_link_url': <VAL>,
                    'price': <VAL>,
                    ...
                  },
                  {
                    'buy_now_link_url': <VAL>,
                    'price': <VAL>,
                    ...
                  }                  
                ]
            }
        }
    */

    $data = array(
      'title' => $product['name'], 
      'album_type' => 'product', 
      'live_update' => false, 
      'num_photo' => 0,
      'num_inbox_photo' => 0, 
      'product' => $product
    );

    //Fix for php versions that don't support JSON_UNESCAPED_SLASHES (< php 5.4)
    if(defined("JSON_UNESCAPED_SLASHES")){
      $payload = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    } else {
      $payload = str_replace('\\/', '/', json_encode($data));
    }
    return $this->postToAPI( "/albums?api_key=" . $this->apiKey, $payload );
  }

  // Private functions
  private function getFromAPI( $uri, $options = NULL ){
    $apiString    = "?api_key=".$this->apiKey;
    $urlToHit     = $this->baseURL;
    $urlToHit     = $urlToHit . $uri . $apiString;

    if( !is_null($options)){
      $queryString  = http_build_query($options);
      $urlToHit     = $urlToHit . "&" . $queryString;
    }

    $ch = curl_init( $urlToHit );
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'X-Alt-Referer: magento.pixlee.com'
    )
  );
    $response   = curl_exec($ch);

    return $this->handleResponse($response, $ch);
  }

  private function postToAPI($uri, $payload){
    $urlToHit = $this->baseURL . $uri;

    $ch = curl_init( $urlToHit );
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'X-Alt-Referer: magento.pixlee.com',
      'Content-Length: ' . strlen($payload),
      'Signature: ' . $this->generateSignature($payload)
    )
  );
    $response   = curl_exec($ch);

    return $this->handleResponse($response, $ch);
  }

  private function generateSignature($data) {
    return base64_encode(hash_hmac('sha1', $data,  $this->secretKey, true));
  }

  private function handleResponse($response, $ch){
    $responseInfo   = curl_getinfo($ch);
    $responseCode   = $responseInfo['http_code'];
    $theResult      = json_decode($response);

    curl_close($ch);

    // Unlike the rails API, distillery doesn't return such pretty statuses
    // On successful creation, we get a JSON with the created product's fields:
    //  {"id":217127,"title":"Tori Tank","user_id":1055,"account_id":216,"public_contribution":false,"thumbnail_id":0,"inbox_thumbnail_id":0,"public_viewing":false,"description":null,"deleted_at":null,"public_token":null,"moderation":false,"email_slug":"A27EfF","campaign":false,"instructions":null,"action_link":null,"password":null,"has_password":false,"collect_email":false,"collect_custom_1":false,"collect_custom_1_field":null,"location_updated_at":null,"captions_updated_at":null,"redis_count":null,"num_inbox_photos":null,"unread_messages":null,"num_photos":null,"updated_dead_at":null,"live_update":false,"album_type":"product","display_options":{},"photos":[],"created_at":"2016-03-11 04:28:45.592","updated_at":"2016-03-11 04:28:45.592"}
    // On product update, we just get a string that says:
    //  Product updated.
    // Suppose we'll check the HTTP return code, but not expect a JSON 'status' field
    if( !$this->isBetween( $responseCode, 200, 299 ) ){
      throw new Exception("HTTP $responseCode response from API");
    }else{
      if (is_null($theResult)) {
        // Some endpoints of the API return a JSON object and some return a string
        // PHP's json_decode returns null if the argument is a string
        // For string responses, we want to pass the string back and not null
        return $response;
      } else {
        return $theResult;
      }
    }
  }

  private function isBetween($theNum, $low, $high){
    if($theNum >= $low &&  $theNum <= $high){
      return true;
    }
    else{
      return false;
    }
  }
}

?>
