<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">

            <div class="row" v-if="is('Super Admin') || is('Admin')">
                <form @submit.prevent="generate()">
            <date-picker v-model="form.date" type="date" range></date-picker>
            <div class="form-group" style="margin-top: 15px;">
                <input placeholder="Search Product" class="form-control"  v-model="searchTerm">
              </div>
            <button type="submit" class="btn btn-success">Show</button>
            <button @click="downloadExcel" class="btn btn-success">Download</button>
          </form>
            </div>
            <!-- /.card -->
          </div>
        </div>

        <div class="row">

          <table id="table1" class="table table-hover">
            <thead>
              <tr>
                <th>Detail</th>
                <th>Purchase Count</th>
                <th>Total Amount</th>

              </tr>
            </thead>
            <tbody>
               <tr v-for="(res,index) in resultQuery">

                <td>{{res.name}}</td>
                <td>{{res.quantity}}</td>
                <td>{{res.cost.toFixed(2)}}</td>

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
                searchTerm: null,
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
                this.form.post('/api/report/product/purchase')
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

        computed: {
          resultQuery(){
            if(this.searchTerm){
            return this.results.filter((res)=>{

                return this.searchTerm.toLowerCase().split(' ').every(v => res.name.toLowerCase().includes(v))
            })
            }else{
                return this.results;
            }
            }
        }


}
</script>
