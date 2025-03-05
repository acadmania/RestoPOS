<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">

            <div class="card" v-if="is('Super Admin') || is('Admin')">
              <div class="card-header">
                <h3 class="card-title">Sale Return List</h3>
                <input style="margin-left:2%" type="text" v-model="searchText" name="name" placeholder="Search.." @keyup="search"  autocomplete="off">
                <div class="card-tools">


                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Date</th>
                      <th>Customer Name</th>
                      <th>Total</th>
                      <th>Status</th>
                      <th>Payment Status</th>
                      <th>Action</th>

                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="sale in sales.data" :key="sale.id">

                      <td>{{sale.id}}</td>
                      <td>{{sale.created_at | date}}</td>
                      <td >{{sale.customer_name}}</td>
                      <td>{{sale.grand_total}}</td>
                      <td>{{sale.status}}</td>
                      <td>{{sale.payment_status}}</td>
                      <td>

                        <div class="btn-group">
                    <button type="button" class="btn btn-info">Action</button>
                    <button type="button" class="btn btn-info dropdown-toggle dropdown-icon" data-toggle="dropdown" aria-expanded="false">
                      <span class="sr-only">Toggle Dropdown</span>
                      <div class="dropdown-menu" role="menu" style="">
                    <!--    <a class="dropdown-item" href="#" @click.prevent="$router.push(`/sale/return/view/${sale.id}`)">View</a> -->
                        <a class="dropdown-item" href="#" @click.prevent="PrintSale(sale.id)">Print</a>
                      </div>
                    </button>
                  </div>




                      <!--  /
                        <a href="#" @click="RemoveItem(purchase.id)">
                            <i class="fa fa-trash red"></i>
                        </a>-->
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

          PrintSale(id)
          {
              if (this.settings.print_format == "ashok_leyland") {
            this.$router.push(`/sale/print_ashok/${id}`)
          }
          else if (this.settings.print_format == "army_canteen") {
              this.$router.push(`/sale/print_army/${id}`)
          }
          else if (this.settings.print_format == "super_market") {
              this.$router.push(`/sale/print_super_market/${id}/return`)
          }

          },

     search()
     {
       if(this.searchText == '')
       {
         this.load();
       }
       else
       {
         axios.get("/api/sale/return/list/search/"+this.searchText).then(
           ({ data }) => (this.sales = data.data)
         );
       }


     },


            getResults(page = 1) {
                  this.$Progress.start();
                  axios.get('/api/sale/return?page=' + page).then(({ data }) => (this.sales = data.data));
                  this.$Progress.finish();
            },
            load(){

                    axios.get("/api/sale/return").then(({ data }) => (this.sales = data.data,
                    this.settings = data.settings));

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

    return   moment.utc(date).local().format('DD-MM-YYYY');

  },


},

    }
</script>
