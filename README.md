TNT Affiliate
============

[![Latest Stable Version](https://poser.pugx.org/jdi/tntaffiliate/version.png)](https://packagist.org/packages/jdi/tntaffiliate)
[![Total Downloads](https://poser.pugx.org/jdi/tntaffiliate/d/total.png)](https://packagist.org/packages/jdi/tntaffiliate) 
[![Build Status](https://travis-ci.org/jdi/tntaffiliate.png)](https://travis-ci.org/jdi/tntaffiliate)

Introduction
---

Please read the following integration guide and follow all instructions to ensure your setup and ready to start tracking data.

Getting DNS Setup
---
**Action Required: Creating DNS Entries**

Before you can get started with your new affiliate system, you will need to configure two hostnames to point to our servers, to provide your tracking links (track.) and your affiliate portal (affiliates.).  Due to calls required through the tracking lifecycle, you must apply these DNS entries on the domain of your product.  If you are running multiple products, you should have the entries on each domain.

**affiliates.youdomain.tld > CNAME > affiliates.tntaffiliate.com**

**track.yourdomain.tld > CNAME > track.tntaffiliate.com**

Although these are the defaults, you can choose one of the following alternatives for your hostname.

Default Hostname | Alternative Hostnames | CNAME
-----------------|-----------------------|------------------------------
affiliates.      | aff. affiliate.       |  affiliates.tntaffiliate.com
track.           | url. link. click.     |  track.tntaffiliate.com


The TNT Affiliate API
---

A large portion of the tracking process is actioned through our API.  This is due to reliability and control on both sides.  The API is a basic HTTP JSON process authenticated through OAUTH2.  To simplify connectivity within PHP projects, we have created a composer package (jdi/tntaffiliate) available for you to include into your projects. allowing you to make a few basic calls.  This guide is written under the assumption that this package will be used.  If you do not wish to use the package, you should be able to review the code to see how to connect, authenticate and make calls to the api.  API documentation will be available at a later date.

###Links

Packagist: https://packagist.org/packages/jdi/tntaffiliate

GitHub: https://github.com/jdi/tntaffiliate

Registering New Affiliates
---
There are two options to register new affiliates into the system.  For simplicity, the easiest method is to simply redirect your affiliates to affiliates.yourdomain.tld, where they will be given a login form, but also a link to create an account if they are not already registered.  If you do not want this feature to be available, you can disable it in your brand configuration options.

Alternatively, you can create affiliates through our api, which can be done with the following call:

    $tnt = new TntAffiliateApi();
    
    $tnt->createAffiliate('email','password','name');

Tracking Users (References)
---
As soon as you have your own unique identifier for a visitor, you can provide tnt with the reference and the original visitor ID, which will allow you to call all actions with that reference.  This improves tracking, as you can fire actions for alternative devices, browsers or even through telephone sales.

The visitor ID is placed in a cookie on your domain with the name of “TNT:VID”.  The TntAffiliate class contains a helper method to pull this cookie back from the php superglobal $_COOKIE, however, if you read cookies through alternative methods, it is recommened to pull the visitor id manually.

    $tnt = new TntAffiliateApi();

    $visitorId = TntAffiliate::getVisitorId();       // Attempts to pull visitor id from $_COOKIE
    $tnt->visitorReference('reference', $visitorId); // Create the reference
    
Actions
---
The preferred method for tracking actions from your site is through curl.  This method provides the most accurate results for you and your affiliates.  Affiliate tracking pixels can also be handled by you however you like to manage them.
    
    $tnt = new TntAffiliateApi();

    $actionOptions                 = new \JDI\TntAffiliate\Models\ActionOptions();
    $actionOptions->eventReference = 'my_action_reference';     // Action reference, e.g. Order ID
    $actionOptions->userReference  = $userId;    // User Reference. if a visitor ID is provided, this will store the reference for future calls.
    $actionOptions->pixels         = false;      // Whether to return pixels to fire
    $actionOptions->data           = [   // Any custom data you wish to allocate to the action, which will be available in the future.
      'user_id'    => $userId,
      'email'      => 'test@abc.com',
      'other_data' => 'some other information'
    ];

    $actionId = $api->triggerAction('action_key', $visitorId, $actionOptions)->getActionId();

Approving Actions
---
When setting up actions in the control panel, you are given the option for them to be auto approved, or require approval.  This allows you to automatically approve every click action, but hold every sale action until it has been verified by your order system.  Once you have a final state of an action, you are then able to take action on the pending state.  There are 3 processes which can be made at this stage.

- Approve : Approve will complete the original action and pay out any commissions required.
- Cancel : This will mark the action as cancelled and not pay any commissions.
- Fraud : Similar to cancel, no commissions will be paid, but the affiliate will also be notified about the fraudulent action.

    $approveOptions       = new \JDI\TntAffiliate\Models\ApproveOptions();
    $approveOptions->type = 'action_type';
    $approved             = $api->approveAction(
      $eventReference, // Action reference, e.g. Order ID
      \JDI\TntAffiliate\Constants\ApprovalState::APPROVE,
      $approveOptions
    );

Refunds, Cancels & Fraud
---
Sometimes, your customers decide they no longer wish to use your product, in this case, you can choose

    $tnt = new TntAffiliateApi();

    $refundOptions             = new \JDI\TntAffiliate\Models\RefundOptions();
    $refundOptions->type       = 'action_type';
    $refundOptions->reason     = 'fraudulent';

    $refunded = $api->refund($orderId, $refundOptions);

Firing Pixels
---
When actions are triggered, pixels can be queued based on the involved affiliate etc.  It is recommended to pull these pixels back at the time of the action to fire them as close to the time as possible, and to avoid checking the queue constantly.

    $tnt = new TntAffiliateApi();

    $visitorId = TntAffiliate::getVisitorId(); //A User Reference is also valid
    $tnt->getPendingPixels($visitorId); //array of pending pixels
