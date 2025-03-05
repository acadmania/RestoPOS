<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">

            <div class="card" v-if="is('Super Admin') || is('Admin')">
              <div class="card-header">
                <h3 class="card-title">BRANCH REGISTERS</h3>

                <div class="card-tools">


                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <thead>
                    <tr>

                      <th>Location</th>
                      <th>Status</th>

                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="userBranch in userBranches" >


                      <td >{{userBranch.name}}</td>
                      <td >{{userBranch.status}}</td>

                      <td>

                        <button v-if="userBranch.status=='close'" type="button" class="btn btn-sm btn-primary" @click="openRegisterModal(userBranch.branchId)">

                            OPEN REGISTER
                        </button>


                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">

              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>


        <div v-if="!is('Super Admin') && !is('Admin')">
            <not-found></not-found>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="openRegister" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Open Register</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- <form @submit.prevent="createUser"> -->

                <form @submit.prevent="submitOpenRegisterModal()">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Cash On Hand</label>
                            <input v-model="formOpenRegister.cash_on_hand" type="text" name="name"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                        <button type="submit" class="btn btn-primary">Open</button>
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
            flag: null,
              userBranch: null,
                editmode: false,
                userBranches : {},
                form: new Form({
                    id : '',
                    name: '',
                    code: '',
                    balance: '',

                }),
                formOpenRegister: new Form({
                    branchId : '',
                    cash_on_hand: '',


                }),
            }
        },
        methods: {


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

            openRegisterModal(branchId){
              this.formOpenRegister.branchId = branchId;
              $('#openRegister').modal('show');

            },

            submitOpenRegisterModal()
            {
              this.formOpenRegister.post("/api/register/open").then();
              $('#openRegister').modal('hide');
              this.$router.push('/pos');
            },

            newModal(){
                this.editmode = false;
                this.form.reset();
                $('#addNew').modal('show');
            },

            load(){
                axios.get("/api/pos/check/registers").then(({ data }) => (this.flag = data));

                    axios.get("/api/pos/list").then(({ data }) => (this.userBranches = data));

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
        watch:{

          flag : function(){

            if(this.flag)
            this.$router.push('/pos');
          }
        },
        created() {

            this.$Progress.start();
            this.load();
            this.$Progress.finish();
        },

    }
</script>
