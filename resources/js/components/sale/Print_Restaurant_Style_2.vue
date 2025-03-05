<template>
  <section class="content" style="font-size:18px;padding-right:1mm;    font-family: arial;
    font-weight: 600;">
    <div class="container-fluid">
  <div class="row">
      <div class="col 12" style="text-align:center;line-height:18px">
        <img v-if="settings.logo" :src="'/storage/'+settings.logo" height="100"/>
    <br><span v-if="settings.logo == null" style="font-size:20px;"><b>{{settings.business_name}}</b></span>

    <span style="font-size:13px ">{{settings.address}}<br>
    Phone: {{settings.phone}} <br> </span>
    <span  style="font-size:15px " v-if="settings.gst_number">GST NO: {{settings.gst_number}}</span><br>
    <span  style="font-size:15px " >Date: {{sale.created_at | moment }} {{sale.created_at | time }}</span><br>
    <div style="font-size:20px; background-color:black; padding: 15px;color:white;margin-left:30px;margin-right:30px;margin-top:10px;margin-bottom:10px;">Order # AFC-{{sale.id}} </div>
    <span v-if="sale.customer.id!==settings.customer_id">    Name: {{sale.customer.name}}<br v-if="sale.customer.address"> {{sale.customer.address}}<br v-if="sale.customer.phone"> {{sale.customer.phone}} <br>
    <span v-if="sale.note">{{sale.note}}</span></span>
  </div>
  </div>



  <table class="table tblCool" style="font-size:12px">
    <thead>
      <tr>
        <th style="width:50%;text-align:center;padding-left: 0;padding-right: 0;">Description</th>

        <th style="width:11%;text-align:center;padding-left: 0;padding-right: 0;">Rate</th>
        <th style="width:8%;text-align:center;padding-left: 0;padding-right: 0;">Qty</th>
        <th style="width:20%;text-align:center;padding-left: 0;padding-right: 0;">Amt</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="(item, index) in sale.items" :key="item.id">
       <td style="width:50%">{{item.food_name}}</td>

       <td  style="width:11%;text-align:center">{{item.selling_price | currency}}</td>
       <td  style="width:8%;text-align:center">{{parseInt(item.quantity)}}</td>
       <td style="width:20%;text-align:right">{{item.subtotal | currency}}</td>
     </tr>
    </tbody>
  </table>

  <div class="row" style="font-size:18px">

  <div class="col 12 " style="text-align:center;font-size:20px">
    <span v-if="sale.discount > 0">Sub</span> Total: {{sale.total | currency}}<br>
    <span v-if="sale.discount > 0">Discount: {{sale.discount | currency}}<br>
    Total:{{sale.grand_total | currency}}</span>
  </div>

  </div>

  <span v-if="!($route.params.return)">
  <div  v-if="sale.discounts.length>0 && !($route.params.return)">
  <hr style="background-color: black;margin:0">
  <div v-for="disc in sale.discounts" class="row" style="font-size:15px">
  <div class="col 6">
  {{disc.title}}
  </div>
  <div class="col 6 " style="text-align:right;text-align:right;font-size:15px">
    {{disc.value}}
  </div>
  </div>
  <div class="row">
    <div class="col 6 " style="text-align:right;text-align:right;font-size:15px">
  </div>
    <div class="col 6 " style="text-align:right;text-align:right;font-size:15px">
      {{sale.discounts.reduce((total, obj) => parseFloat(obj.value) + total,0)}}
  </div>
  </div>
</div>

</span>
  <div v-if="settings.gst_number" class="row" style="font-size:14px; text-align:center">
  <div class="col 12">

    *All Rates Are Inclusive Of Taxes
  </div>
  </div>

  <hr style="background-color: black;margin:0" v-if="settings.gst_number && settings.gst_summary">
    <div v-if="settings.gst_number && settings.gst_summary" class="row" style="font-size:14px; text-align:center">
    <div class="col 12">
    GST Summary
    </div>
    </div>
    <table v-if="settings.gst_number && settings.gst_summary" class="table tblCool" style="font-size:12px">
      <thead>
        <tr>
          <th>%</th>
          <th>Sales</th>
          <th>CGST</th>
          <th>SGST</th>
          <th>Tot. Tax</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in sale.gsts">
           <td>{{index}}</td>
           <td>{{item.reduce((total, obj) => parseFloat(obj.subtotal) + total,0)  | currency}}</td>
           <td>{{(item.reduce((total, obj) => parseFloat(obj.subtotal) + total,0)*index/100)/2  | currency}}</td>
           <td>{{(item.reduce((total, obj) => parseFloat(obj.subtotal) + total,0)*index/100)/2  | currency}}</td>
           <td>{{item.reduce((total, obj) => parseFloat(obj.subtotal) + total,0)*index/100  | currency}}</td>
        </tr>
    </tbody>
    </table>


    <div style="font-size:15px; text-align:center; background-color:black; padding: 5px;color:white;margin-top:10px;margin-bottom:10px;">Thank You. Do visit us again! </div>

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

            if(this.$route.params.return)
            {
              axios.get("/api/sale/return/get/"+this.$route.params.id).then(({ data }) => (
                this.sale = data.data,
                this.settings = data.settings
              ));
            }
            else
            {
              axios.get("/api/sale/get/"+this.$route.params.id).then(({ data }) => (
                this.sale = data.data,
                this.settings = data.settings
              ));
            }

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

  setTimeout(() => window.print(), 1000);

}

      }
  </script>
  <style media="print">
  .table thead th, .table td {

    border-top: 1px solid #000;
    border-right: 1px solid #000;
    border-left: 1px solid #000;
    border-bottom: 1px solid #000;
}

  </style>
  <style media="print">
    .tblCool td{
      padding : 0px !important;
    }
    body{
      background-color: #ffffff !important;
    }
    .content-wrapper{
      background-color: #ffffff !important;
    }

  .table .t_blue {
    background-color:#387AB5 !important;
    color:white;
  }
  </style>
