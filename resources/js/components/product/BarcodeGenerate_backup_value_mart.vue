<template>
  <div style="text-align:center;padding-left:7%;padding-right:7%">


  <div style="width: 100%; overflow: hidden;">
        <div class="row" style="line-height:9px">
          <template v-for="p in products">
          <template v-for="q in parseInt(p.q)">
          <div class="w" style="padding-top:2mm;min-height:21.7mm;max-height:21.5mm; width:33%; float: left; text-align:center;font-family: 'Ubuntu', sans-serif;">

            <span style="font-size:11px"><b>
              <span v-if="settings.label_business_name" >{{settings.label_business_name}}</span>
              <span v-else >{{settings.business_name}}</span>
            </b><br>
            
            {{p.name}}<br></span>
            <span style="font-size:10px" v-if="p.sale_price !== null">Rs. {{p.sale_price}}</span>
             <span style="font-size:10px" v-if="p.sale_price == null">Rs. {{p.price}}</span><br>
            <span style="font-size:7px" v-if="p.custom !== null">{{p.custom}}</span>
            <div>
            <barcode marginTop="-5" marginBottom="2" textMargin="0" height="12" width="1" fontSize="8" v-bind:value="p.code ? p.code : p.id">
    Show this if the rendering fails.
  </barcode>
</div>

            <!-- /.card -->
          </div>
          </template>
           </template>
        </div>
        <!-- Modal -->

    </div>
    </div>

</template>

<script>
    export default {

        data () {
            return {
                settings:null,
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

          axios.get("/api/customer/list").then(({ data }) => (

            this.settings = data.settings
          ));
            this.$Progress.start();
            this.load();
            this.$Progress.finish();
        },

    }
</script>
