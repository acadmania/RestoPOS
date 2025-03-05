<?php

namespace App\Http\Controllers\API\V1;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
class RoleController extends BaseController
{
    protected $role = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Role $role)
    {
        $this->middleware('auth:api');
        $this->role = $role;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = $this->role->with('permissions')->latest()->paginate(10);


        return $this->sendResponse($roles, 'Role list');
    }

    public function permissions()
    {
        $permissions = Permission::all()->pluck('name', 'id');

        return $this->sendResponse($permissions, 'Permission list');
    }

    public function list()
    {
        $roles = $this->role->pluck('name', 'id');

        return $this->sendResponse($roles, 'Role list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $role = $this->role->create([
            'name' => $request->get('name'),
            'guard_name' => 'api',


        ]);
        $permissions = Permission::find($request->permissions);
        $role->syncPermissions($permissions);

        return $this->sendResponse($role, 'Role Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $location = $this->location->findOrFail($id);

        $location->update([
            'name' => $request->get('name'),
            'code' => $request->get('code'),

        ]);


        return $this->sendResponse($location, 'Location Information has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $location = $this->location->findOrFail($id);

        $location->delete();

        return $this->sendResponse($location, 'Location has been Deleted');
    }
}
