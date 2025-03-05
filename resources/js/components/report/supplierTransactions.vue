<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">

            <div class="row" v-if="is('Super Admin') || is('Admin')">

                <form @submit.prevent="generate()">

                  <div class="form-group">
                      <label>Supplier</label>
                      <select class="form-control"  v-model="form.supplier_id">
                        <option
                            v-for="(sup,index) in suppliers" :key="index"
                            :value="index">{{ sup }}</option>
                      </select>
                      <has-error :form="form" field="supplier_id"></has-error>
                  </div>

            <date-picker v-model="form.date" type="date" range></date-picker>

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
                <th>Date</th>
                <th>Bill No</th>
                <th>Bill Amount</th>
                <th>Payment</th>


              </tr>
            </thead>
            <tbody>
               <tr v-for="(res,index) in results.purchases">

                 <td>{{res.created_at | date}}</td>
                 <td>{{res.id}}</td>

                 <td>{{res.grand_total}}</td>

                 <td></td>


              </tr>

              <tr v-for="(res,index) in results.payments">

                <td>{{res.created_at | date}}</td>
                <td>{{res.purchase_id}}</td>

                <td></td>

                <td>{{res.amount}}</td>


             </tr>
             <tr v-if="results.purchases || results.payments">
               <td></td>
               <td><b>TOTAL</b></td>
               <td ><b>{{results.purchases.reduce((total, obj) => parseFloat(obj.grand_total) + total,0)}}</b></td>
               <td><b>{{results.payments.reduce((total, obj) => parseFloat(obj.amount) + total,0)}}</b></td>
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
import moment from 'moment'
    export default {
        data () {
            return {
              suppliers: null,
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
                this.form.post('/api/report/supplier/transactions')
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


        filters: {
  date: function (date) {

    return   moment.utc(date).local().format('DD-MM-YYYY');

  },



},

    }
</script>
