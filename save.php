<?php
    require 'vendor/autoload.php';

    use App\Secrets;

    if(isset($_POST['url'])) {
        $url = $_POST['url'];
        $array = explode("?", $url);
        $url = $array[0].'.json';
        echo "url is $url <br />";
        //$myfile = fopen("logs.txt", "a") or die("Unable to open file!");
        //fwrite($myfile, "\n". $url);
        //fclose($myfile);
        //file_put_contents('logs.txt', $url , FILE_APPEND);
        $secrets = new Secrets();
        $authToken = null;
        if(!isset($_COOKIE['authenticated'])) {
            $authToken = getAccessToken($secrets);
            setcookie("authenticated", $authToken, time() + (3600), "/");
        } else {
            $authToken = $_COOKIE['authenticated'];
        }
        //$accessToken = getAccessToken($secrets);
        //echo "alert('Success!')";
    }else{
        echo "alert('Oops! Something went wrong!')";
    }

    function sendPost($url, $secrets){
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        

        $url = 'https://www.reddit.com/api/v1/access_token';

            
        $username = $secrets->USERNAME;
        $password = $secrets->PASSWORD;
        echo "password is $password username is $username <br/>";
        $clientId = "EaQr1tXzOXPa5Q";
        $clientSecret = "ICuXCKfqdzDQLfS1fE3iC4k_wMU-Kw";
        $post_data = array(
                'grant_type' => 'password',
                'username' => $username,
                'password' => $password,
        );
        $headers = array();
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';

        $options = array(
                CURLOPT_URL            => $url,
                //CURLOPT_HEADER         => true,
                CURLOPT_HTTPHEADER     => $headers,  
                CURLOPT_USERAGENT      => 'ImprovedPostSaver/0.1 by CloneJediXI',
                //CURLOPT_VERBOSE        => true,
                CURLOPT_RETURNTRANSFER => true,
                //CURLOPT_FOLLOWLOCATION => true,
                //CURLOPT_SSL_VERIFYPEER => false,    // for https
                CURLOPT_USERPWD        => "$clientId:$clientSecret",
                //CURLOPT_HTTPAUTH       => CURLAUTH_DIGEST
                //CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => http_build_query($post_data) 
                
        );

        $ch = curl_init();

        curl_setopt_array( $ch, $options );

        try {
            $raw_response  = curl_exec( $ch );
            //echo $raw_response;
            // validate CURL status
            if(curl_errno($ch))
                throw new Exception(curl_error($ch), 500);

            // validate HTTP status code (user/password credential issues)
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($status_code != 200)
                throw new Exception("Response with Status Code [" . $status_code . "].", 500);

        } catch(Exception $ex) {
            if ($ch != null) curl_close($ch);
            throw new Exception($ex);
        }

        if ($ch != null) curl_close($ch);
        if ($raw_response != null){
            //$token = $raw_response["access_token"];
            //echo "Access token is $token";
            //print_r($raw_response);
            //var_dump($raw_response);
            $data = json_decode($raw_response, true);
            return $data['access_token'];
            //return json_decode($raw_response, JSON_PRETTY_PRINT); 
        }else {
            return "No response";
        }
    }
    function getAccessToken($secrets){
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $url = 'https://www.reddit.com/api/v1/access_token';
        $post_data = array(
                'grant_type' => 'password',
                'username' => $secrets::USERNAME,
                'password' => $secrets::PASSWORD,
        );
        $headers = array('Content-Type: application/x-www-form-urlencoded');
        $clientId = $secrets::CLIENT_ID;
        $clientSecret = $secrets::CLIENT_SECRET;
        $options = array(
                CURLOPT_URL            => $url,
                CURLOPT_HTTPHEADER     => $headers,  
                CURLOPT_USERAGENT      => 'ImprovedPostSaver/0.1 by CloneJediXI',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERPWD        => "$clientId:$clientSecret",
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => http_build_query($post_data) 
        );

        $ch = curl_init();

        curl_setopt_array( $ch, $options );

        try {
            $raw_response  = curl_exec( $ch );
            // validate CURL status
            if(curl_errno($ch))
                throw new Exception(curl_error($ch), 500);

            // validate HTTP status code (user/password credential issues)
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($status_code != 200)
                throw new Exception("Response with Status Code [" . $status_code . "].", 500);

        } catch(Exception $ex) {
            if ($ch != null) curl_close($ch);
            throw new Exception($ex);
        }

        if ($ch != null) curl_close($ch);
        if ($raw_response != null){
            $data = json_decode($raw_response, true);
            return $data['access_token'];
        }else {
            return null;
        }
    }
?>
