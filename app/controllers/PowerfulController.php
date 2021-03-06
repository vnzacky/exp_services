<?php

class PowerfulController extends \BaseController {

	/**
	 * Display a listing of powerful
	 *
	 * @return Response
	 */
	public function index()
	{
		$powerful = Powerful::orderBy('id','desc')->paginate(10);

		return View::make('powerful.index', compact('powerful'));
	}

	/**
	 * Show the form for creating a new powerful
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('powerful.create');
	}

	/**
	 * Store a newly created powerful in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$rules = array(
			'name' => 'required',
			'icon' => 'image'
		);
		$validator = Validator::make($data = Input::all(), $rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		//upload powerful icon
		if( Input::hasFile('icon') ) {
			$name = md5(time() . Input::file('icon')->getClientOriginalName()) . '.' . Input::file('icon')->getClientOriginalExtension();
			$folder = "public/uploads/powerful";
			Input::file('icon')->move($folder, $name);
			$path = $folder . '/' . $name;
			//update new path
			$data['icon'] = $path;
		}
		
		//save to database
		Powerful::create($data);

		return Redirect::route('admin.powerful.index')->with('message', 'Item had created!');
	}

	/**
	 * Show the form for editing the specified powerful.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$powerful = Powerful::find($id);

		return View::make('powerful.edit', compact('powerful'));
	}

	/**
	 * Update the specified powerful in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$powerful = Powerful::findOrFail($id);

		$rules = array(
			'name' => 'required',
			'icon' => 'image'
		);
		$validator = Validator::make($data = Input::all(), $rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		//upload powerful icon
		if( Input::hasFile('icon') ) {
			//delete old icon
			if( File::exists($powerful->icon) ) File::delete($powerful->icon);
			//create new icon
			$name = md5(time() . Input::file('icon')->getClientOriginalName()) . '.' . Input::file('icon')->getClientOriginalExtension();;
			$folder = "public/uploads/powerful";
			Input::file('icon')->move($folder, $name);
			$path = $folder . '/' . $name;

			//update new path
			$data['icon'] = $path;
		} else {
			unset($data['icon']);
		}

		$powerful->update($data);

		return Redirect::route('admin.powerful.index')->with('message', 'Item had updated!');
	}

	/**
	 * Remove the specified powerful from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Powerful::destroy($id);

		return Redirect::route('admin.powerful.index')->withErrors('Item had deleted!');
	}

}
