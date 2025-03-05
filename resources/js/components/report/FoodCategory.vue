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

          <table class="table table-hover">
            <thead>
              <tr>
                <th>Detail</th>
                <th>Sales Count</th>
                <th>Total Amount</th>

              </tr>
            </thead>
            <tbody>
               <tr v-for="(res,index) in results">

                <td>{{res.name}}</td>
                <td>{{res.quantity}}</td>
                <td>{{res.cost}}</td>

              </tr>
              <tr>
                <td>Total</td>
                <td>{{Object.values(this.results).reduce((total, obj) => parseFloat(obj.quantity) + total,0) }} </td>
                <td>{{Object.values(this.results).reduce((total, obj) => parseFloat(obj.cost) + total,0) }} </td>
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
                this.form.post('/api/report/food/category')
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


    }
</script>
