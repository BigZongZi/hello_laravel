<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\DemoRequest;
use App\Models\User;
use Auth;

class UsersController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth', ['except' => ['show','create','store','index']]);
		$this->middleware('guest', ['only' => ['create']]);
	}

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function index()
	{
		$users = User::paginate(10);
		return view('users.index',compact('users'));
	}

    public function create()
    {
		return view('users.create');
    }
    
    public function show(User $user)
    {
		return view('users.show',compact('user'));
	}

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
	public function store(Request $request)
	{
		$this->validate($request,[
			'name' => 'required|max:50',
			'email' => 'required|email|unique:users|max:255',
			'password' => 'required|confirmed|min:6'
		]);
		
		$user = User::create([
			'name' => $request->name,
			'email' => $request->email,
			'password' => bcrypt($request->password)
		]);
		Auth::login($user);
		session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
		return redirect()->route('users.show',[$user]);
	}

    /**
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
	public function edit(User $user)
	{
        $this->authorize('update', $user);
		return view('users.edit',compact('user'));
	}

    /**
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
	public function update(User $user, Request $request)
	{
		$this->validate($request, [
			'name' => 'required|max:50',
			'password' => 'nullable|confirmed|min:6'
		]);

        $this->authorize('update', $user);
		
		$data = [];
		$data['name'] = $request->name;
		if ($request->password) {
			$data['password'] = bcrypt($request->password);
		}	
		$user->update($data);

		session()->flash('success', '个人资料更新成功');

		return redirect()->route('users.show', $user->id);
	}

    /**
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
	public function destroy(User $user)
    {
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success','成功删除用户!');
        return back();
    }
}
