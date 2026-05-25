<?php

namespace App\Notifications\Channels;

use NotificationChannels\WebPush\WebPushChannel as BaseWebPushChannel;

/**
 * Custom WebPush channel wrapper to maintain compatibility with existing references.
 */
class WebPushChannel extends BaseWebPushChannel
{
}
