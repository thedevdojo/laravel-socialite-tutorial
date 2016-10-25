<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Socialite;
use \App\User;
use \Auth;

class OauthController extends Controller
{
    public function redirectToProvider(){
    	return Socialite::driver('facebook')->redirect();
    }

    public function handleProviderCallback(){
    	$user = Socialite::driver('facebook')->user();

    	$existingUser = User::where('email', '=', $user->getEmail())->first();

    	if(isset($existingUser->id)){
    		if($existingUser->oauth_id == $user->getId()){
    			Auth::login($existingUser);
    		}
    	} else {

	    	$newUser = new User;
	    	$newUser->name = $user->getName();
	    	$newUser->email = $user->getEmail();
	    	$newUser->oauth_id = $user->getId();
	    	$newUser->password = \Hash::make(uniqid());
	    	$newUser->type = 'facebook';
	    	$newUser->save();

	    	Auth::login($newUser);
	    }

    	return redirect('/')->with( array('message' => 'Successfully authenticated via facebook'));
    }
}
