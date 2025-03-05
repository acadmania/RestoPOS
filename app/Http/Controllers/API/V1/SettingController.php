<?php

namespace App\Http\Controllers\API\V1;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Encryption\Encrypter;
use Illuminate\Contracts\Encryption\DecryptException;
use Carbon\Carbon;
class SettingController extends BaseController
{

    public function licenseUpdate(Request $request)
    {
        $setting = Setting::first();
        $setting->license = $request->license;
        $setting->save();
        return redirect()->back();
    }
    public function licenseCheck()
    {
        $setting = Setting::first();





        $key = 'U2x2QdvosFTtk5nL0ejrKqLFP1tUDtSt';
    if($setting->license !== null)
    {
        try {
            $encrypter = new Encrypter(
                key: $key,
                cipher: config('app.cipher'),
            );
            return Carbon::parse(Carbon::parse($encrypter->decrypt($setting->license)));
        } catch (DecryptException $e) {
            return null;
        }



    }
    else
    {
        return null;
    }

    }


    // public function autoRefresh($status)
    // {
    //   $setting = Setting::first();
    //   if($status == "true")
    //   $setting->auto_refresh = 1;
    //   else
    //   $setting->auto_refresh = 0;
    //   $setting->save();
    // }
    public function update(Request $request)
    {
      $setting = Setting::first();
      if(!$setting)
      {
        $setting = new Setting;
      }
      $setting->business_name = $request->business_name;

      $setting->gst_number = $request->gst_number;
      $setting->tagline = $request->tagline;
      $setting->is_direct_file_print = $request->is_direct_file_print;
      $setting->pos_all_products = $request->pos_all_products;
      $setting->reset_print_number_daily = $request->reset_print_number_daily;
      $setting->address = $request->address;
      $setting->phone = $request->phone;
      $setting->account_id = $request->account_id;
      if($request->customer_id == "null")
      $setting->customer_id = null;
      else
      $setting->customer_id = $request->customer_id;
      $setting->table_id = $request->table_id;
      $setting->print_format = $request->print_format;
      $setting->location_id = $request->location_id;
      $setting->amountpoint = $request->amountpoint;
      $setting->print_footer_message = $request->print_footer_message;
      $setting->kot_printer = $request->kot_printer;

      $setting->pos_default_payment_method = $request->pos_default_payment_method;
      $setting->pos_default_order_type = $request->pos_default_order_type;
      $setting->pos_post_product_add_action = $request->pos_post_product_add_action;
      $setting->license = $request->license;


      if($request->gst_summary)
      $setting->gst_summary = 1;
      else
        $setting->gst_summary = 0;

      if($request->pos_auto_fill_cash_amount)
      $setting->pos_auto_fill_cash_amount = 1;
      else
        $setting->pos_auto_fill_cash_amount = 0;

      if($request->each_kot_new_order)
    $setting->each_kot_new_order = 1;
    else
      $setting->each_kot_new_order = 0;

      if($request->all_orders_kot)
    $setting->all_orders_kot = 1;
    else
      $setting->all_orders_kot = 0;

      if($request->pre_order)
    $setting->pre_order = 1;
    else
      $setting->pre_order = 0;
      if($request->supplier_dine_in)
      $setting->supplier_dine_in = 1;
      else
        $setting->supplier_dine_in = 0;





      $setting->currency = $request->currency;
      if($request->file)
      {
        $newPath = $request->file->store('','public');
        $setting->logo = $newPath;
      }

      $setting->save();

    }

    public function load()
    {
        $setting = Setting::first();
        return $setting;
    }
    public function get()
    {
        $setting = Setting::first();
        return $setting;
    }
}
