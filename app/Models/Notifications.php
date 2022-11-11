<?php

namespace App\Models;

use App\Notifications\CreateNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Modules\PrimaryModule\Models\UserGroupPermission;

class Notifications extends Model
{

    protected $table = 'notifications';
    protected $primaryKey = 'id';


    public function createNotification($type, $data)
    {
        //get Notifibal user permission group
        $user_groups = UserGroupPermission::where('permission_code',$type)->groupBy('user_group_id')->get('user_group_id');
        //get user list
        $users = User::whereIn('user_group_id',$user_groups->pluck('user_group_id'))->get();

        $data['type']=$type;
        $data['create_user_id']=Auth::user()->id;

        if(count($users)>0){
            Notification::send($users, new CreateNotification($data));
        }
    }
}
