<?php

namespace App\Enums;

enum PermissionEnum: string
{
    case PERMISSION_LIST = 'list';
    case PERMISSION_SHOW = 'show';
    case PERMISSION_CREATE = 'create';
    case PERMISSION_UPDATE = 'update';
    case PERMISSION_DELETE = 'delete';
}
