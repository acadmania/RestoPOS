<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">

            <div class="card" v-if="is('Super Admin')">
                <form @submit.prevent="update()">

              <div class="card-header">
                <h3 class="card-title">Settings</h3>
              </div>
              <div class="card-body">

                  <div class="row">
                    <div class="col-6">
                    <div class="form-group">
  											<label>Business Name (Bill Header)</label>
                        <input type="text" required v-model="business_name" class="form-control">
  									</div>
                  </div>
                    <div class="col-6">
                    <div class="form-group">
  											<label>Tagline</label>
                        <input type="text" v-model="tagline" class="form-control">
  									</div>
                  </div>
                    <div class="col-6">
                    <div class="form-group">
  											<label>GST Number</label>
                        <input type="text"  v-model="gst_number" class="form-control">
  									</div>
                  </div>
                    <div class="col-6">
                      <div class="form-group">
    											<label>Customer</label>
                          <select required class="form-control" v-model="customer_id">
                            <option value="null">NULL</option>
                            <option
                                v-for="(cus,index) in customers" :key="index"
                                :value="index">{{ cus }}</option>
                          </select>
                          <has-error :form="form" field="customer_id"></has-error>
    									</div>
                  </div>
                  </div>

                  <div class="row">
                    <div class="col-6">
                    <div class="form-group">
  											<label>Address</label>
                        <input type="text" v-model="address" class="form-control">
  									</div>
                  </div>
                    <div class="col-6">
                    <div class="form-group">
  											<label>Phone Numbers</label>
                        <input type="text" v-model="phone" class="form-control">
  									</div>
                  </div>
                  </div>


                <div class="row">

                  <div class="col-6" >
  									<div class="form-group">
  											<label>Print Format</label>
                        <select  class="form-control"  v-model="print_format">
                          <option value="">Nothing</option>
                          <option value="super_market">Restaurant Style 1</option>
                          <option value="restaurant_style_2">Restaurant Style 2</option>
                          <option value="restaurant_style_3">Restaurant Style 3</option>
                          <option value="restaurant_style_4">Restaurant Style 4</option>
                          <option value="restaurant_style_5">Restaurant Style 5</option>
                          <option value="bakery_style_1">Bakery Style 1</option>
                        </select>

  									</div>

                    </div>


                    <div class="col-6" >

                    <div class="form-group">
  											<label>Show MRP in Print</label>
                        <select  class="form-control"  v-model="mrp">
                          <option value="0">No</option>
                          <option value="1">Yes</option>

                        </select>

  									</div>
  									</div><div class="col-6" >

                    <div class="form-group">
  											<label>POS add All products</label>
                        <select  class="form-control"  v-model="pos_all_products">
                          <option value="0">No</option>
                          <option value="1">Yes</option>

                        </select>

  									</div>
  									</div>

                  <div class="col-6" >
  									<div class="form-group">
  											<label>Default Location</label>
                        <select  class="form-control"  v-model="location_id">
                          <option></option>
                          <option
                              v-for="(loc,index) in locations" :key="index"
                              :value="index">{{ loc }}</option>

                        </select>

  									</div>
                    </div>
                    <div class="col-6" >
    									<div class="form-group">
    											<label>Default Table</label>
                          <select  class="form-control"  v-model="table_id">
                            <option></option>
                            <option
                                v-for="(loc,index) in tables" :key="index"
                                :value="index">{{ loc }}</option>

                          </select>

    									</div>
                      </div>
                      <div class="col-6">
                      <div class="form-group">
    											<label>KOT Printer Name</label>
                          <input type="text" v-model="kot_printer" class="form-control">
    									</div>
                    </div><div class="col-6">
                        <div class="form-group">
  											<label>Is Direct File Print?</label>
                        <select  class="form-control"  v-model="is_direct_file_print">
                          <option value="0">No</option>
                          <option value="1">Yes</option>

                        </select>

  									</div>
                                      <div class="form-group">
  											<label>Reset Print Number Daily?</label>
                        <select  class="form-control"  v-model="reset_print_number_daily">
                          <option value="0">No</option>
                          <option value="1">Yes</option>

                        </select>

  									</div>
                    </div>
                  <div class="col-6" >
  									<div class="form-group">
  											<label>Default Account</label>
                        <select  class="form-control"  v-model="account_id">
                          <option></option>
                          <option
                              v-for="(acc,index) in accounts" :key="index"
                              :value="index">{{ acc }}</option>

                        </select>

  									</div>
                    </div>
                    <div class="col-6" >
  									<div class="form-group">
  											<label>Default Payment Method</label>
                        <select  class="form-control"  v-model="pos_default_payment_method">
                            <option :value="null" selected="" >Nothing</option>
                  <option value="Cash">Cash</option>
                  <option value="Unpaid">Unpaid</option>
                  <option value="Card">Card</option>
                  <option value="Online">Online</option>
                  <option value="GPay">GPay</option>
                  <option value="Phonepe">Phonepe</option>
                  <option value="Amazon Pay">Amazon Pay</option>


                        </select>

  									</div>
                    </div>


                    <div class="col-6" >
  									<div class="form-group">
  											<label>Default Order Type</label>
                        <select  class="form-control"  v-model="pos_default_order_type">
                            <option :value="null" selected="" >Nothing</option>
                  <option value="d">DINE-IN</option>
                  <option value="t">TAKEWAY</option>
                  <option value="s">DELIVERY</option>
                  <option value="b">PREBOOK</option>



                        </select>

  									</div>
                    </div>

                    <div class="col-6" >

