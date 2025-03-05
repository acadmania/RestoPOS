<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">

            <div class="card" v-if="is('Super Admin')">
              <div class="card-header">
                <h3 class="card-title">User List</h3>

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
                      <th>Email</th>
                      <th>Email Verified?</th>
                      <th>Created</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="user in users.data" :key="user.id">

                      <td>{{user.id}}</td>

                      <td class="text-capitalize">{{user.name}}</td>
                      <td>{{user.email}}</td>
                      <td :inner-html.prop="user.email_verified_at | yesno"></td>
                      <td>{{user.created_at}}</td>

                      <td>

                        <a href="#" @click="editModal(user)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deleteUser(user.id)">
                            <i class="fa fa-trash red"></i>
                        </a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="users" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>


        <div v-if="!is('Super Admin')">
            <not-found></not-found>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" v-show="!editmode">Create New User</h5>
                    <h5 class="modal-title" v-show="editmode">Update User's Info</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- <form @submit.prevent="createUser"> -->

                <form @submit.prevent="editmode ? updateUser() : createUser()">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6">
                        <div class="form-group">
                            <label>Name</label>
                            <input v-model="form.name" type="text" name="name"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                            <has-error :form="form" field="name"></has-error>
                        </div>
                        </div>
                         <div class="col-sm-6">
                        <div class="form-group">
                            <label>Email</label>
                            <input v-model="form.email" type="text" name="email"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                            <has-error :form="form" field="email"></has-error>
                            </div>
                        </div>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input v-model="form.password" type="password" name="password"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('password') }" autocomplete="false">
                            <has-error :form="form" field="password"></has-error>
                        </div>



                        <div class="form-group">
                            <label>Access to POS Locations</label>
                            <select required class="form-control" multiple v-model="form.locations">
                              <option
                                  v-for="(loc,index) in locations" :key="index" :value="{ id: index, text: loc }"
                                  >{{ loc }}</option>

                            </select>
                            <has-error :form="form" field="type"></has-error>
                        </div>



                        <div class="row"  v-for='loc in form.locations'>
                          <div class="col 2">

                            <label>{{loc.text}} - Cash Account</label>
                            <select name="account" class="form-control"  v-model="form.cashAccounts[loc.id]" >
                              <option
                                v-for="(acc,index) in accounts" :key="index"
                                :value="index">{{ acc }}</option>
                            </select>
                        </div>
                          <div class="col 2">
                            <label>{{loc.text}} - Card Account</label>
                            <select name="account" class="form-control"  v-model="form.cardAccounts[loc.id]" >
                              <option
                                v-for="(acc,index) in accounts" :key="index"
                                :value="index">{{ acc }}</option>
                            </select>
                        </div>
                          <div class="col 2">
                            <label>{{loc.text}} - Online Account</label>
                            <select name="account" class="form-control"  v-model="form.onlineAccounts[loc.id]" >
                              <option
                                v-for="(acc,index) in accounts" :key="index"
                                :value="index">{{ acc }}</option>
                            </select>
                        </div>
                          <div class="col 2">
                            <label>{{loc.text}} - GPay Account</label>
                            <select name="account" class="form-control"  v-model="form.gpayAccounts[loc.id]" >
                              <option
                                v-for="(acc,index) in accounts" :key="index"
                                :value="index">{{ acc }}</option>
                            </select>
                        </div>
                          <div class="col 2">
                            <label>{{loc.text}} - Phonepe Account</label>
                            <select name="account" class="form-control"  v-model="form.phonepeAccounts[loc.id]" >
                              <option
                                v-for="(acc,index) in accounts" :key="index"
                                :value="index">{{ acc }}</option>
                            </select>
                        </div>
                          <div class="col 2">
                            <label>{{loc.text}} - Amazon Pay Account</label>
                            <select name="account" class="form-control"  v-model="form.amazonpayAccounts[loc.id]" >
                              <option
                                v-for="(acc,index) in accounts" :key="index"
                                :value="index">{{ acc }}</option>
                            </select>
                        </div>
                        </div>

                          <div class="col3">

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
    export default {
        data () {
            return {
                editmode: false,
                users : {},
                locations : {},
                accounts: {},
                form: new Form({
                    id : '',

                    name: '',
                    email: '',
                    cashAccounts: {},
                    cardAccounts: {},
                    onlineAccounts: {},
                    gpayAccounts: {},
                    phonepeAccounts: {},
                    amazonpayAccounts: {},
                    locations: [],
                    password: '',
                    email_verified_at: '',
                })
            }
        },
        methods: {
          loadAccounts(){
              axios.get("/api/account/list").then(({ data }) => (this.accounts = data.data));
          },
          loadLocations(){
              axios.get("/api/location/list").then(({ data }) => (this.locations = data.data));
          },

            getResults(page = 1) {

                  this.$Progress.start();

                  axios.get('api/user?page=' + page).then(({ data }) => (this.users = data.data));

                  this.$Progress.finish();
            },
            updateUser(){
                this.$Progress.start();
                // console.log('Editing data');
                this.form.put('api/user/'+this.form.id)
                .then((response) => {
                    // success
                    $('#addNew').modal('hide');
                    Toast.fire({
                      icon: 'success',
                      title: response.data.message
                    });
                    this.$Progress.finish();
                        //  Fire.$emit('AfterCreate');

                    this.loadUsers();
                })
                .catch(() => {
                    this.$Progress.fail();
                });

            },
            editModal(user){

                this.editmode = true;
                this.form.reset();
                this.loadLocations();
                this.loadAccounts();
                $('#addNew').modal('show');
                this.form.fill(user);
                if(user.cashAccounts == null)
                this.form.cashAccounts = {};
                if(user.cardAccounts == null)
                this.form.cardAccounts = {};
                if(user.onlineAccounts == null)
                this.form.onlineAccounts = {};
                if(user.gpayAccounts == null)
                this.form.gpayAccounts = {};
                if(user.phonepeAccounts == null)
                this.form.phonepeAccounts = {};
                if(user.amazonpayAccounts == null)
                this.form.amazonpayAccounts = {};
            },
            newModal(){
                this.loadLocations();
                this.loadAccounts();
                this.editmode = false;
                this.form.reset();
                $('#addNew').modal('show');
            },
            deleteUser(id){
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
                                this.form.delete('api/user/'+id).then(()=>{
                                        Swal.fire(
                                        'Deleted!',
                                        'Your file has been deleted.',
                                        'success'
                                        );
                                    // Fire.$emit('AfterCreate');
                                    this.loadUsers();
                                }).catch((data)=> {
                                  Swal.fire("Failed!", data.message, "warning");
                              });
                         }
                    })
            },
          loadUsers(){
            this.$Progress.start();


              axios.get("api/user").then(({ data }) => (this.users = data.data));


            this.$Progress.finish();
          },

          createUser(){

              this.form.post('api/user')
              .then((response)=>{
                  $('#addNew').modal('hide');

                  Toast.fire({
                        icon: 'success',
                        title: response.data.message
                  });

                  this.$Progress.finish();
                  this.loadUsers();

              })
              .catch(()=>{

                  Toast.fire({
                      icon: 'error',
                      title: 'Some error occured! Please try again'
                  });
              })
          }

        },
        mounted() {
            console.log('User Component mounted.')
        },
        created() {

            this.$Progress.start();
            this.loadUsers();
            this.$Progress.finish();
        }
    }
</script>
