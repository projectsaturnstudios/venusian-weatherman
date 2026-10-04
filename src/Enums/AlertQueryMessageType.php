<?php

namespace ProjectSaturnStudios\Weatherman\Enums;

/**
 * Values accepted by the message_type query parameter.
 * Ack and Error appear on documents and are not queryable.
 */
enum AlertQueryMessageType: string
{
    case ALERT = 'alert';
    case UPDATE = 'update';
    case CANCEL = 'cancel';
}
