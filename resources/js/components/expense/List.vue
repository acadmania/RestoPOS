<template>

  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">

            <div class="card" v-if="is('Super Admin') || is('Admin')">
              <div class="card-header">
                <h3 class="card-title">Expense List</h3>

                <div class="card-tools">

                  <button type="button" class="btn btn-sm btn-primary" @click="newModal">
                      <i class="fa fa-plus-square"></i>
                      Add New
                  </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Details</th>
                      <th>Account</th>
                      <th>Amount</th>
                      <th>Created At</th>

                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="expense in expenses.data" :key="expense.id">

                      <td>{{expense.id}}</td>
                      <td >{{expense.note}}</td>
                      <td >{{expense.account_name}}</td>
                      <td >{{expense.amount}}</td>
                      <td >{{expense.created_at | moment }}</td>
                      <td>

                        <a href="#" @click="editModal(expense)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                         /
                        <a href="#" @click="RemoveItem(expense.id)">
                            <i class="fa fa-trash red"></i>
                        </a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="expenses" :limit="3" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>


        <div v-if="!is('Super Admin') && !is('Admin')">
            <not-found></not-found>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" v-show="!editmode">Create New Expense</h5>
                    <h5 class="modal-title" v-show="editmode">Update Expense</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- <form @submit.prevent="createUser"> -->

                <form @submit.prevent="editmode ? update() : create()">
                    <div class="modal-body">
                        <div class="form-group" style="padding-left:250px">
                  <label>Date/Time</label>
                <!-- <date-picker v-model="form.datetime" placeholder="OPTIONAL" type="datetime" format="DD-MM-YYYY h:m:s A" :use12h="true"></date-picker>-->
                <input v-model="form.datetime1" placeholder="OPTIONAL" type="date" required>
                </div>

                        <div class="form-group">
                            <label>Note</label>
                            <input v-model="form.note" type="text" name="name"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                            <has-error :form="form" field="name"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Amount</label>
                            <input v-model.number="form.amount" type="text" name="name"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                            <has-error :form="form" field="name"></has-error>
                        </div>


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
                              <label>Category</label>
                              <select name="account" class="form-control"  v-model="form.category_id" >
                                <option
                                  v-for="(cat,index) in categories" :key="index"
                                  :value="index">{{ cat }}</option>

                              </select>
                              <has-error :form="form" field="account_id"></has-error>
                          </div>



                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button v-show="editmode" type="submit" class="btn btn-success">Update</button>
                        <button v-show="!editmode" type="submit" class="btn btn-primary">Create</button>
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
                accounts : {},
                categories : {},
                expenses : {},
                form: new Form({
                    id : '',
                    note: '',
                    amount: '',
                    datetime1: '',
                    category_id: '',
                    account_id: '',


                })
            }
        },
        filters: {
  moment: function (date) {

    return   moment.utc(date).local().format('DD-MM-YYYY hh:mm A');

  }

},

        methods: {
            scrollToTop() {
                window.scrollTo(0,0);
        },
            getResults(page = 1) {

                  this.$Progress.start();

                  axios.get('/api/Expense?page=' + page).then(({ data }) => (this.expenses = data.data));
                  this.scrollToTop()
                  this.$Progress.finish();
            },
            update(){
                this.$Progress.start();
                this.form.put('/api/Expense/'+this.form.id)
                .then((response) => {
                    // success
                    $('#addNew').modal('hide');
                    Toast.fire({
                      icon: 'success',
                      title: response.data.message
                    });
                    this.$Progress.finish();
                        //  Fire.$emit('AfterCreate');

                    this.load();
                })
                .catch(() => {
                    this.$Progress.fail();
                });

            },
            editModal(account){
                this.editmode = true;
                this.form.reset();
                $('#addNew').modal('show');
                this.form.fill(account);
            },

            newModal(){
                this.editmode = false;
                this.form.reset();
                $('#addNew').modal('show');
            },

            load(){
                this.getResults();
                    //axios.get("/api/Expense").then(({ data }) => (this.expenses = data.data));

            },

            create(){

                this.form.post('/api/Expense')
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
            loadCategories(){
                axios.get("/api/expense/category/list").then(({ data }) => (this.categories = data.data));
            },
            loadAccounts(){
                axios.get("/api/account/list").then(({ data }) => (this.accounts = data.data));
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
                              this.form.delete('/api/Expense/'+id).then(()=>{
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
        }

        },
        mounted() {
            window.scrollTo(0, 0)
        },
        created() {

            this.$Progress.start();
            this.loadCategories();
            this.loadAccounts();
            this.load();

            this.$Progress.finish();
        },

    }
</script>
