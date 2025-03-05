<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12  d-print-none">

            <div class="row" v-if="is('Super Admin')">
                <form @submit.prevent="generate()">
            <date-picker v-model="form.date" type="date" range></date-picker>

            <button type="submit" class="btn btn-success">Show</button>
          </form>
            </div>
            <!-- /.card -->
          </div>
        </div>

        <div class="row">

          <table class="table table-hover">
            <thead>
              <tr>
                <!-- <th></th> -->

                <th>Account Name</th>
                <th>Sales</th>
                <th>Purchase</th>
                <th>Expense</th>
                <th>Total</th>
                <th>Balance</th>






              </tr>
            </thead>
            <tbody>



                <tr v-for="(res,ind) in results">
                <td>{{ind}}</td>
                <td>{{ res.sale }}</td>
                <td>{{ res.purchase }}</td>
                <td>{{ res.expense }}</td>
                <td>{{ res.sale + res.purchase + res.expense }}</td>
                <td>{{ res.balance }}</td>
                <tr>
                    <td>Total</td>
                    <td>{{Object.values(this.results).reduce((total, obj) => parseFloat(obj.sale) + total,0) }} </td>
                    <td>{{Object.values(this.results).reduce((total, obj) => parseFloat(obj.purchase) + total,0) }} </td>
                    <td>{{Object.values(this.results).reduce((total, obj) => parseFloat(obj.expense) + total,0) }} </td>

                </tr>
                </tr>



            </tbody>
          </table>
        </div>
        <div v-if="!is('Super Admin')">
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
                this.form.post('/api/report/account')
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
            scrollToTop() {
                window.scrollTo(0,0);
            },


        },
        mounted() {
            console.log('Component mounted.')
        },
        created() {

            this.$Progress.start();
            this.scrollToTop();
            this.$Progress.finish();
        },

        filters: {
    date: function (date) {

    return   moment.utc(date).local().format('DD-MM-YYYY hh:mm A');

  },


},


    }
</script>
