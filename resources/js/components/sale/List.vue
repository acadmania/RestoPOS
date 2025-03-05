<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">

            <div class="card" v-if="is('Super Admin') || is('Admin')">
              <div class="card-header">
                <h3 class="card-title">Sale List</h3>
                <input style="margin-left:0.5%; width:10%" type="text" v-model="searchText" name="name" placeholder="Search.." @keyup="search"  autocomplete="off">
                <!-- <input type="checkbox" @change="autoRefreshChange" v-model="settings.auto_refresh"> Auto Refresh -->
                <div class="card-tools" style="margin-right: 0.375rem">
                        <form @submit.prevent="filterSales()">
                                  <div class="row">

                                          <date-picker v-model="filterForm.dateFilter" type="date" placeholder="From To Date" range></date-picker>

                                          <input type="text" v-model="filterForm.customer" name="name" placeholder="Customer name" autocomplete="off">


                                        <select  v-model="filterForm.type" style="margin-left: 20px;">
                                          <option value="" disabled selected>Select Type</option>
                                          <option value="d">Dine-in</option>
                                          <option value="t">Takeaway</option>
                                          <option value="s">Delivery</option>
                                          <option value="b">Pre-Book</option>

                                        </select>
                                        <!-- <input type="text" style="margin-right:91px" v-model="" name="name" placeholder="Type" autocomplete="off"> -->


                                        <select  v-model="filterForm.payment_status" style="margin-left: 20px;">
                                          <option value="" disabled selected>Select Payment Status</option>
                                          <option value="Paid">Paid</option>
                                          <option value="Unpaid">Unpaid</option>
                                          <option value="Partially Paid">Partially Paid</option>
                                        </select>
                                        <!-- <input type="text" style="margin-right:91px" v-model="" name="name" placeholder="Type" autocomplete="off"> -->


                                          <button type="submit" class="btn btn-success">Filter</button>
                                          <a href="" @click="resetFilter()">
                                            <i class="fas fa-sync blue" aria-hidden="true"></i>
                                        </a>

                                  </div>
                    </form>
                <!-- <form @submit.prevent="filterSales()">
                                  <div class="row justify-content:space-between">

                                      <div class="col-5">
                                          <date-picker v-model="filterForm.dateFilter" type="date" placeholder="From To Date" range></date-picker>
                                      </div>
                                      <div class="col-3">
                                          <input type="text" v-model="filterForm.customer" name="name" placeholder="Customer name" autocomplete="off">
                                      </div>
                                      <div class="col-2">
                                        <select  v-model="filterForm.type" placeholder="Type" style="margin-left: 20px;">
                                          <option value="" disabled selected>Select Type</option>
                                          <option value="d">Dine-in</option>
                                          <option value="t">Takeaway</option>
                                          <option value="s">Delivery</option>
                                          <option value="b">Pre-Book</option>

                                        </select> -->
                                        <!-- <input type="text" style="margin-right:91px" v-model="" name="name" placeholder="Type" autocomplete="off"> -->
                                      <!-- </div>
                                     <div class="col-2">
                                        <select  v-model="filterForm.type" placeholder="Type" style="margin-left: 20px;">
                                          <option value="" disabled selected>Select Type</option>
                                          <option value="d">Dine-in</option>
                                          <option value="t">Takeaway</option>
                                          <option value="s">Delivery</option>
                                          <option value="b">Pre-Book</option>

                                        </select> -->
                                        <!-- <input type="text" style="margin-right:91px" v-model="" name="name" placeholder="Type" autocomplete="off"> -->
                                      <!-- </div>
                                      <div class="col-1">
                                          <button type="submit" class="btn btn-success">Filter</button>
                                      </div>
                                  </div>
                    </form> -->
                <!--  <button type="button" class="btn btn-sm btn-primary" @click="$router.push('/sale/add')">
                      <i class="fa fa-plus-square"></i>
                      Add New
                  </button>-->
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th v-if="settings.reset_print_number_daily">Daily Serial</th>
                      <th>Date</th>
                      <th>Type</th>
                      <th>Customer Name</th>
                      <th>Table Name</th>
                      <th>Total</th>
                      <th>Status</th>
                      <th>Payment Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="sale in sales.data" :key="sale.id">

                      <td style="padding:0">{{sale.id}}</td>
                      <td style="padding:0" v-if="settings.reset_print_number_daily">{{sale.serial}}</td>
                      <td style="font-size:10px;padding:0">{{sale.created_at | date}}</td>
                      <td style="padding:0"><span v-if="sale.type=='d'">Dine-In</span>
                        <span v-if="sale.type=='t'">Takeaway</span>
                        <span v-if="sale.type=='s'">Delivery</span>
                        <span v-if="sale.type=='b'">Pre-Book <br> {{sale.dateTime | date}}</span>
                      </td>
                      <td style="padding:0">{{sale.customer_name}}</td>
                      <td style="padding:0">{{sale.table_name}}</td>
                      <td style="padding:0"><b>{{sale.grand_total}}</b></td>
                      <td style="padding:0">{{sale.status}}</td>
                      <!-- <td style="padding:0">{{sale.payment_status}}</td> -->
                      <td>
                        <span style="background-color: green; color:white;padding:5px" v-if="sale.grand_total == sale.pay_statuses.reduce((total, obj) => parseFloat(obj.amount) + total,0)"> PAID</span>
                        <span  style="background-color: blue; color:white;padding:5px" v-else-if="Math.round(sale.grand_total) < Math.round(sale.pay_statuses.reduce((total, obj) => parseFloat(obj.amount) + total,0))">PAID MORE</span>
                        <span  style="background-color: red; color:white;padding:5px" v-else-if="0 == Math.round(sale.pay_statuses.reduce((total, obj) => parseFloat(obj.amount) + total,0))"> UNPAID</span>
                        <span   v-else-if="Math.round(sale.grand_total) > Math.round(sale.pay_statuses.reduce((total, obj) => parseFloat(obj.amount) + total,0))">
                            <span style="background-color: orange; color:white;padding:5px"> PAID LESS</span>

                            <br>
                            {{ (sale.grand_total - sale.pay_statuses.reduce((total, obj) => parseFloat(obj.amount) + total,0)).toFixed(3) }}
                        </span>
                      </td>
                      <td style="padding:0">

                        <div class="btn-group">
                    <button type="button" style="padding:0;height:28px"  class="btn btn-info">Action</button>
                    <button type="button" style="padding:0;height:28px" class="btn btn-info dropdown-toggle dropdown-icon" data-toggle="dropdown" aria-expanded="false">
                      <span class="sr-only">Toggle Dropdown</span>
                      <div class="dropdown-menu" role="menu" style="">

                        <a class="dropdown-item" href="#" @click.prevent="$router.push(`/sale/view/${sale.id}`)">View</a>
                        <a class="dropdown-item" v-if="is('Super Admin')" href="#" @click.prevent="RemoveItem(sale.id)">Delete</a>

                        <a class="dropdown-item" href="#" @click.prevent="PrintSale(sale.id)">Print</a>
                          <a class="dropdown-item" v-if="sale.status === 'Ordered'" href="#" @click.prevent="changeStatus(sale.id,'Ready')">Ready</a>
                          <a class="dropdown-item" v-if="sale.status === 'Ready'" href="#" @click.prevent="changeStatus(sale.id,'Completed')">Complete</a>
                          <a class="dropdown-item" v-if="sale.payment_status === 'Unpaid' || sale.payment_status === 'Partially Paid'" href="#" @click.prevent="paymentModal(sale.id,sale.cash_balance)">Record Payment</a>

                      </div>
                    </button>
                  </div>




                      <!--  /
                        <a href="#" @click="RemoveItem(purchase.id)">
                            <i class="fa fa-trash red"></i>
                        </a>-->
                      </td>
                    </tr>
                    <tr>
                        <!-- <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td> -->
                        <td v-if="!settings.reset_print_number_daily" colspan="6" style="text-align: end;">Total: {{ sales.data.reduce((total, obj) => parseFloat(obj.grand_total) + total,0)}} </td>
                        <td v-if="settings.reset_print_number_daily" colspan="7" style="text-align: end;">Total: {{ sales.data.reduce((total, obj) => parseFloat(obj.grand_total) + total,0)}} </td>
                        <!-- <td></td>
                        <td></td>
                        <td></td> -->
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="sales" :limit="2" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>


        <div v-if="!is('Super Admin') && !is('Admin')">
            <not-found></not-found>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" >Payment</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit.prevent="createPayment()">
                    <div class="modal-body">
                      <div class="form-group">
                          <label>Account</label>
                          <select name="account" class="form-control"  v-model="form.account_id" >
                            <option
                              v-for="(acc,index) in accounts" :key="index"
                              :value="index">{{ acc }}</option>

                          </select>
                          <has-error :form="form" field="account_id"></has-error>
                      </div>
                      <div class="form-group">
                        <label>Payment Method</label>
                          <select required class="form-control"  v-model="form.payment_method">
                            <option value="Cash">Cash</option>
                            <option value="Card">Card</option>
                            <option value="Online">Online</option>
                            <option value="GPay">GPay</option>
                            <option value="Phonepe">Phonepe</option>
                            <option value="Amazon Pay">Amazon Pay</option>
                          </select>

                      </div>
                      <span v-if="form.payment_method == 'Cash'">
                        <div class="form-group">
                        <label>Amount Paid</label>
                            <input required v-model.number="form.amount_paid"  type="text">
                      </div>
                      </span>
                     <!-- <div class="form-group">
    											<label>Payment Status</label>
                          <select required class="form-control"  v-model="form.payment_status">

                            <option value="Paid">Fully Paid</option>
                            <option value="Partially Paid">Partially Paid</option>
                          </select>
                          <has-error :form="form" field="location_id"></has-error>
    									</div>
                      <span v-if="form.payment_status === 'Partially Paid'">
                        <div class="form-group">
      											<label>Amount Paid</label>
                            <input required v-model.number="form.amount_paid" type="text">
      									</div>
                      </span>-->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                        <button  type="submit" class="btn btn-primary">Create</button>
                    </div>
                  </form>

                <!-- <form @submit.prevent="createUser"> -->

              <!--  <form @submit.prevent="createPayment()">
                    <div class="modal-body">
                      <div class="form-group">

