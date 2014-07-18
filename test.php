<?php
require 'vendor/autoload.php';

define('CLIENT_ID', 'your client id');
define('CLIENT_SECRET', 'your client secret');

define('TOKEN_CACHE_FILE', 'token.json');

$tick  = json_decode('"\u2714"');
$cross = json_decode('"\u2718"');

function writeLine($line = '')
{
  echo $line . "\n";
}

writeLine("Initialising TNT Api Object");
$api = new \JDI\TntAffiliate\TntAffiliateApi(
  defined('API_URL')
    ? API_URL : \JDI\TntAffiliate\TntAffiliateApi::DEFAULT_API_URL
);
writeLine();

$loggedIn = false;
/**
 * Login to the API, or set the token from a cache
 */
$tokenJson = json_decode(file_get_contents(TOKEN_CACHE_FILE));
if(isset($tokenJson->token))
{
  writeLine("Setting token '" . $tokenJson->token . "' from cache");
  $api->setToken($tokenJson->token, $tokenJson->tokenType);
  try
  {
    $user = $api->whoami();
    writeLine("$tick Token valid, logged in as $user");
    $loggedIn = true;
  }
  catch(Exception $e)
  {
    writeLine("$cross The token in cache has expired");
  }
}

if(!$loggedIn)
{
  writeLine("Logging in to api");

  if($api->login(CLIENT_ID, CLIENT_SECRET))
  {
    writeLine("$tick Logged in successfully as '" . CLIENT_ID . "'");

    $tokenJson            = new stdClass();
    $tokenJson->token     = $api->getToken();
    $tokenJson->tokenType = $api->getTokenType();
    file_put_contents(TOKEN_CACHE_FILE, json_encode($tokenJson));

    writeLine("Written token cache to '" . TOKEN_CACHE_FILE . "'");
  }
  else
  {
    writeLine("$cross Unable to login as '" . CLIENT_ID . "'");
    die;
  }
}
writeLine();

/**
 * Verify API connectivity and version retrieval
 */
writeLine("Retrieving API version");
$version = $api->getVersion();
writeLine("API version detected as " . $version);
writeLine();

/**
 * Verify API Auth
 */
writeLine("Verifying API Authentication");
$whoami = $api->whoami();
writeLine("You are connected as '" . $whoami . "'");
writeLine();

/**
 * Creating an affiliate
 */
writeLine("Creating affiliate");
try
{
  if($api->createAffiliate('test@example.com', 'password', 'Test User'))
  {
    writeLine("$tick Created affiliate 'test@example.com");
  }
  else
  {
    writeLine("$cross Unable to create affiliate 'test@example.com");
  }
}
catch(Exception $e)
{
  writeLine("$cross Unable to create affiliate : " . $e->getMessage());
}
writeLine();

/**
 * Create a test visitor ID - Be sure not to change this to avoid conflict
 */
$visitorId = 'VIS:1234567890123123123123';

/**
 * Trigger a join
 */
$userId = 123;

writeLine("Triggering a join action");
$joinOptions                 = new \JDI\TntAffiliate\Models\ActionOptions();
$joinOptions->userReference  = $userId;
$joinOptions->eventReference = 'myjoinref';
$joinOptions->data           = [
  'user_id' => $userId,
  'email'   => 'test@abc.com'
];

$joinActionId = $api->triggerAction('join', $visitorId, $joinOptions)
  ->getActionId();
writeLine("$tick Join triggered, resulting in action ID '$joinActionId'");
writeLine();

/**
 * Trigger a sale
 */
$orderId = 345;

writeLine("Triggering a sale action");
$saleOptions = new \JDI\TntAffiliate\Models\ActionOptions();
//Create a reference for the visitor ID with our user id for future calls
$saleOptions->eventReference = $orderId;
$saleOptions->amount         = 10.95;
$saleOptions->data           = ['product' => "eBook 1"];
$saleOptions->pixels         = true;

$saleAction = $api->triggerAction('sale', $userId, $saleOptions);
writeLine(
  "$tick Sale triggered, resulting in action ID '"
  . $saleAction->getActionId() . "', "
  . (int)$saleAction->getPixels() . " with pixels to fire"
);
writeLine();

/**
 * Approve the sale
 */
writeLine("Approving sale action for order id " . $orderId);
$approved = $api->approveAction(
  $orderId,
  \JDI\TntAffiliate\Constants\ApprovalState::APPROVE
);
if($approved)
{
  writeLine("$tick Approved order id " . $orderId);
}
else
{
  writeLine("$cross Unable to approve order id " . $orderId);
}
writeLine();

/**
 * Refund the sale
 */
writeLine("Refunding order id " . $orderId);
$refundOptions             = new \JDI\TntAffiliate\Models\RefundOptions();
$refundOptions->type       = \JDI\TntAffiliate\Models\RefundOptions::TYPE_FRAUD;
$refundOptions->fullRefund = true;
$refundOptions->reclaim    = \JDI\TntAffiliate\Models\RefundOptions::RECLAIM_BOTH;
$refundOptions->reason     = 'fraudulent';
$refundOptions->amount     = 39.99;

$refunded = $api->refund($orderId, $refundOptions);
if($refunded)
{
  writeLine("$tick Refunded order id " . $orderId);
}
else
{
  writeLine("$cross Unable to refund order id " . $orderId);
}
writeLine();

/**
 * Get Pending Pixels
 */
writeLine("Looking for any pending pixels");
$pixels = $api->getPendingPixels($visitorId);
if(empty($pixels))
{
  writeLine("No pending pixels located");
}
else
{
  writeLine("Found " . count($pixels) . " Pixels");
  foreach($pixels as $pixel)
  {
    writeLine($pixel->render());
  }
}
writeLine();

writeLine("Test Complete");
writeLine();
