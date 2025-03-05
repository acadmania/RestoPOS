<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">

            <div class="card"  v-if="is('Super Admin')  || is('Admin')">
              <div class="card-header">
                <h3 class="card-title">Transactions</h3>

                <div class="card-tools">
                    <div>
                    <div class="form-group">
                        <!-- <label>Choose Account</label> -->
                        <select class="form-control" @change="filterAcc()"  v-model="selectedAcc">
                          <option value="" disabled selected>Search account</option>
                          <option
                              v-for="(item,index) in accounts.data" :key="index"
                              :value="item.id">{{ item.name }}</option>

                        </select>
                    </div>
                  </div>
                  <!-- <button type="button" class="btn btn-sm btn-primary" @click="transferAmount">
                      <i class="fa fa-plus-square"></i>
                      Account Transfer
                  </button> -->
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Date</th>
                      <th>Account Name</th>
                      <th>Detail</th>
                      <th>Opening Balance</th>
                      <th>Amount</th>
                      <th>Credit/Debit</th>
                      <th>Closing Balance</th>
                    <!--  <th></th>-->
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="transaction in transactions.data" :key="transaction.id">

                      <td>{{transaction.id}}</td>
                      <td>{{transaction.created_at | date}}</td>
                      <td >{{transaction.account_name}}</td>
                      <td >
                        <span v-if="transaction.supplier_id">Supplier: </span>{{transaction.supplier_name}}
                        <span v-if="transaction.customer_id">Customer: </span>{{transaction.customer_name}}
                        <span v-if="transaction.purchase_id">Purchase: </span>{{transaction.purchase_id}}
                        <span v-if="transaction.sale_id">Sale: </span>{{transaction.sale_id}}
                        <span v-if="transaction.employee_id">Employee: </span>{{transaction.employee_name}}
                        <span v-if="transaction.investor_id">Investor: </span>{{transaction.investor_name}}
                        <span v-if="transaction.expense_id">Expense : {{transaction.expense_id}}</span>
                        <span v-else>{{transaction.description}}</span>
                      </td>
                      <td >{{transaction.opening_balance}}</td>
                      <td>{{transaction.amount}}</td>
                      <td>{{transaction.type}}</td>
                      <td>{{transaction.closing_balance}}</td>
                    <!--  <td>
                        <a href="#" @click="RemoveItem(transaction.id)">
                          <i class="fa fa-trash red"></i>
                      </a>
                    </td> -->

                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :limit="3" :data="transactions" @pagination-change-page="page => getResults(page,selectedAcc)"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>


        <div  v-if="!is('Super Admin') && !is('Admin')">
            <not-found></not-found>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" >Account Transfer</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- <form @submit.prevent="createUser"> -->

                <form @submit.prevent="create()">
                    <div class="modal-body">


                        <div class="form-group">
                            <label>Amount</label>
                            <input v-model="form.amount" type="text"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('balance') }">

                        </div>

                        <div class="form-group">
                            <label>From Account</label>
                            <select class="form-control" v-model="form.from_account">
                              <option
                                  v-for="(cat,index) in accounts" :key="index"
                                  :value="index"
                                  >{{ cat }}</option>
                            </select>

                        </div>

                        <div class="form-group">
                            <label>To Account</label>
                            <select class="form-control" v-model="form.to_account">
                              <option
                                  v-for="(cat,index) in accounts" :key="index"
                                  :value="index"
                                  >{{ cat }}</option>
                            </select>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Transfer</button>

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
                selectedAcc:null,
                accounts:null,
                transactions : {},
                form: new Form({

                }),

            }
        },
        methods: {
          loadAccounts(){
              // axios.get("/api/account/list").then(({ data }) => (this.accounts = data.data));
              axios.get("/api/get/account").then(({ data }) => (this.accounts = data));
          },
          transferAmount()
          {
            $('#addNew').modal('show');
          },
          getResults(page = 1, selectedAcc = this.selectedAcc) {
                this.$Progress.start();
                axios.get('/api/transaction?page=' + page + '&selectedAcc=' + selectedAcc).then(({ data }) => (this.transactions = data.data));
                this.$Progress.finish();
          },

          filterAcc() {
                if(this.selectedAcc == '')
                {
                this.load();
                }
                else
                {
                axios.get("/api/filter/transaction/"+this.selectedAcc).then(
                    ({ data }) => (this.transactions = data.data)
                );
                }
            },

          create(){

              this.form.post('/api/account/transfer')
              .then((response)=>{
                  $('#addNew').modal('hide');

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
                           this.form.delete('/api/transaction/'+id).then(()=>{
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

            load(){

              this.getResults();
                    // axios.get("/api/transaction").then(({ data }) => (this.transactions = data.data));

            },



        },
        mounted() {
            window.scrollTo(0, 0)
        },
        created() {

            this.$Progress.start();
            this.load();
            this.loadAccounts();
            this.$Progress.finish();
        },
        filters: {
    date: function (date) {

    return   moment.utc(date).local().format('DD-MM-YYYY hh:mm A');

  },


},

    }
</script>
<style>
.table td {
padding: 0.25rem !important;
}
</style>
