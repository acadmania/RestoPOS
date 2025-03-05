<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">

            <div class="card" v-if="is('Super Admin') || is('Admin')">
              <div class="card-header">

                <h3 class="card-title">Customer Point Transactions | {{customer.name}} | {{customer.points}}</h3>
                <div class="card-tools">

                  <button type="button" class="btn btn-sm btn-primary" @click="redeem">
                      <i class="fa fa-plus-square"></i>
                      Redeem
                  </button>
                </div>

              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Sale ID</th>
                      <th>Opening Balance</th>
                      <th>Points</th>
                      <th>Credit/Debit</th>
                      <th>Closing Balance</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="transaction in transactions.data" :key="transaction.id">

                      <td>{{transaction.id}}</td>
                      <td >{{transaction.sale_id}}</td>

                      <td >{{transaction.opening_balance}}</td>
                      <td>{{transaction.points}}</td>
                      <td>{{transaction.type}}</td>
                      <td>{{transaction.closing_balance}}</td>


                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="transactions" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
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
                transactions : {},
                customer: null
            }
        },
        methods: {
          getResults(page = 1) {
                this.$Progress.start();
                axios.get('/api/customer/points/view/'+this.$route.params.id+'?page=' + page).then(({ data }) => (
                  this.transactions = data.data.transactions,
                  this.customer = data.data.customer
                ));
                this.$Progress.finish();
          },





        },
        mounted() {
            window.scrollTo(0, 0)
        },
        created() {

            this.$Progress.start();
            this.getResults();
            this.$Progress.finish();
        },

    }
</script>
