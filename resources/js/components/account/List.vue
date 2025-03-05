<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">

            <div class="card"  v-if="is('Super Admin') || is('Admin') && is('Admin')">
              <div class="card-header">
                <h3 class="card-title">Account List</h3>

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
                      <th>Name</th>
                      <th>Code</th>
                      <th>Balance</th>
                      <th>Created By</th>
                      <th>Updated By</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="account in accounts.data" :key="account.id">

                      <td>{{account.id}}</td>
                      <td >{{account.name}}</td>
                      <td>{{account.code}}</td>
                      <td>{{account.balance}}</td>
                      <td>{{account.created_by}}</td>
                      <td>{{account.updated_by}}</td>
                      <td>

                        <a href="#" @click="editModal(account)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="transferAmt(account)">
                            <i class="fas fa-tags blue"></i>
                        </a>
                         /
                        <a href="#" @click="RemoveItem(account.id)">
                            <i class="fa fa-trash red"></i>
                        </a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="accounts" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>


        <div  v-if="!is('Super Admin')  && !is('Admin')">
            <not-found></not-found>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" v-show="!editmode">Create New Account</h5>
                    <h5 class="modal-title" v-show="editmode">Update Account</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- <form @submit.prevent="createUser"> -->

                <form @submit.prevent="editmode ? update() : create()">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input v-model="form.name" type="text" name="name"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                            <has-error :form="form" field="name"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Code</label>
                            <input v-model="form.code" type="text" name="code"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('code') }">
                            <has-error :form="form" field="code"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Balance</label>
                            <input v-model="form.balance" type="text" name="balance"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('balance') }">
                            <has-error :form="form" field="balance"></has-error>
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


       <!-- Transaction Modal -->
       <div class="modal fade" id="transactionModal" tabindex="-1" role="dialog" aria-labelledby="transactionModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" v-show="!editmode">Transfer Amount</h5>
                <h5 class="modal-title" v-show="editmode">Update Account</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- <form @submit.prevent="createUser"> -->
                <!-- editmode ? update() :  -->
            <form @submit.prevent="transfer()">
                <div class="modal-body">
                    <div class="form-group">

                        <label class="text-danger">To Account</label>
                        <select class="form-control" v-model="transferForm.toAcc">
                          <option
                              v-for="(cat,index) in accounts.data"  v-if="cat.id != transferForm.fromAcc"
                              :value="cat.id"
                              >{{ cat.name }}</option>
                        </select>



                    </div>
                    <div class="form-group">
                        <label class="text-danger">Amount</label>
                        <input v-model="transferForm.amount" type="text" name="amount"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('amount') }">
                        <has-error :form="form" field="amount"></has-error>
                    </div>


                    <div class="form-group">
                        <label>Description</label>
                        <input v-model="transferForm.description" type="text" name="description" class="form-control">
                    </div>
                    <!-- <div class="form-group">
                        <label>Code</label>
                        <input v-model="form.code" type="text" name="code"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('code') }">
                        <has-error :form="form" field="code"></has-error>
                    </div> -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <!-- <button v-show="editmode" type="submit" class="btn btn-success">Update</button> -->
                    <button v-show="!editmode" type="submit" class="btn btn-primary">Transfer</button>
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
                editmode: false,
                accounts : {},
                form: new Form({
                    id : '',
                    name: '',
                    code: '',
                    balance: '',

                }),
                transferForm: new Form({
                    toAcc: '',
                    fromAcc: '',
                    amount: '',
                    description: '',
                }),
            }
        },
        methods: {

            getResults(page = 1) {

                  this.$Progress.start();

                  axios.get('/api/account?page=' + page).then(({ data }) => (this.accounts = data.data));

                  this.$Progress.finish();
            },
            update(){
                this.$Progress.start();
                this.form.put('/api/account/'+this.form.id)
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
            transferAmt(account){

                // console.log(this.fromAcc);
                this.editmode = false;
                this.transferForm.reset();
                $('#transactionModal').modal('show');
                this.transferForm.fromAcc = account.id;
            },

            transfer(){
                axios.post('/api/transfer/account', this.transferForm)
                .then((response)=>{
                    $('#transactionModal').modal('hide');

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

            load(){

                    axios.get("/api/account").then(({ data }) => (this.accounts = data.data));

            },

            create(){

                this.form.post('/api/account')
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
                              this.form.delete('/api/account/'+id).then(()=>{
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
            this.load();
            this.$Progress.finish();
        },

    }
</script>
