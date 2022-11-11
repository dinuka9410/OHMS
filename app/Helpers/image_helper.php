<?php

//item image
  if(!function_exists('item_image')){
    function item_image($iid){
      if(file_exists('disk/item/itm_'.$iid.'.jpg')){
        return '/disk/item/itm_'.$iid.'.jpg';
      }else{
        return '/disk/blank.jpg';
      }
    }
  }
//user image
  if(!function_exists('user_image')){
    function user_image($uid){
        if(file_exists('storage/img/userprofiles/'.$uid.'.jpg')){
            return asset('storage/img/userprofiles/'.$uid.'.jpg');
          }else{
    return asset('dist/images/default.png');
}
    }
  }
