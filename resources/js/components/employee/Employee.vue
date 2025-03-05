<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">

            <div class="card" v-if="is('Super Admin') || is('Admin')">
              <div class="card-header">
                <h3 class="card-title">Employee List</h3>

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
                      <th>Last Updated By</th>

                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="item in items.data" :key="item.id">

                      <td>{{item.id}}</td>
                      <td >{{item.name}}</td>
                      <td>{{item.code}}</td>
                      <td>{{item.user_name}}</td>

                      <td>
                        <div class="btn-group">
                    <button type="button" class="btn btn-info">Action</button>
                    <button type="button" class="btn btn-info dropdown-toggle dropdown-icon" data-toggle="dropdown" aria-expanded="false">
                      <span class="sr-only">Toggle Dropdown</span>
                      <div class="dropdown-menu" role="menu" style="">


                          <a class="dropdown-item" href="#" @click.prevent="paySalaryModal(item)">PAY SALARY</a>

                          <a class="dropdown-item" href="#" @click.prevent="attendance(item)">Attendance</a>
                          <a class="dropdown-item" href="#" @click.prevent="editModal(item)">EDIT</a>
                          <a class="dropdown-item" href="#" @click.prevent="RemoveItem(item.id)">DELETE</a>

                      </div>
                    </button>
                  </div>


                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="items" @pagination-change-page="getResults"></pagination>
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
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" v-show="!editmode">Create New Employee</h5>
                    <h5 class="modal-title" v-show="editmode">Update Employee</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- <form @submit.prevent="createUser"> -->

                <form @submit.prevent="editmode ? update() : create()">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input v-model="form.name" required type="text" name="name"
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
                            <label>Category</label>
                            <select class="form-control" v-model="form.employee_category_id">
                              <option
                                  v-for="(cat,index) in categories" :key="index"
                                  :value="index"
                                  :selected="index == form.employee_category_id">{{ cat }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Kitchen</label>
                            <select class="form-control" v-model="form.kitchen_id">
                              <option
                                  v-for="(cat,index) in kitchens" :key="index"
                                  :value="index"
                                  :selected="index == form.kitchen_id">{{ cat }}</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Salary Paying Frequency</label>
                            <select required class="form-control"  v-model="form.salary_frequency">
                              <option value="Monthly">Monthly</option>
                              <option value="Daily">Daily</option>
                              <option value="Hourly">Hourly</option>
                            </select>
                        </div>

                          <div class="form-group">
                              <label>Salary</label>
                              <input v-model="form.salary" required type="text" name=""
                                  class="form-control">
                          </div>
                          <div class="form-group">
                              <label>Is Waiter?</label>
                                <input v-model="form.waiter" checked  type="checkbox" >
                          </div>
                          <div class="form-group">
                              <label>can Login?</label>
                                <input v-model="form.login"   type="checkbox" >
                          </div>
                          <div class="form-group">
                              <label>Address</label>
                              <input v-model="form.address"  type="text" name=""
                                  class="form-control">
                          </div>
                          <div class="form-group">
                              <label>Area</label>
                              <input v-model="form.area"  type="text" name=""
                                  class="form-control">
                          </div>
                          <div class="form-group">
                              <label>District</label>
                              <input v-model="form.district"  type="text" name=""
                                  class="form-control">
                          </div>
                          <div class="form-group">
                              <label>Phone</label>
                              <input v-model="form.phone"  type="text" name=""
                                  class="form-control">
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
        <div class="modal fade" id="paySalary" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" >Pay Salary</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- <form @submit.prevent="createUser"> -->

                <form @submit.prevent="paySalary()">
                    <div class="modal-body">
                      <div class="form-group" style="padding-left:800px">
                  <label>Date/Time</label>
                <!-- <date-picker v-model="form.datetime" placeholder="OPTIONAL" type="datetime" format="DD-MM-YYYY h:m:s A" :use12h="true"></date-picker>-->
                <input v-model="form.datetime1" placeholder="OPTIONAL" type="date" required>
                </div>


                        <div class="col-3">
                          <div class="form-group">
                              <label>FROM - TO</label>
                            <date-picker v-model="form.fromTo" type="datetime" :use12h="true" range></date-picker>
                          </div>
                        </div>
                        <div class="col-3">
                          <div class="form-group">
                              <label>Salary</label>
                              <input v-model="form.actual_salary" required type="text" name=""
                                  class="form-control">
                          </div>
                        </div>

                        <div class="col-3">
                          <div class="form-group">
                              <label>Note</label>
                              <input v-model="form.note"  type="text" name=""
                                  class="form-control">
                          </div>
                        </div>

                        <div class="col-3">
                          <div class="form-group">
                              <label>Is Advance?</label>
                                <input v-model="form.advance"   type="checkbox" >
                          </div>
                        </div>
                        <div class="col-3">
                          <div class="form-group">
                              <label>Is Incentive?</label>
                                <input v-model="form.incentive"   type="checkbox" >
                          </div>
                        </div>
                        <div class="col-3">
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
                        <div class="col-3" style="padding-top:33px">



                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                  </form>
                </div>
            </div>
        </div>




        <div class="modal fade" id="attendance" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" >Attendance</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- <form @submit.prevent="createUser"> -->

                <form @submit.prevent="submitAttendance()">
                    <div class="modal-body">


                        <div class="col-3">
                          <div class="form-group">
                              <label>Date / Time</label>
                            <date-picker v-model="form.dateTime" type="datetime" :use12h="true"></date-picker>
                          </div>
                        </div>
                        <div class="col-3">
                          <div class="form-group">
                              <label>In Time</label>
                                <input v-model="form.type" value="i" :name="type" type="radio" >
                          </div>
                          <div class="form-group">
                              <label>Out Time</label>
                                <input v-model="form.type" :name="type" value="o"  type="radio" >
                          </div>
                        </div>

                        <div class="col-3" style="padding-top:33px">



                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                        <button type="submit" class="btn btn-primary">Create</button>
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
                accounts: null,
                items : {},
                categories : {},
                kitchens : {},

                extras: [],

                form: new Form({
                  type: null,
                    id : '',
                    name: '',
                    salary: '',
                    code: '',
                    datetime1: '',
                    tax_number: '',
                    waiter: true,
                    login: false,
                    extras: [
                      {
                        key: '',
                        value: ''
                      }
                    ],

                        id:'',
                        actual_salary: '',
                        salary_frequency: '',
                        account_id: '',
                        note: '',
                        fromTo: [
                          Date(),
                          Date()
                        ]
                        

                })
            }
        },
        methods: {

            getResults(page = 1) {

                  this.$Progress.start();

                  axios.get('/api/employee?page=' + page).then(({ data }) => (this.items = data.data));

                  this.$Progress.finish();
            },
            addExtra()
            {
              this.form.extras.push({ field: '', value: '' });
            },
            addSalary()
            {
              this.form.salaries.push({ salary: '', salary_frequency: '' , accout_id: '', note: '' });
            },
            update(){
                this.$Progress.start();
                this.form.put('/api/employee/'+this.form.id)
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
            editModal(item){
                this.editmode = true;
                this.form.reset();
                $('#addNew').modal('show');
                this.form.fill(item);
            },
            paySalaryModal(item){


                this.form.reset();
                this.form.employee_id = item.id;
                $('#paySalary').modal('show');
                //this.form.fill(item);
            },

            attendance(item){


                this.form.reset();
                this.form.employee_id = item.id;
                $('#attendance').modal('show');
                //this.form.fill(item);
            },

            newModal(){
                this.editmode = false;
                this.form.reset();
                $('#addNew').modal('show');

            },

            loadCategories(){
                axios.get("/api/employee/category/list").then(({ data }) => (this.categories = data.data));
            },
            loadKitchens(){
                axios.get("/api/kitchen/list").then(({ data }) => (this.kitchens = data.data));
            },
            loadAccounts(){
                axios.get("/api/account/list").then(({ data }) => (this.accounts = data.data));
            },

            load(){

                    axios.get("/api/employee").then(({ data }) => (this.items = data.data));

            },
            paySalary()
            {
              this.form.post('/api/EmployeeSalary')
              .then((response)=>{
                  $('#paySalary').modal('hide');

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
            submitAttendance()
            {
              this.form.post('/api/EmployeeAttendance')
              .then((response)=>{
                  $('#attendance').modal('hide');

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
            create(){

                this.form.post('/api/employee')
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
                              this.form.delete('/api/employee/'+id).then(()=>{
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
            this.loadCategories();
            this.loadKitchens();
            this.loadAccounts();
            this.$Progress.finish();
        },

    }
</script>
