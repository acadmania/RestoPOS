<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">

            <div class="card" v-if="is('Super Admin') || is('Admin')">
                <form @submit.prevent="create()">

              <div class="card-header">
                <h3 class="card-title">Add Product Purchase</h3>
                <div class="card-tools">
                <div class="form-group">
                  <label>Date/Time</label>
                <!-- <date-picker v-model="form.datetime" placeholder="OPTIONAL" type="datetime" format="DD-MM-YYYY h:m:s A" :use12h="true"></date-picker>-->
                <input v-model="form.datetime" placeholder="OPTIONAL" type="date" required>
                </div>
              </div>
              </div>
              
              <div class="card-body">

                <div class="row">
                <div class="col-4">
									<div class="form-group">
											<label>Supplier</label>
                      <select class="form-control" @change="form.items=[]" v-model="form.supplier_id">
                        <option
                            v-for="(sup,index) in suppliers" :key="index"
                            :value="index">{{ sup }}</option>
                      </select>
                      <has-error :form="form" field="supplier_id"></has-error>
									</div>
                  </div>


                  <div class="col-4" >
  									<div class="form-group">
  											<label>Choose Location</label>
                        <select required class="form-control"  v-model="form.location_id">
                          <option
                              v-for="(loc,index) in locations" :key="index"
                              :value="index">{{ loc }}</option>

                        </select>
                        <has-error :form="form" field="location_id"></has-error>
  									</div>
                    </div>


                    <div class="col-4">
                      <div class="form-group">
                          <label>Account</label>
                          <select name="account" class="form-control"  v-model="form.account_id" >
                            <option
                              v-for="(acc,index) in accounts" :key="index"
                              :value="index">{{ acc }}</option>

                          </select>
                          <has-error :form="form" field="account_id"></has-error>
                      </div>
                      </div>
                  </div>


									<div class="card">
              <div class="card-header">
								<div class="form-group">

                  <v-select :options="options" ref="searchBox"  v-model="selected" :filterable="false"  @option:selected="selectedMat" @search="fetchOptions" />

                </select>
								</div>

              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0" style="height: 300px;">
                <table class="table table-head-fixed text-nowrap" id="items">
                  <thead>
                    <tr>
                      <th>Details</th>
                      <th>Cost</th>
                      <th>Tax</th>
                      <th>Quantity</th>
                      <th>Discount</th>
                      <th>Subtotal</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item,index) in form.items" :key="item.index">
                     <td><span style="cursor: pointer;" @click="productModal(item,index,item.gst_percentage)"><u>{{item.product_name}}</u></span>
                       <select disabled style="width:50%" v-if="item.units && item.units.length > 0" v-model="item.purchase_unit">
                         <option
                             v-for="(u,index) in item.units" :key="u.id"
                             :value="u.id"
                             >{{ u.name }}</option>
                       </select></td>
                     <td ><input style="width:50%" v-model.number="item.cost" @change="updateTotal" type="text"></td>
                     <td ><input style="width:50%"  v-model.number="item.tax_total=item.cost*item.gst_percentage/100" @change="updateTotal" type="text"></td>
                     <td ><input style="width:50%"  v-model.number="item.quantity" @change="updateTotal" type="text"></td>
                     <td ><input style="width:50%"  v-model.number="item.discount" @change="updateTotal" type="text"></td>
                    <td ><input style="width:50%"  readonly v-model.number="item.subtotal=(parseFloat(item.cost)+parseFloat(item.tax_total))*item.quantity" type="text"></td>
                     <td>
                       <a href="#" @click="RemoveItem(index)">
                           <i class="fa fa-trash red"></i>
                       </a>
                     </td>
                   </tr>
                   <tr style="background-color:#cacaca">
                     <td>TOTALS: </td>
                     <td><input style="width:50%"  readonly v-model.number="form.total_cost" type="text"></td>
                     <td><input style="width:50%" readonly v-model.number="form.total_taxtotal" type="text"></td>
                     <td><input style="width:50%"  readonly v-model.number="form.total_quantity" type="text"></td>
                     <td><input style="width:50%"   v-model.number="form.total_discount" type="text"></td>
                     <td><input style="width:50%"  readonly v-model.number="form.total_subtotal" type="text"></td>
                     <td></td>
                   </tr>
                   <tr>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td>Tax Total: </td>
                     <td><input style="width:50%"  @change="grandTaxTotalChange"  v-model.number="form.grand_tax_total" type="text"></td>
                    </tr>
                    <tr>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td>Discount Total: </td>
                      <td><input style="width:50%" @change="grandDiscountChange" v-model.number="form.grand_discount" type="text"></td>
                     </tr>
                   <tr>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td>Shipping & Delivery: </td>
                     <td><input style="width:50%" @change="updateTotal" v-model.number="form.shipping" type="text"></td>
                    </tr>
                   <tr>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td>Grand Total: </td>
                     <td><input style="width:50%" readonly v-model.number="form.grand_total" type="text"></td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <div class="row">
                    <div class="col-3">
                      <div class="form-group">
    											<label>Order Status</label>
                          <select required class="form-control"  v-model="form.order_status">
                            <option value="Ordered">Ordered</option>
                            <option value="Received">Received</option>
                          </select>
                          <has-error :form="form" field="location_id"></has-error>
    									</div>
    									</div>
                      <div class="col-3">
                      <div class="form-group">
    											<label>Payment Status</label>
                          <select required class="form-control"  v-model="form.payment_status">
                            <option value="Unpaid">Unpaid</option>
                            <option value="Paid">Fully Paid</option>
                            <option value="Partially Paid">Partially Paid</option>
                          </select>
                          <has-error :form="form" field="location_id"></has-error>
    									</div>
                    </div>
                    <div class="col-3">
                      <span v-if="form.payment_status === 'Partially Paid'">
                        <div class="form-group">
      											<label>Amount Paid</label>
                            <input required v-model.number="form.amount_paid" type="text">
      									</div>
                      </span>

                    </div>
                    <div class="col-3">
                      	<label></label>
                          <button style="width:100%" type="submit" class="btn btn-primary" :disabled="isDisabled">CREATE PURCHASE</button>
                    </div>

                    <div class="col-3">

                        <div class="form-group">
      											<label>Invoice Number</label>
                            <input  v-model.number="form.note" type="text">
      									</div>


                    </div>

                  </div>
                </div>
            </div>
                </div>
              </form>
            </div>
          </div>
        </div>


        <div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="paymentModal" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" >Update Product</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- <form @submit.prevent="createUser"> -->

                <form @submit.prevent="updateProduct">
                    <div class="modal-body">
                      <div class="row">




                      <div class="form-group col-md-6">
                          <label>Cost</label>
                          <input class="form-control" v-model="itemForm.cost" type="text">
                      </div>

                      <div class="form-group col-md-6" v-if="itemForm.gst_percentage !== '' && itemForm.gst_percentage !== null">
                          <label>GST Included / Exculded</label>
                          <select required class="form-control"  v-model="itemForm.cost_include_gst">
                            <option></option>
                            <option value="0">Excluding GST</option>
                            <option value="1">Including GST</option>
                          </select>
                      </div>

                      <div class="form-group col-md-6">
                          <label>Price <span v-if="itemForm.sale_unit_name"> (per {{itemForm.sale_unit_name}})</span> </label>
                          <input class="form-control" v-model="itemForm.price" type="text">
                      </div>

                      <div class="form-group col-md-6" v-if="itemForm.gst_percentage !== '' && itemForm.gst_percentage !== null">
                          <label>GST Included / Exculded</label>
                          <select required class="form-control"  v-model="itemForm.price_include_gst">
                            <option></option>
                            <option value="0">Excluding GST</option>
                            <option value="1">Including GST</option>
                          </select>
                      </div>

                      <div class="form-group col-md-12">
                          <label>Sale Price <span v-if="itemForm.sale_unit_name"> (per {{itemForm.sale_unit_name}})</span></label>
                          <input class="form-control" v-model="itemForm.sale_price" type="text">
                      </div>


                        <div class="form-group col-md-12">
      											<label>batch</label>
                            <input class="form-control" v-model="itemForm.batch" type="text">
      									</div>
                        <div class="form-group col-md-12">
      											<label>Code</label>
                            <input class="form-control" v-model="itemForm.code" type="text">
      									</div>
                        <div class="form-group col-md-12">
      											<label>Expiry Date</label>
                            <input class="form-control" v-model="itemForm.expiry_date" type="date">
      									</div>


                        <input type="hidden" :name="itemForm.index"  :value="itemForm.index">

                    </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                        <button  type="submit" class="btn btn-primary">Update</button>
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
                isDisabled : false,
                purchase_id: null,
                options: [],
                form: new Form({
                  datetime: '',
                  amount_paid: null,
                  purchase_id: null,
                  items: [],
                  supplier_id: null,
                  account_id: null,
                  total_subtotal: 0,
                  total_cost: 0,
                  total_taxtotal: 0,
                  total_quantity: 0,
                  total_discount:0,
                  shipping: 0,
                  grand_total: 0,
                  grand_discount: 0,
                  grand_tax_total: 0,
                }),
                itemForm: new Form({

                  batch: null,
                  code: null,
                  expiry_date: null,
                  cost: null,
                  price: null,
                  sale_price: null,
                  cost_include_gst: null,
                  price_include_gst: null,
                  gst_percentage: null,
                  index:null,
                  sale_unit_name:null,
                }),
                suppliers: [],
                locations: [],
                accounts: [],
                selected: null,
                units: [],
    }

          },


          methods: {

            updateProduct()
            {
              this.form.items[this.itemForm.index].batch =this.itemForm.batch
              this.form.items[this.itemForm.index].expiry_date =this.itemForm.expiry_date
              this.form.items[this.itemForm.index].cost =this.itemForm.cost
              this.form.items[this.itemForm.index].code =this.itemForm.code
              this.form.items[this.itemForm.index].price =this.itemForm.price
              this.form.items[this.itemForm.index].sale_price =this.itemForm.sale_price
              this.form.items[this.itemForm.index].price_include_gst =this.itemForm.price_include_gst
              this.form.items[this.itemForm.index].gst_percentage =this.itemForm.gst_percentage
              this.form.items[this.itemForm.index].cost_include_gst =this.itemForm.cost_include_gst
              $('#productModal').modal('hide');

            },
            productModal(item,index,gst_percentage)
            {

            this.itemForm.reset();
              $('#productModal').modal('show');
              this.itemForm.fill(item);
              this.itemForm.index = index;
              this.itemForm.gst_percentage = gst_percentage;
            },
            loadUnits(){
                axios.get("/api/product/unit/list").then(({ data }) => (this.units = data.data));
            },

            grandTaxTotalChange(){
              for(var i=0; i<this.form.items.length; i++)
              {
                this.form.items[i].tax_total = 0;
              }
              this.form.total_taxtotal = 0;
              this.form.grand_total = this.form.total_subtotal+this.form.grand_tax_total+this.form.shipping-this.form.grand_discount;
            },
            grandDiscountChange(){
              for(var i=0; i<this.form.items.length; i++)
              {
                this.form.items[i].discount = 0;
              }
              this.form.total_discount = 0;
              this.form.grand_total = this.form.total_subtotal+this.form.grand_tax_total+this.form.shipping-this.form.grand_discount;
            },

            RemoveItem(id)
            {
              this.form.items.splice(id, 1);
              this.updateTotal();
            },
            selectedMat(selectedOption)
            {
              if(this.form.supplier_id !== null){
              let itemExist = false;
              if(this.form.items)
              this.form.items.forEach(i=>{
          if(i.product_id == selectedOption.code)
          {
            i.quantity = parseInt(i.quantity)+1;
            itemExist = true;
          }
        });
        if(itemExist == false)
        {

          axios.get("/api/purchase/product/get/"+selectedOption.code+"/"+this.form.supplier_id+"/"+this.form.location_id).then(({ data }) => (

            this.form.items.push({
              product_name: data[1].name,
              hsn: data[1].hsn,
              cost: data[0].cost,
              price: data[0].price,
              tax_total: data[0].tax_total,
              gst_percentage: data[0].gst_percentage,
              sale_price: data[0].sale_price,
              cost_include_gst: data[0].cost_include_gst,
              price_include_gst: data[0].price_include_gst,
              batch:data[0].batch,
              code:data[0].code,
              expiry_date:data[0].expiry_date,
              discount: 0,
              quantity: 1,
              product_id:data[1].id,
              purchase_unit:data[1].purchase_unit ? data[1].purchase_unit.id : null,
              sale_unit_name:data[1].sale_unit ? data[1].sale_unit.name : null,
              sale_unit:data[1].sale_unit ? data[1].sale_unit.id : null,
              units: data[0].units,
              unit_table: data[1].units,




            })


          ));

        }

      }
      else
      {
        alert("please select supplier first");
      }
              this.selected = null;



            },
            totalTaxUpdate()
            {

            },
            updateTotal()
            {

                this.form.total_subtotal = this.form.items.reduce((total, obj) => parseFloat(obj.subtotal) + total,0);
                this.form.total_taxtotal = this.form.items.reduce((total, obj) => parseFloat(obj.tax_total) + total,0);
                this.form.total_quantity = this.form.items.reduce((total, obj) => parseFloat(obj.quantity) + total,0);
                this.form.total_cost = this.form.items.reduce((total, obj) => parseFloat(obj.cost) + total,0);
                this.form.total_discount = this.form.items.reduce((total, obj) => parseFloat(obj.discount) + total,0);
                this.form.grand_discount = this.form.total_discount;
                this.form.grand_tax_total = this.form.items.reduce((total, obj) => parseFloat(obj.tax_total*obj.quantity) + total,0);
                this.form.grand_total = this.form.total_subtotal+this.form.shipping-this.form.grand_discount;
            },
            loadSuppliers(){
                axios.get("/api/supplier/list").then(({ data }) => (this.suppliers = data.data));
            },
            loadLocations(){
                axios.get("/api/location/list").then(({ data }) => (this.locations = data.data,
                this.form.location_id = data.settings.location_id));
            },

            loadAccounts(){
                axios.get("/api/account/list").then(({ data }) => (this.accounts = data.data,
                this.form.account_id = data.settings.account_id));
            },
            loadPurchase(){
              if(this.$route.params.id)
              {


                axios.get("/api/purchase/get/"+this.$route.params.id).then(({ data }) => (
                  this.form.items = data.data[1],
                  this.form.supplier_id = data.data[0].supplier_id,
                  this.form.purchase_id = data.data[0].id,
                  this.form.location_id = data.data[0].location_id
                ));
                }
            },
            fetchOptions (search, loading) {
              if(search.length) {
              loading(true);
              this.search(loading, search, this);
            }
            },
            search: _.debounce((loading, search, vm) => {
              axios.get("/api/purchase/product/search/"+search).then(
                ({ data }) => (vm.options = data)
              );
              loading(false);
    }, 350),
            create(){
              this.isDisabled = true;
                if(
                  (this.form.account_id !== null && this.form.payment_status === 'Partially Paid')
              || (this.form.account_id !== null && this.form.payment_status === 'Paid')
              || (this.form.account_id === null && this.form.payment_status === 'Unpaid')
              || (this.form.account_id !== null && this.form.payment_status === 'Unpaid')
            )
                {
                this.form.post('/api/product/purchase')
                .then((response)=>{


                    Toast.fire({
                            icon: 'success',
                            title: response.data.message
                    });

                    this.$Progress.finish();
                    if(this.form.order_status == 'Ordered')
                    window.open(`/purchase/order/print/${response.data.data.id}`);
                    //this.$router.go();
                    this.$router.push('/product/purchases');
                })
                .catch(()=>{
                    Toast.fire({
                        icon: 'error',
                        title: 'Some error occured! Please try again'
                    });
                })
              }
              else
              {
                alert("choose the account please");
              }

            },

          },
          mounted()
          {

          this.$watch('form.items', function () {
   this.updateTotal();
 }, {deep:true})
          },
            created() {
                this.loadPurchase();
                this.$Progress.start();
                //this.load();
                this.loadSuppliers();
                this.loadAccounts();
                this.loadLocations();

                this.loadUnits();
                this.$Progress.finish();

            },

        }
    </script>
