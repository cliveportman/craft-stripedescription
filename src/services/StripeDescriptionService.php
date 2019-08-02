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

namespace cliveportman\stripedescription\services;

use cliveportman\stripedescription\StripeDescription;

use Craft;
use craft\base\Component;

/**
 * StripeDescriptionService Service
 *
 * @author    Clive Portman
 * @package   StripeDescription
 * @since     1.0.0
 */
class StripeDescriptionService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     *
     *     StripeDescription::$plugin->stripeDescriptionService->improveDescription()
     *
     * @return mixed
     */
    public function improveDescription($event)
    {
        
        $orderId = $event->metadata['order_id'];
        $order = \craft\commerce\elements\Order::find()
            ->id($orderId)
            ->one();

        if($order) {
            $lineItems = $order->lineItems;
            $product = $lineItems[0]->purchasable->product;
            $description = "Order #" . $orderId . ": " . $lineItems[0]->qty . " x " . $product->title;
            $event->request['description'] = $description;
        }

        return $event;
    }

    /**
     * Use this for logging to plugin-specific log file
     *
     *     StripeDescription::$plugin->stripeDescriptionService->logMessage("Message");
     *
     * @return mixed
     */
    public function logMessage($message = "")
    {
        $file = Craft::getAlias('@storage/logs/stripedescription.log');
        $log = date('Y-m-d H:i:s').' '.$message."\n";
        \craft\helpers\FileHelper::writeToFile($file, $log, ['append' => true]);
        return true;
    }
}
