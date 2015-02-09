<?php
/**
 * PHP MMSoap Example code
 *
 * Copyright 2014 MessageMedia
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License.
 * You may obtain a copy of the License at http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

// Uncomment the following line for debugging information
// ini_set('display_errors', true);

require('mmsoap/MMSoap.php');

// Set up account details
$username = 'username';
$password = 'password';

// Set up sendMessage parameters
// A list of fictitious numbers provisioned for use in fils, radio, television is available here: 
// http://www.acma.gov.au/Citizen/Consumer-info/All-about-numbers/Special-numbers/fictitious-numbers-for-radio-film-and-television-i-acma


// Recipients has 3 valid formats: 
// a single number as a string, e.g. '+61491570157'
// an array of numbers, e.g.: ['+61491570157', '+61491570158']
// a multidimensional array of numbers with unique IDs, e.g. [['+61491570157', '01'], ['+61491570158', '02']]
$recipients = array(array('+0491 570 157', uniqid())); 
$origin     = '+61491570157';
$message    = 'Hello from messagemedia-php!';

// Capture the wsdl host
//$host = parse_url(WsdlClass::VALUE_WSDL_URL, PHP_URL_HOST);

// Set up stream context Server Name Indication (SNI) for use with proxy and HTTPS
//$opts = array('ssl' => array('SNI_server_name' => $host,'SNI_enabled' => TRUE));
//$context = stream_context_create($opts);

// Set up SOAP Options
$options = array( 
    // Put options here to override defaults
    // Example Proxy Options

    //WsdlClass::WSDL_PROXY_HOST => '127.0.0.1',
    //WsdlClass::WSDL_PROXY_PORT => '8888'

    //WsdlClass::WSDL_PROXY_LOGIN => 'proxyUsername',
    //WsdlClass::WSDL_PROXY_PASSWORD => 'proxyPassword',
    //WsdlClass::WSDL_STREAM_CONTEXT => $context //If you require SNI
);

// Create new MMSoap class
$soap = new MMSoap($username, $password, $options);

// Check user info
echo "\n** User Info\n";
$response       = $soap->getUserInfo();

// If an error is returned then exit
if ($response instanceof SoapFault) 
    exit('Error: ' . $response->getMessage());

$result         = $response->getResult();
$accountDetails = $result->accountDetails;

echo 'Account type: ' . $accountDetails->type . "\n";
echo $result->accountDetails->creditRemaining . " credits remaining\n";

// Send messages using rotary
echo "\n** Send Messages using rotary\n";
echo "Sending '$message' to " . implode(', ', $recipients) . "\n";

// Example of sending a message
$response = $soap->sendMessages($recipients, $message);
$result   = $response->getResult();
echo $result->sent . ' sent / ' . $result->scheduled . ' scheduled / ' . $result->failed . " failed\n";

// Send messages using a source number
echo "\n** Send Messages using a source number.\n";
echo "Sending '$message' to " . implode(', ', $recipients) . "\n";

// Example of sending a message
$response = $soap->sendMessages($recipients, $message, null, $origin);
$result   = $response->getResult();
echo $result->sent . ' sent / ' . $result->scheduled . ' scheduled / ' . $result->failed . " failed\n";

/* // Example of sending a message at a scheduled date and time
echo "\n** Schedule A Message\n";
$oneMinuteInTheFuture = mktime(date("H"), date("i")+1, date("s"), date("m")  , date("d"), date("Y"));
echo "Scheduling to send '$message' on ".date('l jS \of F Y h:iA',$oneMinuteInTheFuture)." to " . implode(', ', $recipients) . "\n";

// This is an example of the date format $scheduled = "2016-07-28T17:10:00";
$scheduled = date('Y-m-j\TG:i:s',$oneMinuteInTheFuture);
$response = $soap->sendMessages($recipients, $message, $scheduled, $origin);
$result   = $response->getResult();
echo $result->sent . ' sent / ' . $result->scheduled . ' scheduled / ' . $result->failed . " failed\n";
*/

// Get blocked numbers
echo "\n** Get Blocked Numbers\n";
$response   = $soap->getBlockedNumbers();
$result     = $response->getResult();
$recipients = $result->recipients->recipient;

if (isset($recipients)) 
    
    foreach ($recipients as $recipient) 
        echo 'The number ' . $recipient . " is blocked\n";
        
else     
    echo "No numbers in your blocked list\n";
    


