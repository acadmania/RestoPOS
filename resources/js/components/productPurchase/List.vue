<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">

            <div class="card" v-if="is('Super Admin') || is('Admin')">
              <div class="card-header">
                <h3 class="card-title">Product Purchase List</h3>

                <div class="card-tools">
                    <form @submit.prevent="filterPurchases()">


                             <date-picker v-model="filterForm.dateFilter" type="date" placeholder="From To Date" range></date-picker>

                             <input type="text" v-model="filterForm.supplier" name="name" placeholder="Supplier name" autocomplete="off">

                              <select  v-model="filterForm.payment_status" style="margin-left: 20px;">
                                <option value="" disabled selected>Select Payment Status</option>
                                <option value="Paid">Paid</option>
                                <option value="Unpaid">Unpaid</option>
                                <option value="Partially Paid">Partially Paid</option>
                              </select>


                                <button type="submit" class="btn btn-success">Filter</button>
                                <a href="" @click="resetFilter()">
                                    <i class="fas fa-sync blue" aria-hidden="true"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-primary" @click="$router.push('/product/purchase/add')">
                                    <i class="fa fa-plus-square"></i>
                                    Add New
                                </button>

          </form>


                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Date</th>
                      <th>Supplier Name</th>
                      <th>Total</th>
                      <th>Order Status</th>
                      <th>Payment Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="purchase in purchases.data" :key="purchase.id">

                      <td>{{purchase.id}}</td>
                      <td>{{purchase.created_at | date}}</td>
                      <td >{{purchase.supplier_name}}</td>
                      <td>{{purchase.grand_total}}</td>
                      <td>{{purchase.status}}</td>
                      <td>{{purchase.payment_status}}</td>
                      <td>

                        <div class="btn-group">
                    <button type="button" class="btn btn-info">Action</button>
                    <button type="button" class="btn btn-info dropdown-toggle dropdown-icon" data-toggle="dropdown" aria-expanded="false">
                      <span class="sr-only">Toggle Dropdown</span>
                      <div class="dropdown-menu" role="menu" style="">

                        <a class="dropdown-item" href="#" @click.prevent="$router.push(`/product/purchase/view/${purchase.id}`)">VIEW</a>
                        <a class="dropdown-item" v-if="purchase.status === 'Ordered'" href="#" @click.prevent="$router.push(`/product/purchase/add/${purchase.id}`)">RECEIVED</a>
                        <a class="dropdown-item" href="#" @click.prevent="$router.push(`/product/purchase/print/${purchase.id}`)">PRINT</a>
                        <a class="dropdown-item" href="#" @click.prevent="returnPurchase(purchase.id)">Return</a>
                        <a class="dropdown-item" href="#" @click.prevent="RemoveItem(purchase.id)">DELETE</a>
                          <!--<a class="dropdown-item" v-if="purchase.status === 'Ordered'" href="#" @click.prevent="receive(purchase.id)">RECEIVED</a>-->
                          <a class="dropdown-item" v-if="purchase.payment_status === 'Unpaid' || purchase.payment_status === 'Partially Paid'" href="#" @click.prevent="paymentModal(purchase.id)">Record Payment</a>

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
                        <td colspan="4" style="text-align: end;">Total: {{ purchases.data.reduce((total, obj) => parseFloat(obj.grand_total) + total,0)}} </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="purchases" @pagination-change-page="getResults"></pagination>
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

                <!-- <form @submit.prevent="createUser"> -->

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
                      </span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                        <button  type="submit" class="btn btn-primary">Create</button>
                    </div>
                  </form>
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
                editmode: false,
                purchases : {},
                form: new Form({
                  amount_paid: null,
                  dateFilter: null,
                  account_id: null,
                  payment_status: null,
                  current_purchase_id: null,
                  supplier: null,
                }),
                filterForm: new Form({
                  dateFilter: null,
                  payment_status: null,
                  supplier: null,
                  src: window.location.href,
                }),
                accounts: [],

            }
        },
        methods: {
            scrollToTop() {
                window.scrollTo(0,0);
        },

          returnPurchase(id)
          {
            this.$router.push(`/purchase/return/${id}`);
          },
          paymentModal(id)
          {
            this.loadAccounts();
            this.form.reset();
            this.form.current_purchase_id = id;

            $('#paymentModal').modal('show');
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
                           this.form.delete('/api/purchase/'+id).then(()=>{
                                   Swal.fire(
                                   'Deleted!',
                                   'Your file has been deleted.',
                                   'success'
                                   );
                               // Fire.$emit('AfterCreate');
                               this.load();
                           }).catch((data)=> {
                               Swal.fire("Failed!", data.message, "warning");
                           });
                     }
               })
     },
          createPayment()
          {
            this.$Progress.start();
            this.form.post('/api/purchase/payment')
            .then((response)=>{
                $('#paymentModal').modal('hide');

                Toast.fire({
                        icon: 'success',
                        title: response.data.message
                });
                this.$Progress.finish();
                this.load();


            })
            .catch(()=>{
                Toast.fire({
                    icon: 'error',
                    title: 'Some error occured! Please try again'
                });
            })

          },
          resetFilter()
          {
            localStorage.removeItem('filterPurchase');
            this.$refs.filterForm.reset();
            this.load();

          },

          filterPurchases()
            {
                localStorage.filterPurchase = JSON.stringify(this.filterForm);
                // localStorage.src = JSON.stringify(window.location.href);

                this.filterForm.post("/api/purchase/filter").then(
                ({ data }) => (this.purchases = data)
                );
            },

            getResults(page = 1) {
                  this.$Progress.start();
                  axios.get('/api/purchase?page=' + page).then(({ data }) => (this.purchases = data.data));
                  this.scrollToTop();
                  this.$Progress.finish();
            },
            load(){
                if (localStorage.getItem('filterPurchase') !== null) {

                    let filterPurchase = JSON.parse(localStorage.getItem('filterPurchase'));

                if (filterPurchase.src == window.location.href) {

                    if((filterPurchase.dateFilter))
                    {

                     this.filterForm.dateFilter = [new Date(filterPurchase.dateFilter[0]),new Date(filterPurchase.dateFilter[1])];

                    }

                    this.filterForm.payment_status = filterPurchase.payment_status;
                    this.filterForm.supplier = filterPurchase.supplier;

                    this.filterPurchases();

                }
            }
            else{
                axios.get("/api/purchase").then(({ data }) => (this.purchases = data.data));
            }

            },
            loadAccounts(){
                axios.get("/api/account/list").then(({ data }) => (this.accounts = data.data));
            },


        },
        mounted() {
            window.scrollTo(0, 0)
        },
        created() {

            this.$Progress.start();
            this.load();
            this.$Progress.finish();
        },
        filters: {
  date: function (date) {
    return   moment.utc(date).local().format('DD-MM-YYYY / hh:mm A');
  },


},

    }
</script>
