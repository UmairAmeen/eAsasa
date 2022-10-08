<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Requests\CreateRoleRequest;
use App\Role;
use App\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Exception;
class RolesController extends Controller
{
    public function __construct()
    {
         View::share('load_head', true);
         View::share('role_menu',true);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::orderBy('id','DESC')->paginate(10);
        return view('roles.index',compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permission = Permission::all()->groupBy('module');
        $chunked_permissions = array_chunk($permission->all(),3,true);
        return view('roles.create',compact('chunked_permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRoleRequest $request)
    {
        $role = new Role();
        $role->name = $request->input('name');
        $role->display_name = $request->input('name');
        $role->description = $request->input('description');
        $role->save();

        foreach ($request->input('permission') as $key => $value) {
            $permission = Permission::whereId($value)->first();
            $role->attachPermission($permission);
        }

        return response()->json(['message' => 'Role is created successfully','action'=>'redirect','do'=>url('/roles')], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join("permission_role","permission_role.permission_id","=","permissions.id")
            ->where("permission_role.role_id",$id)
            ->get();

        return view('roles.show',compact('role','rolePermissions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::find($id);
        $permission = Permission::all()->groupBy('module');
        $chunked_permissions = array_chunk($permission->all(),3,true);
        // $permission1 = Permission::orderBy(DB::raw("module, display_name"), 'asc')->where('module','customer')->orWhere('module','supplier')
        // ->orWhere('module','warehouse')->orWhere('module','stock')->get(); 
        // $permission2 = Permission::orderBy(DB::raw("module, display_name"), 'asc')->where('module','transaction')->orWhere('module','human resource')
        // ->orWhere('module','refund')->orWhere('module','stock manage')->orWhere('module','misc')->get();
        // $permission3 = Permission::orderBy(DB::raw("module, display_name"), 'asc')->where('module','product')->orWhere('module','sale')
        // ->orWhere('module','purchase')->get(); 
        // $permission4 = Permission::orderBy(DB::raw("module, display_name"), 'asc')->where('module','report')->get(); 

        $permission = Permission::orderBy(DB::raw("module, display_name"), 'asc')->get();
        $rolePermissions = DB::table("permission_role")->where("permission_role.role_id",$id)
            ->lists('permission_role.permission_id','permission_role.permission_id');

        return view('roles.edit',compact('role','chunked_permissions','rolePermissions'));
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
        $role = Role::find($id);
        $role->display_name = $request->input('display_name');
        $role->description = $request->input('description');
        $role->save();

        DB::table("permission_role")->where("permission_role.role_id",$id)
            ->delete();

        foreach ($request->input('permission') as $key => $value) {
            $permission = Permission::whereId($value)->first();
            $role->attachPermission($permission);
        }

        return response()->json(['message' => 'Role is successfully updated','action'=>'redirect','do'=>url('/roles')], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            if(is_admin() && $id > 1) {
                $role = Role::findOrFail($id);
                $role->delete();
            } else {
                throw new Exception('Access Denied');
            }
            
        } catch(Exception $e) {
            return response()->json(['message' => 'Unable to Remove: '.$e->getMessage()], 403);
        }
        return response()->json(['message' => 'Role is removed','action'=>'redirect','do'=>url('/roles')], 200);
    }
}
