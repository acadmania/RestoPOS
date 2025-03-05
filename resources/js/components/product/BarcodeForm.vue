<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">

            <div class="card" v-if="is('Super Admin')">
              <div class="card-header">
                <h3 class="card-title">Barcode Generate</h3>


                    <label style="margin-left:50px">Column Count</label>
                    <select class="" v-model="labeltype">
                    
                      <option value="3 column">3 Column</option>
                      <option value="2 column">2 Column</option>

                    </select>



                    <label style="margin-left:50px">Price Format</label>
                    <select class="" v-model="printPrice">

                      <option value="MRP">MRP</option>
                      <option value="Selling Price">Selling Price</option>

                    </select>



                <div class="card-tools">

                  <button type="button" class="btn btn-sm btn-primary" @click="generateBarcode">

                      GENERATE
                  </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Code</th>
                      <th>Total Stock</th>
                      <th>Label Quantity</th>
                      <th>Custom Text</th>

                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="item in products" :key="item.id">

                      <td>{{item.id}}</td>
                      <td >{{item.name}}</td>
                      <td>{{item.code}}</td>
                      <td>{{item.locations.reduce((total, obj) => parseFloat(obj.pivot.quantity) + total,0)}}</td>
                      <td><input type="text" v-model="item.q"></td>
                      <td><input type="text" v-model="item.custom"></td>

                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">

              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>


        <div v-if="!is('Super Admin')">
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

                products : [],
                labeltype:null,
                printPrice: null,
            }
        },
        methods: {
            load(){
                Object.assign(this.products, JSON.parse(localStorage.products));
            },

            generateBarcode()
            {
              if(this.labeltype && this.printPrice)
              {
                localStorage.products = JSON.stringify(this.products);
                localStorage.labeltype = this.labeltype;
                localStorage.printPrice = this.printPrice;
                this.$router.push('/barcode/generate');
              }
              else
              {
                alert("select barcode label type & price");
              }
            }


        },
        mounted() {
            window.scrollTo(0, 0)
        },
        created() {

            this.$Progress.start();
            this.load();
            this.$Progress.finish();
        },

    }
</script>
