<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">

            <div class="card" v-if="is('Super Admin') || is('Admin')">
              <div class="card-header">
                <h3 class="card-title">Employee Salary Transaction</h3>


              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Employee Name</th>
                      <th>Salary</th>
                      <th>Advance</th>
                      <th>Incentive</th>
                      <th>Account</th>
                      <th>Note</th>
                      <th>Transaction Date</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="t in transactions.data" :key="t.id">

                      <td>{{t.id}}</td>
                      <td >{{t.employee_name}}</td>
                      <td ><span v-if="t.advance !== 1 & t.incentive !==1">{{t.actual_salary}}</span></td>
                      <td ><span v-if="t.advance == 1">{{t.actual_salary}}</span></td>
                      <td ><span v-if="t.incentive == 1">{{t.actual_salary}}</span></td>
                      <td >{{t.account_name}}</td>
                      <td >{{t.note}}</td>
                      <td >{{t.created_at | date}}</td>
                      <td><a href="#"  @click.prevent="RemoveItem(t.id)">
                            <i class="fa fa-trash red"></i>
                      </a></td>


                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="transactions" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>


        <div v-if="!is('Super Admin') && !is('Admin')">
            <not-found></not-found>
        </div>

        <!-- Modal -->

    </div>
  </section>
</template>

<script>
import moment from 'moment'
    export default {

        data () {
            return {

                transactions : {},
                extras: [],

            }
        },

        methods: {

            getResults(page = 1) {

                  this.$Progress.start();

                  axios.get('/api/EmployeeSalary?page=' + page).then(({ data }) => (this.transactions = data.data));

                  this.$Progress.finish();
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
                              this.form.delete('/api/EmployeeSalary/'+id).then(()=>{
                                      Swal.fire(
                                      'Deleted!',
                                      'Your file has been deleted.',
                                      'success'
                                      );
                                  // Fire.$emit('AfterCreate');
                                  
                              }).catch((data)=> {
                                  Swal.fire("Failed!", data.message, "warning");
                              });
                        }
                  })

             
        },

        },
  
        mounted() {
            window.scrollTo(0, 0)
        },
        created() {


              axios.get("/api/EmployeeSalary").then(({ data }) => (this.transactions = data.data));

            this.$Progress.start();

            this.$Progress.finish();
        },
        filters: {
  date: function (date) {
    return   moment.utc(date).local().format('DD-MM-YYYY');
  },


},
        

    }
</script>