<select required class="form-control"   v-model="form.payment_method">
  <option :value="null" selected="" disabled="">Payment Method</option>
  <option value="Cash">Cash</option>
  <option value="Card">Card</option>
  <option value="Online">Online</option>
  <option value="GPay">GPay</option>
  <option value="Phonepe">Phonepe</option>
  <option value="Amazon Pay">Amazon Pay</option>
</select>

</div>-->

                      <!--<div class="form-group">
                          <label>Account</label>
                          <select name="account" class="form-control"  v-model="form.account_id" >
                            <option
                              v-for="(acc,index) in accounts" :key="index"
                              :value="index">{{ acc }}</option>

                          </select>
                          <has-error :form="form" field="account_id"></has-error>
                      </div>
                      <div class="form-group">
    											<label>Payment Status</label>
                          <select required class="form-control"  v-model="form.payment_status">

                            <option value="Paid">Fully Paid</option>
                            <option value="Partially Paid">Partially Paid</option>
                          </select>
                          <has-error :form="form" field="location_id"></has-error>
    									</div>
                      <span v-if="form.payment_status === 'Partially Paid'">
                        <div class="form-group">
      											<label>Amount Paid</label>
                            <input required v-model.number="form.amount_paid" type="text">
      									</div>
                      </span> -->
                  <!--  </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                        <button  type="submit" class="btn btn-primary">Create</button>
                    </div>
                  </form>-->
                </div>
            </div>
        </div>

    </div>
  </section>
