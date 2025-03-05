<template>
    <section class="content">
      <div class="container-fluid">
          <div class="row">

            <div class="col-12">

              <div class="row" v-if="is('Super Admin') || is('Admin')">
                  <form @submit.prevent="generate()">
              <date-picker v-model="form.date" type="date" range></date-picker>

              <button type="submit" class="btn btn-success">Show</button>
            </form>
              </div>
              <!-- /.card -->
            </div>
          </div>

          <div class="row">

            <table class="table">
              <thead>
                <tr>
                  <th>Payment Method</th>
                </tr>
              </thead>
              <tbody>
                 <tr v-for="(res,index) in results">

                  <td>{{index}}</td>
                  <table class="table">

                    <tr>
                    <th>Sale id</th>
                    <th>Date</th>
                    <th>Amount</th>
                    </tr>
                    <tr v-for="r in res.sale">
                    <td>{{ r.sale_id }}</td>
                    <td>{{ r.created_at | date}}</td>
                    <td>{{ r.amount }}</td>
                    </tr>
                    <tr >
                        <td></td>
                        <td>Total</td>
                        <td>{{res.sale.reduce((total, obj) => parseFloat(obj.amount) + total,0)}}</td>
                    </tr>
                    </table>
                    <tr>
                        <td></td>
                        <td>Total Amount</td>
                        <td>{{ total_amount }}</td>
                    </tr>
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



              generate(){
                this.$Progress.start();
                  this.form.post('/api/report/payment_method')
                  .then((response)=>{


                      Toast.fire({
                              icon: 'success',
                              title: response.data.message
                      });
                      this.results = response.data[0],
                      this.total_amount = response.data[1];
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

    return   moment.utc(date).local().format('DD-MM-YYYY hh:mm A');

  },


},


      }
  </script>
