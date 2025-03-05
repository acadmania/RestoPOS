<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">

            <div class="row" v-if="is('Super Admin') || is('Admin')">
                <form @submit.prevent="generate()">

                    <div class="form-group">
                        <label>Supplier</label>
                        <select multiple class="form-control" @change="form.items=[]" v-model="form.supplier_id">
                          <option
                              v-for="(sup,index) in suppliers" :key="index"
                              :value="index">{{ sup }}</option>
                        </select>
                        <has-error :form="form" field="supplier_id"></has-error>
                    </div>


            <button type="submit" class="btn btn-success">Show</button>



          </form>


          <download-excel :data="results">
  <button class="btn btn-success">DOWNLOAD</button>
</download-excel>
            </div>
            <!-- /.card -->
          </div>
        </div>

        <div class="row">

          <table class="table table-hover">
            <thead>
              <tr>
                <th>Supplier Name</th>
                <th>Product Name</th>
                <th>Cost</th>
                <th>Stock Available</th>
                <th>Total Cost</th>

              </tr>
            </thead>
            <tbody>
               <tr v-for="(res,index) in results">

                <td>{{res.supplier_name}}</td>
                <td>{{res.product_name}}</td>
                <td>{{res.cost}}</td>
                <td>{{res.quantity}}</td>
                <td>{{res.total}}</td>

              </tr>
            </tbody>
          </table>
        </div>
        <div v-if="!is('Super Admin') && !is('Admin')">
            <not-found></not-found>
        </div>

        <!-- Modal -->

    </div>
  </section>
</template>

<script>
    export default {
        data () {
            return {

              suppliers: [],
                editmode: false,
                results: {},
                date : null,
                form: new Form({
                  supplier_id: [],
                    id : '',
                    name: '',
                    code: '',
                    balance: '',

                })
            }
        },
        methods: {



            generate(){
              this.$Progress.start();
                this.form.post('/api/report/supplier/stock')
                .then((response)=>{


                    Toast.fire({
                            icon: 'success',
                            title: response.data.message
                    });
                    this.results = response.data;
                    this.$Progress.finish();

                })
                .catch(()=>{
                    Toast.fire({
                        icon: 'error',
                        title: 'Some error occured! Please try again'
                    });
                })
            },

            loadSuppliers(){
                axios.get("/api/supplier/list").then(({ data }) => (this.suppliers = data.data));
            },


        },
        mounted() {
            window.scrollTo(0, 0)
        },
        created() {

            this.$Progress.start();
            this.loadSuppliers();
            this.$Progress.finish();
        },

    }
</script>
