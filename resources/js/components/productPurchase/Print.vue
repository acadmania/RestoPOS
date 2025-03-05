<template>
  <section class="content" style="font-size:18px;padding-right:1mm;    font-family: arial;
    font-weight: 600;">
    <div class="container-fluid">
  <div class="row">
      <div class="col 12" style="text-align:center;line-height:18px">
        <img v-if="settings.logo" :src="'/storage/'+settings.logo" height="100"/>
    <br><span style="font-size:20px;"><b>{{settings.business_name}}</b></span>
    <hr style="background-color: black;margin:0">
    <span style="font-size:13px ">{{settings.address}}<br>
    Phone: {{settings.phone}} <br> </span>
    <span  style="font-size:15px " v-if="settings.gst_number">GST NO: {{settings.gst_number}}</span>
  </div>
  </div>
<hr style="background-color: black;margin:0">
  <div class="row" style="line-height:18px">
  <div class="col 12" style="text-align:right; font-size:13">
    Date: {{sale.created_at | moment }} <br>
    Time: {{sale.created_at | time }} <br>

  </div>
</div>
<div class="row" style="line-height:18px">
  <div class="col 12">
  Purchase Order No: {{sale[0].id}}  <br>

  <span >    Name: {{sale[0].supplier_name}}<br>

    </span>
  </div>


  </div>









  <table class="table tblCool" style="font-size:12px">
    <thead>
      <tr>
        <th style="width:50%;text-align:left;padding-left: 0;padding-right: 0;">Description</th>


        <th style="width:8%;text-align:right;padding-left: 0;padding-right: 0;">Qty</th>

      </tr>
    </thead>
    <tbody>
      <tr v-for="(item, index) in sale[1]" :key="item.id">
       <td style="width:50%">{{item.product_name}}</td>


       <td  style="width:8%;text-align:center">{{parseInt(item.quantity)}}</td>

     </tr>
    </tbody>
  </table>
  <hr style="background-color: black;margin:0">





  <hr style="background-color: black;margin:0" v-if="settings.gst_number && settings.gst_summary">




    <hr style="background-color: black;margin:0">
    <span style="font-size:12px">Software By: restoPOS</span>
  </div>
</section>
</template>
<script>
import moment from 'moment'
    export default {

        data () {
            return {
              sale: [
                {
                  id:null
                }
              ],
              options: [],
              form: new Form({
                items: [],
                customer_id: null,
                total_subtotal: 0,
                total_cost: 0,
                total_taxtotal: 0,
                total_quantity: 0,
                total_discount:0,
                shipping: 0,
                grand_total: 0,
                grand_discount: 0,
                grand_tax_total: 0,
              }),
              customers: [],
              locations: [],
              accounts: [],
              selected: null,
              settings: null
  }

        },


        methods: {

          loadSale(){


              axios.get("/api/purchase/get/"+this.$route.params.id).then(({ data }) => (
                this.sale = data.data,
                this.settings = data.settings
              ));


          },




        },
        filters: {
  moment: function (date) {

    return   moment.utc(date).local().format('DD-MM-YYYY');

  },
  time: function (date) {

    return   moment.utc(date).local().format('hh:mm A');

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
          created() {


              this.$Progress.start();
              //this.load();

              this.loadSale();


              this.$Progress.finish();

          },
          mounted()
          {
            window.onafterprint = function(){
     window.close();
}

},
updated: function () {

  setTimeout(() => window.print(), 500);

}

      }
  </script>

  <style media="print">
    .tblCool td{
      padding : 0px !important;
    }

  .table .t_blue {
    background-color:#387AB5 !important;
    color:white;
  }
  </style>
