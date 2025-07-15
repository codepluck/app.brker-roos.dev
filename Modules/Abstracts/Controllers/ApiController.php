<?php

// Author: Neeraj Saini
// Email: hax-neeraj@outlook.com
// GitHub: https://github.com/haxneeraj/
// LinkedIn: https://www.linkedin.com/in/hax-neeraj/

namespace Modules\Abstracts\Controllers;
use Modules\Abstracts\Traits\ResponseTrait;

use Session, Auth;

abstract class ApiController extends Controller
{
    use ResponseTrait;
    public function __construct()
    {
        if(!Session::has('owner_venue_id') && Session::get('owner_venue_id') == null):
            $user = Auth::user();
            if($user->hasRole('owner') && count($user->venues) > 0):
                Session::put('owner_venue_id', $user->venues->first()->id);
            endif;
        endif;

        $this->getOwnerID();
    }

    public function getOwnerID()
    {
        $user = Auth::user();
        if($user->hasRole('owner'))
        {
            Session::put('owner_id', $user->id);
        }
        else{
            if(count($user->staff ?? [])):
                Session::put('owner_id', $user->staff->first()->user_id);
            endif;
        }
    }
}
