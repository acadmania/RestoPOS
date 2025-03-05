<template>
  <div style="text-align:center;padding-left:7%;padding-right:7%">


  <div style="width: 100%; overflow: hidden;">
        <div class="row"  :style="{lineHeight:labeltype=='2 column' ? '14px' : '9px'}">
          <template v-for="p in products">
          <template v-for="q in parseInt(p.q)">
          <div class="w" :style="{paddingTop:'2mm',minHeight:minHeight,maxHeight:maxHeight, width:width, float: left, textAlign:'center',fontFamily: 'Ubuntu, sans-serif'}">

            <span :style="{fontSize:labeltype=='2 column' ? '15px' : '11px'}"><b>
              <span v-if="settings.label_business_name" >{{settings.label_business_name}}</span>
              <span v-else >{{settings.business_name}}</span>
            </b><br>

            {{p.name}}<br></span>
            <span :style="{fontSize:labeltype=='2 column' ? '14px' : '10px'}" v-if="printPrice == 'Selling Price'">Rs. {{p.sale_price}}</span>
             <span :style="{fontSize:labeltype=='2 column' ? '14px' : '10px'}" v-if="printPrice == 'MRP'">Rs. {{p.price}}</span><br>
            <span :style="{fontSize:labeltype=='2 column' ? '9px' : '7px'}" v-if="p.custom !== null">{{p.custom}}</span>
            <div>
            <barcode v-if="labeltype=='2 column'" marginTop="-5" marginBottom="2" textMargin="0" height="16" width="1" fontSize="12" v-bind:value="p.code ? p.code : p.id">
    Show this if the rendering fails.
  </barcode>
            <barcode v-if="labeltype=='3 column'" marginTop="-5" marginBottom="2" textMargin="0" height="16" width="1" fontSize="8" v-bind:value="p.code ? p.code : p.id">
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
                labeltype: [],
                width:null,
                minHeight: null,
                maxHeight:null,
                printPrice: [],
            }
        },
        methods: {
            load(){

                Object.assign(this.products, JSON.parse(localStorage.products));
                this.labeltype = localStorage.labeltype;
                this.printPrice = localStorage.printPrice;

                if(this.labeltype == "2 column")
                {
                  this.minHeight = "25mm";
                  this.maxHeight = "25mm";
                    this.width="50%";
                }

                else if(this.labeltype == "3 column")
                {
                  this.minHeight = "21.7mm";
                  this.maxHeight = "21.5mm";
                    this.width="33%";
                }


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
