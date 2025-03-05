<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">

            <div class="row" v-if="is('Super Admin') || is('Admin')">
                <form @submit.prevent="generate()">
            <date-picker v-model="form.date" type="date" range></date-picker>

            <button type="submit" class="btn btn-success">Show</button>
            <button @click="downloadExcel" class="btn btn-success">Download</button>
          </form>
            </div>
            <!-- /.card -->
          </div>
        </div>

        <div class="row">
          <h5>ALL ITEMS</h5>
          <table id="table1" class="table table-hover">
            <thead>
              <tr>
                <th>Date / Time</th>
                <th>Item</th>
                <th>Kitchen</th>
                <th>Employee</th>
                <th>Quantity</th>

              </tr>
            </thead>
            <tbody>
               <tr v-for="(res,index) in results['all_items']">

                <td>{{res.created_at | date}}</td>
                <td>{{res.name}}</td>
                <td>{{res.kitchen_name}}</td>
                <td>{{res.employee_name}}</td>
                <td>{{res.quantity}}</td>



              </tr>
            </tbody>
          </table>
        </div>
        <div class="row">
          <h5>ITEMS BY NAME</h5>
          <table class="table table-hover">
            <thead>
              <tr>

                <th>Item</th>

                <th>Quantity</th>

              </tr>
            </thead>
            <tbody>
               <tr v-for="(res,index) in results['items_by_name']">


                <td>{{index}}</td>

                <td>{{res.reduce((total, obj) => parseFloat(obj.quantity) + total,0)}}</td>



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
            downloadExcel()
          {
           TableToExcel.convert(document.getElementById("table1"));
          },


            generate(){
              this.$Progress.start();
                this.form.post('/api/report/product/consumption')
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


        },
        mounted() {
            window.scrollTo(0, 0)
        },
        created() {

            this.$Progress.start();

            this.$Progress.finish();
        },
        filters: {
      date: function (date) {

      return   moment.utc(date).local().format('DD-MM-YYYY / hh:mm A');

      },


      },


    }
</script>
