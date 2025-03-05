<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">

            <div class="card" v-if="is('Super Admin') || is('Admin')">
                <form @submit.prevent="create()">

              <div class="card-header">
                <h3 class="card-title">Add Sale</h3>
              </div>
              <div class="card-body">

                <div class="row">
                <div class="col-4">
									<div class="form-group">
											<label>Customer</label>
                      <select required class="form-control" v-model="form.customer_id">
                        <option
                            v-for="(cus,index) in customers" :key="index"
                            :value="index">{{ cus }}</option>
                      </select>
                      <has-error :form="form" field="customer_id"></has-error>
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
                      <th>Rate</th>
                      <th>Discount</th>
                      <th>Price</th>
                      <th>Tax</th>
                      <th>Quantity</th>
                      <th>Subtotal</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item,index) in form.items" :key="item.index">
                     <td>{{item.product_name}}</td>
                     <td ><input style="width:50%" v-model.number="item.rate" @change="updateTotal" type="text"></td>
                     <td ><input style="width:50%"  v-model.number="item.discount" @change="updateTotal" type="text"></td>
                     <td ><input style="width:50%"  v-model.number="item.price=item.rate-item.discount" @change="updateTotal" type="text"></td>
                     <td ><input style="width:50%"  v-model.number="item.tax_total" @change="updateTotal" type="text"></td>
                     <td ><input style="width:50%"  v-model.number="item.quantity" @change="updateTotal" type="text"></td>
                    <td ><input style="width:50%"  readonly v-model.number="item.subtotal=item.price*item.quantity" type="text"></td>
                     <td>
                       <a href="#" @click="RemoveItem(index)">
                           <i class="fa fa-trash red"></i>
                       </a>
                     </td>
                   </tr>
                   <tr style="background-color:#cacaca">
                     <td>TOTALS: </td>
                     <td><input style="width:50%"  readonly v-model.number="form.total_rate" type="text"></td>
                     <td><input style="width:50%"   v-model.number="form.total_discount" type="text"></td>
                     <td><input style="width:50%"  readonly v-model.number="form.total_price" type="text"></td>
                     <td><input style="width:50%"  v-model.number="form.total_taxtotal" type="text"></td>
                     <td><input style="width:50%"  readonly v-model.number="form.total_quantity" type="text"></td>

                     <td><input style="width:50%"  readonly v-model.number="form.total_subtotal" type="text"></td>
                     <td></td>
                   </tr>
                   <tr>
                     <td></td>
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
                      <td></td>
                      <td>Discount Total: </td>
                      <td><input style="width:50%" @change="grandDiscountChange" v-model.number="form.grand_discount" type="text"></td>
                     </tr>
                   <tr>
                     <td></td>
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
                            <option value="Completed">Completed</option>
                          </select>
                          <has-error :form="form" field="location_id"></has-error>
    									</div>
                      <div class="form-group">
    											<label>IGST</label>
                          <input v-model="form.igst" type="checkbox" value="1" name="igst" >
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

                      <div class="col-3" v-if="form.payment_status === 'Partially Paid'">
                          <div class="form-group">
        											<label>Amount Paid</label>
                              <input required v-model.number="form.amount_paid" type="text">
        									</div>
                      </div>



                    <div class="col-3">
                      	<label></label>
                          <button style="width:100%" type="submit" class="btn btn-primary" :disabled="isDisabled">CREATE SALE</button>
                    </div>


                  </div>
                </div>
            </div>
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
                sale_id: null,
                options: [],
                form: new Form({
                  amount_paid: null,
                  items: [],
                  supplier_id: null,
                  account_id: null,
                  location_id: null,
                  total_subtotal: 0,
                  total_price: 0,
                  total_taxtotal: 0,
                  total_quantity: 0,
                  total_discount:0,
                  shipping: 0,
                  grand_total: 0,
                  grand_discount: 0,
                  grand_tax_total: 0,
                }),
                customers: [],
                locations: [],
                accounts: [],
                selected: null,
                settings: null,

    }

          },


          methods: {
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
          axios.get("/api/sale/product/get/"+selectedOption.code+"/"+this.form.customer_id).then(({ data }) => (

            this.form.items.push({
              product_name: data[1].name,
              product_id: data[1].id,
              tax_total: data[0].tax_total,
              price: data[0].price,
              rate: data[0].rate,
              hsn: data[1].hsn,
              code: data[1].code,
              gst_percentage: data[1].gst_percentage,
              discount: data[0].discount,
              cost: data[0].cost,
              quantity: 1,


            })


          ))
        }

              this.selected = null;
              this.updateTotal();

            },

            updateTotal()
            {
              /*this.form.items.forEach(i=>{
                if(i.gst_percentage)
                {

                  i.tax_total=  i.price*i.gst_percentage/100;

                }
              });*/
                this.form.total_subtotal = this.form.items.reduce((total, obj) => parseFloat(obj.subtotal) + total,0);
                this.form.total_taxtotal = this.form.items.reduce((total, obj) => parseFloat(obj.tax_total) + total,0);
                this.form.total_quantity = this.form.items.reduce((total, obj) => parseFloat(obj.quantity) + total,0);
                this.form.total_rate = this.form.items.reduce((total, obj) => parseFloat(obj.rate) + total,0);
                this.form.total_price = this.form.items.reduce((total, obj) => parseFloat(obj.price) + total,0);
                this.form.total_discount = this.form.items.reduce((total, obj) => parseFloat(obj.discount*obj.quantity) + total,0);
                this.form.grand_discount = this.form.total_discount;
                this.form.grand_tax_total = this.form.items.reduce((total, obj) => parseFloat(obj.tax_total*obj.quantity) + total,0);
                this.form.grand_total = parseFloat(this.form.total_subtotal)+parseFloat(this.form.shipping);
            },
            loadCustomers(){
                axios.get("/api/customer/list").then(({ data }) => (this.customers = data.data));
            },
            loadLocations(){
                axios.get("/api/location/list").then(({ data }) => (this.locations = data.data,
                this.settings = data.settings,
                this.form.location_id = data.settings.location_id));
            },

            loadAccounts(){
                axios.get("/api/account/list").then(({ data }) => (this.accounts = data.data,
                this.form.account_id = data.settings.account_id));
            },
            fetchOptions (search, loading) {
              if(search.length) {
              loading(true);
              this.search(loading, search, this);
            }
            },
            search: _.debounce((loading, search, vm) => {
              axios.get("/api/product/search/"+search).then(
                ({ data }) => (vm.options = data)
              );
              loading(false);
    }, 350),
            create(){

                this.isDisabled = true;
              this.updateTotal();
                if(
                  (this.form.account_id !== null && this.form.payment_status === 'Partially Paid')
              || (this.form.account_id !== null && this.form.payment_status === 'Paid')
              || (this.form.account_id === null && this.form.payment_status === 'Unpaid')
              || (this.form.account_id !== null && this.form.payment_status === 'Unpaid')
            )
                {
                this.form.post('/api/sale')
                .then((response)=>{


                    Toast.fire({
                            icon: 'success',
                            title: response.data.message
                    });

                    this.$Progress.finish();
                    //this.$router.push('/sales');
                    //this.$router.push(`/sale/print/${response.data.data.id}`);

                    if (this.settings.print_format == "ashok_leyland") {

                  this.$router.push(`/sale/print_ashok/${response.data.data.id}`);
                }
                else if (this.settings.print_format == "army_canteen") {
                    this.$router.push(`/sale/print_army/${response.data.data.id}`);
                }
                else if (this.settings.print_format == "super_market") {
                    this.$router.push(`/sale/print_super_market/${response.data.data.id}`);
                }
                      this.$router.go();
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
 },  {deep:true})
          },
            created() {

                this.$Progress.start();
                //this.load();
                this.loadCustomers();
                this.loadAccounts();
                this.loadLocations();

                this.$Progress.finish();
            },

        }



    </script>
