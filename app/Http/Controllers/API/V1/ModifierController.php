<?php

namespace App\Http\Controllers\API\V1;
use App\Http\Controllers\Controller;
use App\Models\Modifier;
use Illuminate\Http\Request;
use Auth;
class ModifierController extends BaseController
{
    protected $item = '';

    public function __construct(Modifier $item)
    {
        $this->middleware('auth:api');
        $this->item = $item;
    }
    public function search($search)
    {
        $items = $this->item->where('name', 'like', '%'.$search.'%')
        ->orwhere('code', 'like', '%'.$search.'%')

        ->get();
        foreach ($items as $i) {
          $item_array[] = [
            'label' => $i->name,
            'code' => $i->id,
    ];
        }
        return $item_array;


    }
    public function getModifier($modifier_id)
    {
      $modifier = Modifier::find($modifier_id);
      return $this->sendResponse($modifier, 'Modifier');

    }
    public function index()
    {
        $items = $this->item->latest()->paginate(10);
        return $this->sendResponse($items, 'Food brand list');
    }

    public function list()
    {
        $items = $this->item->pluck('name', 'id');

        return $this->sendResponse($items, 'Food brand list');
    }

    public function store(Request $request)
    {

      $item = $this->item->create([
            'name' => $request->get('name'),
            'code' => $request->get('code'),
            'price' => $request->get('price'),

        ]);

        return $this->sendResponse($item, 'Brand Created Successfully');
    }

    public function update(Request $request, $id)
    {
        $item = $this->item->findOrFail($id);

        $item->update([
            'name' => $request->get('name'),
            'code' => $request->get('code'),
            'price' => $request->get('price'),

        ]);

        return $this->sendResponse($item, 'Brand Information has been updated');

    }

    public function destroy($id)
    {
      $item = $this->item->findOrFail($id);

      $item->delete();

        return $this->sendResponse($item, 'Brand has been Deleted');
    }
}
