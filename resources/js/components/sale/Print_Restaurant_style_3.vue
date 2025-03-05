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
  <div style="text-align:center;">
    <div style="text-decoration: underline;font-size:15;">INVOICE</div>
  </div>
    <div class="row" style="line-height:18px">
    <div class="col 12" style="text-align:right; font-size:13">
      Date: {{sale.created_at | moment }} <br>
      Time: {{sale.created_at | time }} <br>

    </div>
  </div>
  <div class="row" style="line-height:18px">
    <div class="col 12">
    <span v-if="$route.params.return">Return </span> Bill No: {{sale.id}}  <br>
    <span v-if="!($route.params.return)">
    <span v-if="sale.customer.id!==settings.customer_id">    Name: {{sale.customer.name}}<br v-if="sale.customer.address"> {{sale.customer.address}}<br v-if="sale.customer.phone"> {{sale.customer.phone}} </span> <br>
        <span v-if="sale.customer.points > 0 && sale.customer.id!==settings.customer_id">Points: {{parseInt(sale.customer.points)}}</span>
      </span>
    </div>
    <span v-if="sale.note">{{sale.note}}</span>

    </div>









    <table class="col-12" style="font-size:12px">
      <thead style="border-bottom:solid black 1px;border-top:solid black 1px;">
        <tr>
          <th style="width:50%;text-align:left;padding-left: 0;padding-right: 0;">Description</th>

          <th style="width:11%;text-align:right;padding-left: 0;padding-right: 0;">Rate</th>
          <th style="width:8%;text-align:right;padding-left: 0;padding-right: 0;">Qty</th>
          <th style="width:20%;text-align:right;padding-left: 0;padding-right: 0;">Amt</th>

        </tr>
      </thead>



      <tbody>

        <tr v-for="(item, index) in sale.items" :key="item.id">

         <td style="width:50%">{{item.food_name}}</td>

         <td  style="width:11%;text-align:right">{{item.selling_price | currency}}</td>
         <td  style="width:8%;text-align:center">{{parseInt(item.quantity)}}</td>
         <td style="width:20%;text-align:right">{{item.subtotal | currency}}</td>
       </tr>
      </tbody>
    </table>
    <hr style="border-top: dotted 2px;" />

    <div class="row" style="font-size:17px">
    <div class="col 6">
      Total Items: {{sale.items.length}} <br>
      Total Qty: {{sale.items.reduce((total, obj) => parseFloat(obj.quantity) + total,0)}}
    </div>
    <div class="col 6 " style="text-align:right;font-size:18px">
      <span v-if="sale.discount > 0">Sub</span> Total: {{sale.total | currency}}<br>
      <span v-if="sale.discount > 0">Discount: {{sale.discount | currency}}<br>
      Total: {{sale.grand_total | currency}}</span>
    </div>
    </div>
    <span v-if="!($route.params.return)">

    <div  v-if="sale.pay_statuses[0]">
      <div  v-if="sale.pay_statuses[0].payment_method == 'Cash'">
    <hr style="background-color: black;margin:0">
    <div class="row" style="font-size:15px">
    <div class="col 6">
    Cash Tendered <br>
    Cash Returned
    </div>
    <div class="col 6 " style="text-align:right;text-align:right;font-size:15px">
      {{sale.cash_tendered | currency}} <br>
      {{Math.abs(sale.cash_balance) | currency}}

    </div>
    </div>
    </div>
    </div>
  </span>
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
  <hr style="border-top: dotted 2px;" />
    <div v-if="settings.gst_number" class="row" style="font-size:14px; text-align:center">
    <div class="col 12">

      *All Rates Are Inclusive Of Taxes
    </div>
    </div>


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
      if (window.location.href.indexOf("bakery_style_1") > -1) {

}
else
{
  function beforePrint() {
    //  console.log('Before Print');
  }

  function afterPrint() {
    window.close();
  }

  if (window.matchMedia) {
      var mediaQueryList = window.matchMedia('print');
      mediaQueryList.addListener(function (mql) {
          (mql.matches) ? beforePrint() : afterPrint();
      });
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
