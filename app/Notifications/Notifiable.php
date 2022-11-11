<?php

namespace App\Notifications;

use Illuminate\Notifications\RoutesNotifications;

/*
@UH 2022-09-22
Upadte defult notification traits
file replase and change from vendor
*/
trait Notifiable
{
    use HasDatabaseNotifications, RoutesNotifications;
}
