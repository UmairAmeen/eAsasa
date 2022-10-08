<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\CreateUserRequest;

use App\User;
use App\Role;

use DB;
use View;
use Exception;

class UserController extends Controller
{
    public function __construct()
    {
        \View::share('title',"User");
         View::share('load_head', true);
         View::share('user_menu',true);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::orderBy('id','DESC')->paginate(5);
        return view('users.index',compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::lists('display_name','id');
        return view('users.create',compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateUserRequest $request)
    {
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $input['allowed_discount'] = ($input['allowed_discount'] != '') ? $input['allowed_discount'] : '100';
        $input['allowed_discount_pkr'] = ($input['allowed_discount_pkr']) ? : 0;
        $input['fixed_discount'] = ($request['fixed_discount']) ? true : false;
        $input['master_discount'] = ($request['master_discount']) ? true : false;
        $user = User::create($input);
        foreach ($request->input('roles') as $key => $value) {
            $role = Role::whereId($value)->first();
            $user->attachRole($role);
        }
        return response()->json(['message' => 'User is created successfully','action'=>'redirect','do'=>url('/users')], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        return view('users.show',compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::lists('display_name','id');
        $userRole = $user->roles->lists('id','id')->toArray();

        return view('users.edit',compact('user','roles','userRole'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|min:12|max:12|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);

        $input = $request->all();
        if(!empty($input['password'])){ 
            $input['password'] = bcrypt($input['password']);
        }else{
            $input = array_except($input,array('password'));    
        }

        $input['allowed_discount'] = ($input['allowed_discount'] != '') ? $input['allowed_discount'] : '100';
        $input['allowed_discount_pkr'] = ($input['allowed_discount_pkr']) ? : 0;
        $input['fixed_discount'] = ($request['fixed_discount']) ? true : false;
        $input['master_discount'] = ($request['master_discount']) ? true : false;
        $user = User::find($id);
        $user->update($input);
        DB::table('role_user')->where('user_id',$id)->delete();

        foreach ($request->input('roles') as $key => $value) {
            $role = Role::whereId($value)->first();
            $user->attachRole($role);
        }

        return response()->json(['message' => 'User is successfully updated','action'=>'redirect','do'=>url('/users')], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $role = User::findOrFail($id);
            $role->delete();
        }catch(Exception $e)
        {
            return response()->json(['message' => 'Unable to Remove: '.$e->getMessage()], 403);
        }
        

        return response()->json(['message' => 'User is removed','action'=>'redirect','do'=>url('/users')], 200);
    }
}
