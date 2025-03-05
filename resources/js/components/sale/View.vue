<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card" v-if="is('Super Admin') || is('Admin')">
              <div class="card-header">
                <h3 class="card-title">Sale No: {{sale.id}}</h3>
              </div>
              <div class="card-body">
                <div class="row">
                <div class="col-12">
                  <table class="table">
                    <tr >
                      <td>Customer: {{sale.customer_name}}</td>
                      <td>Location: {{sale.location_name}}</td>

                    </tr>
                    <tr>
                      <td>Sale Status: {{sale.status}}</td>
                      <td>Payment Status: {{sale.payment_status}}</td>
                    </tr>
                  </table>


                  </div>

                  </div>


									<div class="card">

              <!-- /.card-header -->
              <div class="card-body table-responsive p-0" >
                <table class="table table-head-fixed text-nowrap" id="items">
                  <thead>
                    <tr>
                      <th>Details</th>
                      <th>Price</th>
                      <th>Tax</th>
                      <th>Quantity</th>
                      <th>Discount</th>
                      <th>Subtotal</th>

                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="item in sale.items" :key="item.id">
                     <td>{{item.food_name}}</td>
                     <td >{{item.price}}</td>
                     <td >{{item.gst}}</td>
                     <td >{{item.quantity}}</td>
                     <td >{{item.discount}}</td>
                     <td >{{item.subtotal}}</td>


                   </tr>

                   <tr>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td>Sub Total: </td>
                     <td>{{sale.total}}</td>
                    </tr>
                   <tr>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td>Tax Total: </td>
                     <td>{{sale.gst}}</td>
                    </tr>
                    <tr>
                   <tr>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td>Discount: </td>
                     <td>{{sale.discount}}</td>
                    </tr>
                   <tr>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td>Shipping: </td>
                     <td>{{sale.shipping}}</td>
                    </tr>
                   <tr>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td>Grand Total: </td>
                     <td>{{sale.grand_total}}</td>
                    </tr>

                  </tbody>
                </table>


              </div>
              <!-- /.card-body -->

            </div>
            Sale Status Timeline
            <div class="row">
            <div class="col-12">
              <table class="table">
                <tr v-for="item in sale.statuses" :key="item.id">
                  <td>Date & Time: {{item.created_at | date}}</td>
                  <td>Status: {{item.status}}</td>
                  <td>Updated By: {{item.user_name}}</td>
                </tr>
                <tr>
                </tr>
              </table>
              </div>
              </div>
            Sale Payment Timeline
            <div class="row">
            <div class="col-12">
              <table class="table">
                <tr v-for="item in sale.pay_statuses" :key="item.id">
                  <td>Date & Time: {{item.created_at | date}}</td>
                  <td>Status: {{item.status}}</td>
                  <td>Amount: {{item.amount}}</td>
                  <td>Account Name: {{item.account_name}}</td>
                  <td>Updated By: {{item.user_name}}</td>
                </tr>
                <tr>
                </tr>
              </table>
              </div>
              </div>
                </div>

            </div>
          </div>
        </div>
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
                selected: null
    }

          },


          methods: {

            loadSale(){
                axios.get("/api/sale/get/"+this.$route.params.id).then(({ data }) => (this.sale = data.data));
            },




          },
            created() {


                this.$Progress.start();
                //this.load();

                this.loadSale();


                this.$Progress.finish();
            },
            filters: {
                date: function (date) {

                    return   moment.utc(date).local().format('DD-MM-YYYY hh:mm A');

                },
            },

        }
    </script>
