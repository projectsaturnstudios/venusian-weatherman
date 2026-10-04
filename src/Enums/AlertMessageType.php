<?php

namespace ProjectSaturnStudios\Weatherman\Enums;

enum AlertMessageType: string
{
    case ALERT = 'Alert';
    case UPDATE = 'Update';
    case CANCEL = 'Cancel';
    case ACK = 'Ack';
    case ERROR = 'Error';
}
