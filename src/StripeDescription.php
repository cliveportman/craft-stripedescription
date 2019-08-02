<?php
/**
 * Stripe Description plugin for Craft CMS 3.x
 *
 * Improves the description sent to Stripe so you have more information when exporting your Stripe payments.
 * It doesn't support subscription payments though.
 *
 * @link      https://clive.theportman.co
 * @copyright Copyright (c) 2019 Clive Portman
 */

namespace cliveportman\stripedescription;

use cliveportman\stripedescription\services\StripeDescriptionService as StripeDescriptionServiceService;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;

use yii\base\Event;

// added by Clive for request event
use craft\commerce\models\Transaction;
use craft\commerce\stripe\events\BuildGatewayRequestEvent;
use craft\commerce\stripe\base\Gateway as StripeGateway;

/**
 *
 * @author    Clive Portman
 * @package   StripeDescription
 * @since     1.0.0
 *
 * @property  StripeDescriptionServiceService $stripeDescriptionService
 */
class StripeDescription extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * StripeDescription::$plugin
     *
     * @var StripeDescription
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================
    
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Do something after we're installed
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    // We were just installed
                }
            }
        );
        
        Event::on(
            StripeGateway::class, 
            StripeGateway::EVENT_BUILD_GATEWAY_REQUEST, 
            function(BuildGatewayRequestEvent $event) {
                StripeDescription::$plugin->stripeDescriptionService->logMessage("EVENT_BUILD_GATEWAY_REQUEST heard.");
                StripeDescription::$plugin->stripeDescriptionService->logMessage($event->request['description']);
                StripeDescription::$plugin->stripeDescriptionService->improveDescription($event);
                StripeDescription::$plugin->stripeDescriptionService->logMessage("EVENT_BUILD_GATEWAY_REQUEST processed.");
        });


    }

}
