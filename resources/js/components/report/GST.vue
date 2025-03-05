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
                <th>%</th>
                <th>Item Count</th>
                <th>Total Price</th>
                <th>GST Total</th>

              </tr>
            </thead>
            <tbody>
               <tr v-for="(res,index) in results">

                <td>{{index}}</td>
                <td>{{res.reduce((total, obj) => parseFloat(obj.quantity) + total,0)}}</td>
                <td>{{res.reduce((total, obj) => parseFloat(obj.subtotal) + total,0)}}</td>
                <td>{{res.reduce((total, obj) => parseFloat(obj.subtotal)*index/(100+obj.gst_percentage) + total,0)}}</td>


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
                this.form.post('/api/report/gst')
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
