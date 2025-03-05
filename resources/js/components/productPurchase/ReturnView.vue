<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card" v-if="is('Super Admin') || is('Admin')">
              <div class="card-header">
                <h3 class="card-title">Return Purchase No: {{purchase.id}}</h3>
              </div>
              <div class="card-body">
                <div class="row">
                <div class="col-12">
                  <table class="table">
                    <tr >
                      <td>Supplier: {{purchase.supplier_name}}</td>
                      <td>Location: {{purchase.location_name}}</td>

                    </tr>
                    <tr>
                      <td>Purchase Status: {{purchase.status}}</td>
                      <td>Payment Status: {{purchase.payment_status}}</td>
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
                    <tr v-for="item in purchase.items" :key="item.id">
                     <td>{{item.product_name}}</td>
                     <td >{{item.cost}}</td>
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
                     <td>{{purchase.total}}</td>
                    </tr>
                   <tr>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td>Tax Total: </td>
                     <td>{{purchase.gst}}</td>
                    </tr>
                    <tr>
                   <tr>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td>Discount: </td>
                     <td>{{purchase.discount}}</td>
                    </tr>
                   <tr>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td>Shipping: </td>
                     <td>{{purchase.shipping}}</td>
                    </tr>
                   <tr>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td>Grand Total: </td>
                     <td>{{purchase.grand_total}}</td>
                    </tr>

                  </tbody>
                </table>


              </div>

              <!-- /.card-body -->

            </div>
            {{purchase.extras}}
            <br>
            Purchase Status Timeline
            <div class="row">
            <div class="col-12">
              <table class="table">
                <tr v-for="item in purchase[2]" :key="item.id">
                  <td>Date & Time: {{item.created_at}}</td>
                  <td>Status: {{item.status}}</td>
                  <td>Updated By: {{item.user_name}}</td>
                </tr>
                <tr>
                </tr>
              </table>
              </div>
              </div>
            Purchase Payment Timeline
            <div class="row">
            <div class="col-12">
              <table class="table">
                <tr v-for="item in purchase[3]" :key="item.id">
                  <td>Date & Time: {{item.created_at}}</td>
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

      export default {

          data () {
              return {
                purchase: [
                  {
                    id:null
                  }
                ],
                options: [],
                form: new Form({
                  items: [],
                  supplier_id: null,
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
                suppliers: [],
                locations: [],
                accounts: [],
                selected: null
    }

          },


          methods: {

            loadPurchase(){
                axios.get("/api/purchase/return/get/"+this.$route.params.id).then(({ data }) => (this.purchase = data.data));
            },




          },
            created() {


                this.$Progress.start();
                //this.load();

                this.loadPurchase();


                this.$Progress.finish();
            },

        }
    </script>
