<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row" style="line-height:15px">
          <template v-for="p in products">
          <template v-for="q in parseInt(p.q)">
          <div class="col-4" style="text-align:center">

            <b>ValuePrice Mart</b><br>
            {{p.name}}<br>
            Rs.<span v-if="p.sale_price !== null">{{p.sale_price}}</span>
             <span style="font-size:12px" v-if="p.sale_price == null">{{p.price}}</span><br>
            <span style="font-size:10px" v-if="p.custom !== null">{{p.custom}}</span>
            <barcode marginTop="0" height="25" fontSize="10" v-bind:value="p.code ? p.code : p.id">
    Show this if the rendering fails.
  </barcode>


            <!-- /.card -->
          </div>
          </template>
           </template>
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
            }
        },
        methods: {
            load(){
                Object.assign(this.products, JSON.parse(localStorage.products));
            },

            generateBarcode()
            {
              localStorage.products = JSON.stringify(this.products);
              this.$router.push('/barcode/generate');
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
