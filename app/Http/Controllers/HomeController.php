<?php

namespace App\Http\Controllers;

use App\Models\User;
use DB;
use Hash;
use Illuminate\Http\Request;
use App\Models\Tenant;
use Spatie\Permission\Models\Role;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function tenantList()
    {
        $tenants = Tenant::all();
        return view('tenantList',compact('tenants'));
    }
    public function tenantAdd()
    {

        return view('tenantAdd');
    }
    public function tenantMessageForm($id)
    {

        return view('tenantMessage',compact('id'));
    }
    public function tenantMessageStore(Request $request)
    {
        $tenant = Tenant::find($request->id);
        $tenant->update([
            'message' => $request->message,

        ]);
        return redirect('/dashboard');
    }
    public function tenantStore(Request $request)
    {

        $tenant = Tenant::create([

            'name' => $request->name,
        ]);

        if($request->date)
        $tenant->update([
            'created_at' => $request->date,

        ]);

        $tenant->domains()->create([
            'domain' => $request->domains,
          ]);

          $tenant->run(function () use ($request) {
            $user = User::create([
                'name' => $request->admin_name,
                'email' => $request->admin_email,

                'password' => Hash::make($request->password,),
            ]);

            DB::table('roles')->insert([
                'name' => "Super Admin",
                'guard_name' => 'api',


            ]);

            DB::table('model_has_roles')->insert([
                  'role_id' => 1,
                  'model_type' => 'App\Models\User',
                  'model_id' => $user->id,

              ]);
        });

        return redirect('/dashboard');
    }

    function tenantDelete($id)
    {
        Tenant::find($id)->delete();
        return redirect('/dashboard');
    }
}
