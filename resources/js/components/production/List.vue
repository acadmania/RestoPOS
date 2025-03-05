<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">

            <div class="card" v-if="$gate.isAdmin()">
              <div class="card-header">
                <h3 class="card-title">Production List</h3>


              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>From</th>
                      <th>To</th>
                      <th>Status</th>

                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="consumption in consumptions.data" :key="consumption.id">

                      <td>{{consumption.id}}</td>
                      <td >{{consumption.from_location_name}}</td>
                      <td >{{consumption.to_location_name}}</td>

                      <td>{{consumption.status}}</td>

                      <td>

                        <div class="btn-group">
                    <button type="button" class="btn btn-info">Action</button>
                    <button type="button" class="btn btn-info dropdown-toggle dropdown-icon" data-toggle="dropdown" aria-expanded="false">
                      <span class="sr-only">Toggle Dropdown</span>
                      <div class="dropdown-menu" role="menu" style="">

                        <a class="dropdown-item" href="#" @click.prevent="$router.push(`/consumption/view/${consumption.id}`)">VIEW</a>
                        <a class="dropdown-item" v-if="consumption.status !== 'Completed'" href="#" @click.prevent="completeModal(consumption.id)">COMPLETED</a>

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
                  <pagination :data="consumptions" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>


        <div v-if="!$gate.isAdmin()">
            <not-found></not-found>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="completeModal" tabindex="-1" role="dialog" aria-labelledby="paymentModal" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" >Production Entry</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- <form @submit.prevent="createUser"> -->

                <form @submit.prevent="productionComplete()">
                    <div class="modal-body">
                      <div class="card-body table-responsive p-0">
                        <table class="table table-head-fixed text-nowrap" id="items">
                          <thead>
                            <tr>
                              <th>Details</th>
                              <th>Quantity</th>
                              <th>Cost</th>
                              <th>Price</th>
                              <th></th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-for="item in form.items" :key="item.id">

                             <td>{{item.name}}</td>
                             <td ><input v-model="item.quantity"type="text"></td>
                             <td ><input   v-model.number="item.cost"  type="text"></td>
                             <td ><input   v-model.number="item.price" type="text"></td>



                           </tr>

                          </tbody>
                        </table>
                      </div>


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
    export default {
        data () {
            return {
              item:{},
                consumptions : {},
                form: new Form({
                  items:{},

                  current_consumption_id: null,
                }),
            }
        },

        methods: {
          getResults(page = 1) {
                this.$Progress.start();
                axios.get('/api/consumption?page=' + page).then(({ data }) => (this.consumptions = data.data));
                this.$Progress.finish();
          },
          productionComplete()
          {
            this.$Progress.start();
            this.form.post('/api/consumption/complete')
            .then((response)=>{
                $('#completeModal').modal('hide');

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
                if(this.$gate.isAdmin()){
                    axios.get("/api/consumption").then(({ data }) => (this.consumptions = data.data));
                }
            },
            completeModal(id)
            {

              this.form.reset();

              this.form.current_consumption_id = id;
              axios.get("/api/consumption/product/get/"+id).then(({ data }) => (this.form.items = data.data));
              $('#completeModal').modal('show');
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

    }
</script>
