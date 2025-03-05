<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">

            <div class="card" v-if="is('Super Admin') || is('Admin')">
              <div class="card-header">
                <h3 class="card-title">Running Orders</h3>
                <input style="margin-left:2%" type="text" v-model="searchText" name="name" placeholder="Search.." @keyup="search"  autocomplete="off">
                <div class="card-tools">

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
                      <th>Date</th>
                      <th>Items</th>
                      <th>Customer Name</th>
                      <th>Table Name</th>
                      <th>Total</th>
                      <th>Status</th>

                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="sale in sales.data" :key="sale.id">

                      <td>{{sale.id}}</td>
                      <td><span v-for="s in sale.items">
                        {{ s.food_name }} - {{ parseFloat(s.quantity).toFixed(0) }}<br></span></td>
                      <td>{{sale.created_at | date}}</td>
                      <td >{{sale.customer_name}}</td>
                      <td >{{sale.table_name}}</td>
                      <td>{{sale.grand_total}}</td>
                      <td>{{sale.status}}</td>

                      <td>

                        <a class="btn btn-primary"  v-if="sale.status === 'Ordered'" href="#" @click.prevent="changeStatus(sale.id,'Ready')">Ready</a>
                        <a  class="btn btn-primary" v-if="sale.status === 'Ordered' || sale.status === 'Ready'" href="#" @click.prevent="changeStatus(sale.id,'Completed')">Complete</a>





                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="sales" @pagination-change-page="getResults"></pagination>
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
              searchText:null,
                editmode: false,
                sales : {},
                form: new Form({
                  amount_paid: null,
                  account_id: null,
                  payment_status: null,
                  current_sale_id: null,
                }),
                accounts: [],

            }
        },
        methods: {
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
                               this.load();
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
          paymentModal(id)
          {
            this.loadAccounts();
            this.form.reset();
            this.form.current_sale_id = id;

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
            this.load();
          },
          PrintSale(id)
          {
              if (this.settings.print_format == "ashok_leyland") {
            this.$router.push(`/sale/print_ashok/${id}`)
          }
          else if (this.settings.print_format == "army_canteen") {
              this.$router.push(`/sale/print_army/${id}`)
          }
          else if (this.settings.print_format == "super_market") {
              this.$router.push(`/sale/print_super_market/${id}`)
          }

          },
          returnSale(id)
          {
            this.$router.push(`/sale/return/${id}`);
          },
            getResults(page = 1) {
                  this.$Progress.start();
                  axios.get('/api/sale?page=' + page).then(({ data }) => (this.sales = data.data));
                  this.$Progress.finish();
            },
            load(){

                     axios.get("/api/sale/notcompleted").then(({ data }) => (this.sales = data.data,
                     this.settings = data.settings));

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
            this.timer = setInterval(this.load, 4000);
            this.$Progress.finish();
        },

        filters: {
  date: function (date) {

    return   moment.utc(date).local().format('DD-MM-YYYY');

  },


},

    }
</script>