<div class="form-group">
    <label>POS Action after adding a product</label>
    <select  class="form-control"  v-model="pos_post_product_add_action">

      <option value="New Row">Go to New Row</option>
          <option value="Quantity On Last Product">Go to Quantity On Last Product</option>


    </select>

</div>
</div>


                  </div>


                  <div class="row">
                    <div class="col-4">
                    <div class="form-group">
  											<label>Currency Symbol</label>
                        <input type="text"  v-model="currency" class="form-control">
  									</div>
                  </div>
                    <div class="col-4">
                      <br>
                      <div class="input-group">
                      <div class="custom-file">

                        <input type="file" class="custom-file-input" v-on:change="handleFileUpload()" ref="file" id="exampleInputFile">
                        <label class="custom-file-label" for="exampleInputFile">Choose Logo</label>
                      </div>

                    </div>
                    </div>
                    <div class="col-4">
                    <img :src="logo" width="200"/>
                  </div>
                  </div>
                  <div class="row">
                    <div class="col-3">
                    <div class="form-group">
  											<label>1 Rs. = How many Points</label>
                        <input type="text" v-model="amountpoint" class="form-control">
  									</div>
                  </div>
                  <div class="col-3">
                    <div class="form-check">

                        <input type="checkbox"  v-model="pos_auto_fill_cash_amount" class="form-check-input">
                        	<label class="form-check-label">POS Autofill Cash Amount</label>
  									</div>
                  </div>
                    <div class="col-3">
                    <div class="form-check">

                        <input type="checkbox"  v-model="gst_summary" class="form-check-input">
                        	<label class="form-check-label">Print GST Summary</label>
  									</div>
                  </div>
                    <div class="col-3">
                    <div class="form-check">

                        <input type="checkbox"  v-model="each_kot_new_order" class="form-check-input">
                        	<label class="form-check-label">Each KOT is New Order Bill</label>
  									</div>
                  </div>
                  <div class="col-3">
                  <div class="form-check">

                      <input type="checkbox"  v-model="all_orders_kot" class="form-check-input">
                        <label class="form-check-label">Print KOT for All Orders</label>
                  </div>
                </div>
                  <div class="col-3">
                  <div class="form-check">

                      <input type="checkbox"  v-model="pre_order" class="form-check-input">
                        <label class="form-check-label">Enable Pre-Booking Orders</label>
                  </div>
                </div>
                 <div class="col-3">
                  <div class="form-check">

                      <input type="checkbox"  v-model="supplier_dine_in" class="form-check-input">
                        <label class="form-check-label">Enable Supplier Dine In</label>
                  </div>
                </div>
                  </div>
                  <div class="row">
                    <div class="col-12">
                    <div class="form-group">
  											<label>Print Footer Message</label>
                        <input type="text" v-model="print_footer_message" class="form-control">
  									</div>
                  </div>

                  <div class="col-12">
                    <div class="form-group">
  											<label>License Key</label>
                        <input type="text" v-model="license" class="form-control">
  									</div>
                  </div>
                  </div>
                  <button class="btn btn-sm btn-primary" type="submit">SAVE</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
  </template>


  <script>

      export default {

          data () {
              return {

                consumption_id: null,
                is_direct_file_print: null,
                reset_print_number_daily: null,
                pre_order: null,
                supplier_dine_in: null,
                license: null,
                customers:null,
                tables:null,
                customer_id:null,
                options: [],
                 logo: null,

                locations: [],
                accounts: [],
              file: '',
              location_id: null,
              table_id: null,
              currency: null,
              mrp: null,
              account_id: null,
              sgst: null,
              cgst: null,
              pos_all_products: null,
              amountpoint: null,
              print_format: null,
              each_kot_new_order: null,
              all_orders_kot: null,
              kot_printer: null,
              print_footer_message: null,
              business_name: null,
              tagline:null,
              address: null,
              phone: null,
              gst_number: null,
              gst_summary: null,
              pos_default_payment_method: null,
              pos_default_order_type: null,
              pos_auto_fill_cash_amount: null,
              pos_post_product_add_action: null,

                selected: null
    }

          },


          methods: {
            scrollToTop() {
                window.scrollTo(0,0);
          },
            handleFileUpload(){
    this.file = this.$refs.file.files[0];

  },

            loadTables(){
                axios.get("/api/table/list").then(({ data }) => (this.tables = data.data));
            },
            loadLocations(){
                axios.get("/api/location/list").then(({ data }) => (this.locations = data.data));
            },
            loadAccounts(){
                axios.get("/api/account/list").then(({ data }) => (this.accounts = data.data));
            },
            loadCustomers(){
                axios.get("/api/customer/list").then(({ data }) => (this.customers = data.data));
            },
            load(){
                axios.get("/api/settings/get").then(({ data }) => (
                  this.business_name = data.business_name,
                  this.tagline = data.tagline,
                  this.currency = data.currency,
                  this.gst_number = data.gst_number,
                  this.is_direct_file_print = data.is_direct_file_print,
                  this.reset_print_number_daily = data.reset_print_number_daily,
                  this.table_id = data.table_id,
                  this.license = data.license,
                  this.sgst = data.sgst,
                  this.cgst = data.cgst,
                  this.each_kot_new_order = data.each_kot_new_order,
                  this.all_orders_kot = data.all_orders_kot,
                  this.address = data.address,
                  this.phone = data.phone,
                  this.account_id = data.account_id,
                  this.customer_id = data.customer_id,
                  this.location_id = data.location_id,
                  this.kot_printer = data.kot_printer,
                  this.pos_all_products = data.pos_all_products,
                  this.pre_order = data.pre_order,
                  this.supplier_dine_in = data.supplier_dine_in,
                  this.amountpoint = data.amountpoint,
                  this.print_format = data.print_format,
                  this.print_footer_message = data.print_footer_message,
                  this.gst_summary = data.gst_summary,
                  this.mrp = data.mrp,
                  this.pos_default_payment_method = data.pos_default_payment_method,
                  this.pos_default_order_type = data.pos_default_order_type,
                  this.pos_auto_fill_cash_amount = data.pos_auto_fill_cash_amount,
                  this.pos_post_product_add_action = data.pos_post_product_add_action,
                  this.logo = '/storage/'+data.logo
                ));
            },
            update(){
                let formData = new FormData();

                formData.append('file', this.file);
                formData.append('business_name', this.business_name);
                if(this.tagline)
                formData.append('tagline', this.tagline);
                if(this.gst_number)
                formData.append('gst_number', this.gst_number);
                if(this.is_direct_file_print)
                formData.append('is_direct_file_print', this.is_direct_file_print);
            if(this.pos_all_products)
                formData.append('pos_all_products', this.pos_all_products);
               if(this.reset_print_number_daily)
                formData.append('reset_print_number_daily', this.reset_print_number_daily);
                if(this.currency)
                formData.append('currency', this.currency);
                if(this.address)
                formData.append('address', this.address);
                if(this.phone)
                formData.append('phone', this.phone);
                if(this.each_kot_new_order)
                formData.append('each_kot_new_order', this.each_kot_new_order);
                if(this.all_orders_kot)
                formData.append('all_orders_kot', this.all_orders_kot);
                if(this.account_id)
                formData.append('account_id', this.account_id);
                if(this.license)
                formData.append('license', this.license);
                if(this.table_id)
                formData.append('table_id', this.table_id);
                if(this.kot_printer)
                formData.append('kot_printer', this.kot_printer);
                if(this.location_id)
                formData.append('location_id', this.location_id);
                if(this.customer_id)
                formData.append('customer_id', this.customer_id);

                if(this.pos_default_payment_method)
                formData.append('pos_default_payment_method', this.pos_default_payment_method);

                if(this.pos_default_order_type)
                formData.append('pos_default_order_type', this.pos_default_order_type);

                if(this.pos_auto_fill_cash_amount)
                formData.append('pos_auto_fill_cash_amount', this.pos_auto_fill_cash_amount);

                if(this.pos_post_product_add_action)
                formData.append('pos_post_product_add_action', this.pos_post_product_add_action);



                if(this.sgst)
                formData.append('sgst', this.sgst);
                if(this.cgst)
                formData.append('cgst', this.cgst);
                if(this.amountpoint)
                formData.append('amountpoint', this.amountpoint);
                if(this.print_format)
                formData.append('print_format', this.print_format);
                if(this.print_footer_message)
                formData.append('print_footer_message', this.print_footer_message);
                if(this.mrp)
                formData.append('mrp', this.mrp);
                if(this.gst_summary)
                formData.append('gst_summary', this.gst_summary);
                if(this.pre_order)
                formData.append('pre_order', this.pre_order);
                if(this.supplier_dine_in)
                formData.append('supplier_dine_in', this.supplier_dine_in);

                  axios.post('/api/settings/update', formData)

                .then((response)=>{


                    Toast.fire({
                            icon: 'success',
                            title: response.data.message
                    });

                    this.$Progress.finish();
                    this.scrollToTop();


                })
                .catch(()=>{
                    Toast.fire({
                        icon: 'error',
                        title: 'Some error occured! Please try again'
                    });
                })

            },

          },
            created() {

                this.$Progress.start();
                this.load();


                this.loadLocations();
                this.loadAccounts();
                this.loadCustomers();
                this.loadTables();

                this.$Progress.finish();
            },

        }
    </script>
