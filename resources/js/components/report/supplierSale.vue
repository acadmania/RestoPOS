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

            <date-picker v-model="form.date" type="date" range></date-picker>

            <button type="submit" class="btn btn-success">Show</button>
            This report correctly works if one product has one supplier
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
                <th>Sold Quantity</th>
                <th>Cost</th>
                <th>Price</th>
                <th>Total Cost</th>
                <th>Total Price</th>

              </tr>
            </thead>
            <tbody>
               <tr v-for="(res,index) in results">

                 <td>{{res.supplier}}</td>
                 <td>{{res.name}}</td>

                 <td>{{res.quantity}}</td>
                 <td>{{res.cost}}</td>
                 <td>{{res.price}}</td>
                 <td>{{res.total_cost}}</td>
                 <td>{{res.total_price}}</td>



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
                    id : '',
                    name: '',
                    code: '',
                    balance: '',

                })
            }
        },
        methods: {


          loadSuppliers(){
              axios.get("/api/supplier/list").then(({ data }) => (this.suppliers = data.data));
          },
            generate(){
              this.$Progress.start();
                this.form.post('/api/report/supplier/sales')
                .then((response)=>{


                    Toast.fire({
                            icon: 'success',
                            title: response.data.message
                    });
                    this.results = {};
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
