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
            <download-excel :data="results">
    <button class="btn btn-success">DOWNLOAD</button>
  </download-excel>
            <!-- /.card -->
          </div>
        </div>

        <div class="row">
          Note: This report will correctly work only if all the selling gst's are inclusive
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Bill #</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Sales Value (exc GST)</th>
                <th>Exempted Sales</th>
                <th>0.25% Sales</th>
                <th>0.25% GST</th>
                <th>3% Sales</th>
                <th>3% GST</th>
                <th>5% Sales</th>
                <th>5% GST</th>
                <th>12% Sales</th>
                <th>12% GST</th>
                <th>18% Sales</th>
                <th>18% GST</th>
                <th>28% Sales</th>
                <th>28% GST</th>
                <th>Total GST</th>
                <th>Sales Value (inc GST)</th>

              </tr>
            </thead>
            <tbody>
               <tr v-for="(res,index) in results">

                <td>{{res.bill_number}}</td>
                <td>{{res.name}}</td>
                <td>{{res.date | date}}</td>
                <td>{{res.salesExcludingGst}}</td>

                <td >{{res.exempted_total}}</td>

                <td>{{res.point25_total | currency}}</td>
                <td>{{res.point25_gst | currency}}</td>

                <td>{{res.three_total | currency}}</td>
                <td>{{res.three_gst | currency}}</td>

                <td>{{res.five_total | currency}}</td>
                <td>{{res.five_gst | currency}}</td>

                <td>{{res.twelve_total | currency}}</td>
                <td>{{res.twelve_gst | currency}}</td>

                <td>{{res.eighteen_total | currency}}</td>
                <td>{{res.eighteen_gst | currency}}</td>

                <td>{{res.twentyeight_total | currency}}</td>
                <td>{{res.twentyeight_gst | currency}}</td>



                <td>{{res.point25_total + res.three_gst + res.five_gst + res.twelve_gst + res.eighteen_gst + res.twentyeight_gst}}</td>
                <td>{{res.salesIncludingGst}}</td>

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
                this.form.post('/api/report/gst/sale/bill')
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

    return   moment.utc(date).local().format('DD-MM-YYYY');

  },
  currency: function (number) {
    if(number == null)
    {
      return 0.00;
    }
    else
    {
      return parseFloat(number).toFixed(2);
    }


  }


},

    }
</script>