</template>

<script>
import moment from 'moment'
    export default {
        data () {
            return {
              settings:null,
              searchText:null,
                editmode: false,
                sales : {},
                form: new Form({
                  amount_paid: null,
                  account_id: null,
                  payment_status: null,
                  current_sale_id: null,
                }),
                filterForm: new Form({
                dateFilter: null,
                customer: null,
                type:null,
                payment_status:null,
                src: window.location.href,
                }),
                accounts: [],

            }
        },
        methods: {
            scrollToTop() {
                window.scrollTo(0,0);
          },
          RemoveItem(id)
     {

           Swal.fire({
               title: 'Are you sure?',
               text: "You won't be able to revert this!",
               showCancelButton: true,
               confirmButtonColor: '#d33',
               cancelButtonColor: '#3085d6',
               confirmButtonText: 'Yes, delete it!'
               }).then((result) => {

                   // Send request to the server
                     if (result.value) {
                           this.form.delete('/api/sale/'+id).then(()=>{
                                   Swal.fire(
                                   'Deleted!',
                                   'Your file has been deleted.',
                                   'success'
                                   );
                               // Fire.$emit('AfterCreate');
                               this.getResults(this.sales.current_page,null);
                           }).catch((data)=> {
                               Swal.fire("Failed!", data.message, "warning");
                           });
                     }
               })
     },
     search()
     {
       if(this.searchText == '')
       {
         this.load();
       }
       else
       {
         axios.get("/api/sale/list/search/"+this.searchText).then(
           ({ data }) => (this.sales = data.data)
         );
       }


     },
     resetFilter()
          {
            localStorage.removeItem('filterSales');
            this.$refs.filterForm.reset();
            this.load();

          },

     filterSales()
            {
                localStorage.filterSales = JSON.stringify(this.filterForm);


                this.filterForm.post("/api/sale/filter").then(
                ({ data }) => (this.sales = data,
                               this.settings = data.settings)
                );
            },
          paymentModal(id,due)
          {
            this.loadAccounts();
            this.form.reset();
            this.form.current_sale_id = id;
            this.form.amount_paid = due;

            $('#paymentModal').modal('show');
          },
          createPayment()
          {
            this.$Progress.start();
            this.form.post('/api/sale/payment')
            .then((response)=>{
                $('#paymentModal').modal('hide');

                Toast.fire({
                        icon: 'success',
                        title: response.data.message
                });
                this.$Progress.finish();
                // this.getResults(this.sales.current_page,null);
                this.load();


            })
            .catch(()=>{
                Toast.fire({
                    icon: 'error',
                    title: 'Some error occured! Please try again'
                });
            })


          },
          changeStatus(id,status)
          {
            this.$Progress.start();
            axios.get('/api/sale/status/change/'+id+'/'+status);
            this.$Progress.finish();
            this.getResults(this.sales.current_page,null);
          },
          PrintSale(id)
          {

           if (this.settings.print_format == "super_market") {
              this.$router.push(`/sale/restaurant_style_1/${id}`)
          }
          else if (this.settings.print_format == "restaurant_style_2") {
              //this.$router.push(`/sale/print_super_market/${response.data.data.id}`);
              setInterval(this.$router.push(`/sale/restaurant_style_2/${id}`), 4000);

          }
          else if (this.settings.print_format == "restaurant_style_3") {
              //this.$router.push(`/sale/print_super_market/${response.data.data.id}`);
              setInterval(this.$router.push(`/sale/restaurant_style_3/${id}`), 4000);

          }
          else if (this.settings.print_format == "restaurant_style_4") {
              //this.$router.push(`/sale/print_super_market/${response.data.data.id}`);
              setInterval(this.$router.push(`/sale/restaurant_style_4/${id}`), 4000);

          }else if (this.settings.print_format == "restaurant_style_5") {
              //this.$router.push(`/sale/print_super_market/${response.data.data.id}`);
              setInterval(this.$router.push(`/sale/restaurant_style_5/${id}`), 4000);

          }
          else if (this.settings.print_format == "bakery_style_1") {
              //this.$router.push(`/sale/print_super_market/${response.data.data.id}`);
              setInterval(window.open(`/sale/bakery_style_1/${id}`), 4000);

          }


          },
          returnSale(id)
          {
            this.$router.push(`/sale/return/${id}`);
          },
            getResults(page = 1,searchText = null) {
                  this.$Progress.start();
                  axios.get('/api/sale?page=' + page).then(({ data }) => (this.sales = data.data));
                  this.scrollToTop();
                  this.$Progress.finish();
            },
            load(){
                if (localStorage.getItem('filterSales') !== null) {

                let filterSales = JSON.parse(localStorage.getItem('filterSales'));

                if (filterSales.src == window.location.href) {

                if((filterSales.dateFilter))
                {

                this.filterForm.dateFilter = [new Date(filterSales.dateFilter[0]),new Date(filterSales.dateFilter[1])];

                }

                this.filterForm.payment_status = filterSales.payment_status;
                this.filterForm.customer = filterSales.customer;
                this.filterForm.type = filterSales.type;

                this.filterSales();

                }
                }
                else{

                    this.getResults();

                }
                    // axios.get("/api/sale").then(({ data }) => (this.sales = data.data,
                    // this.settings = data.settings));

            },
            loadAccounts(){
                axios.get("/api/account/list").then(({ data }) => (this.accounts = data.data,
                this.settings = data.settings));
            },

            autoRefreshChange()
            {
              // axios.get("/api/settings/auto-refresh/"+this.settings.auto_refresh);
              // this.load();
            }


        },
        mounted() {
            window.scrollTo(0, 0)

        },
        created() {

            this.$Progress.start();
            this.loadAccounts();
            this.load();
            // setTimeout(() => {
            //   if(this.settings.auto_refresh)
            //   this.timer = setInterval(this.load, 4000);
            // }, 3000);



            this.$Progress.finish();

        },

        filters: {
  date: function (date) {

    return   moment.utc(date).local().format('DD-MM-YYYY / hh:mm A');

  },


},

    }
</script>
