<?php

namespace App\Http\Controllers\API\V1;
use App\Http\Controllers\Controller;
use App\Models\Kitchen;
use Illuminate\Http\Request;
use Auth;
class KitchenController extends BaseController
{
    protected $location = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Kitchen $location)
    {
        $this->middleware('auth:api');
        $this->location = $location;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $factories = $this->location->latest()->paginate(10);
        return $this->sendResponse($factories, 'Kitchen list');
    }

    public function list()
    {
        $factories = $this->location->pluck('name', 'id');

        return $this->sendResponse($factories, 'Kitchen list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $location = $this->location->create([
            'name' => $request->get('name'),
            'kot_printer' => $request->get('kot_printer'),
            'location_id' => $request->get('location_id'),
            'code' => $request->get('code'),


        ]);

        return $this->sendResponse($location, 'Kitchen Created Successfully');
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
            'kot_printer' => $request->get('kot_printer'),
            'location_id' => $request->get('location_id'),
            'code' => $request->get('code'),

        ]);


        return $this->sendResponse($location, 'Kitchen Information has been updated');
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

        return $this->sendResponse($location, 'Kitchen has been Deleted');
    }
}
