<?php

class UsersController extends \BaseController {

	protected $user;

	public function __construct() {
		$this->user = Sentry::getUser();
	}
	/**
	 * Display a index Member
	 *
	 * @return Response
	 */
	public function index()
	{
		$users = Sentry::getUser();
		return View::make('users.index');
	}

	/**
	* Login
	*/
	public function login() {
		if( ! Sentry::check() ) {
			return View::make('users.login');
		} else {
			return Redirect::route('users.index');
		}
	}
	/**
	* Process Login
	*/
	public function doLogin() {
		$rules = array(
			'email' => 'required|email',
			'password' => 'required'
		);
		$validator = Validator::make(Input::all(), $rules);
		//validate the input
		if($validator->fails()) {
			return Redirect::back()->withErrors($validator)->withInput();
		}

		try {
			
			$data = array(
				'email' => Input::get('email'),
				'password' => Input::get('password'),
			);
			$user = Sentry::authenticate($data, false);
			//check group and redirect to group page
				return Redirect::route('users.index');
		}
		catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
        {
            return Redirect::route('users.login')->withInput(Input::except('password'))->withErrors("Login required");
        }
        catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
        {
            return Redirect::route('users.login')->withInput(Input::except('password'))->withErrors("Password required");
        }
        catch 	(Cartalyst\Sentry\Users\WrongPasswordException $e)
        {
            return Redirect::route('users.login')->withInput(Input::except('password'))->withErrors("Wrong Password");
        }
        catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
        {
            return Redirect::route('users.login')->withInput(Input::except('password'))->withErrors("Email not found");
        }
        catch (Cartalyst\Sentry\Users\UserNotActivatedException $e)
        {
            return Redirect::route('users.login')->withInput(Input::except('password'))->withErrors("Not activated");
        }
	}
	/**
	* Sentry logout
	*
	*/
	public function logout() {
		Sentry::logout();
		return Redirect::route('home.index');
	}

	/*
	* Show profile users
	*/
	public function show($id) {
		if($this->user->id != $id) {
			return App::abort(404, 'Unauthorized action.');
		}
		$user = $this->user;
		return View::make('users.show', compact('user'));
	}
	/**
	 * Show the form for editing the specified user.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		if($this->user->id != $id) {
			return App::abort(404, 'Unauthorized action.');
		}
		$user = User::find($id);

		return View::make('users.edit', compact('user'));
	}

	/**
	 * Update the specified home in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		if( $this->user->id != $id ) {
			return App::abort(404, 'Unauthorized action.');
		}
		$user = User::findOrFail($id);
		$rules = array(
			'first_name' => 'required',
			'last_name' => 'required',
			'phone' => 'numeric',
			'avatar' => 'image'
		);
		$validator = Validator::make($data = Input::except(array('old_password','password','password_confirmation')), $rules);

		if ( $validator->fails() ) {
			return Redirect::back()->withErrors($validator)->withInput();
		}
		if( !is_null(Input::file('avatar')) ) {
			//delete old avatar
			if( File::exists($user->avatar) ) File::delete($user->avatar);

			$fileName =  time().Input::file('avatar')->getClientOriginalName();
			$destinationPath = 'public/uploads/profile';
			Input::file('avatar')->move($destinationPath, $fileName);
			$avatar_path = $destinationPath . '/' . $fileName;

			//update new path image
			$data['avatar'] = $avatar_path;
		}

		/**
		* Process change password if user type old password
		*/
		if( Input::has('old_password') ) {
			$pwrules = array(
				'password' => 'required|confirmed'
			);
			if( $this->user->checkPassword(Input::get('old_password'))) {
				$pwvalidate = Validator::make(Input::only('password', 'password_confirmation'), $pwrules);
				if( $pwvalidate->fails() )  {
					return Redirect::back()->withErrors($pwvalidate)->withInput();
				}
				$data['password'] = Input::get('password');
			} else {
				return Redirect::back()->withErrors("Old Password don't match")->withInput();
			}
		}
		
		$user->update($data);

		return Redirect::route('users.show',$user->id)->with('message','Your profile had updated!');
	}


}