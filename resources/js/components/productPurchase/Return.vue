<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card" v-if="is('Super Admin') || is('Admin')">
              <form @submit.prevent="create()">
              <div class="card-header">
                <h3 class="card-title">Purchase No: {{form.id}}</h3>
              </div>
              <div class="card-body">
                <div class="row">
                <div class="col-12">
                  <table class="table">
                    <tr >
                      <td>Supplier: {{form.supplier_name}}</td>
                      <td>Location: {{form.location_name}}</td>

                    </tr>
                    <tr>
                      <td>Purchase Status: {{form.status}}</td>
                      <td>Payment Status: {{form.payment_status}}</td>
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
                      <th>Cost</th>
                      <th>Discount</th>
                      <th>Tax</th>
                      <th>Quantity</th>
                      <th>Subtotal</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item,index) in form.items" :key="item.index">
                     <td>{{item.product_name}}</td>
                     <td ><input style="width:50%" readonly v-model.number="item.cost" type="text">
                    </td>
                     <td ><input style="width:50%" readonly v-model.number="item.discount"  type="text"></td>
                     <td ><input style="width:50%" readonly v-model.number="item.gst"  type="text"></td>
                     <td ><input style="width:50%" :max='item.quantity' :min='1' v-model.number="item.r_quantity"  @change="updateTotal" @keypress.prevent type="number"></td>
                    <td ><input style="width:50%" readonly  readonly v-model.number="item.subtotal=item.cost*item.r_quantity" type="text"></td>
                     <td>
                       <a href="#" @click="RemoveItem(index)">
                           <i class="fa fa-trash red"></i>
                       </a>
                     </td>
                   </tr>
                   <tr style="background-color:#cacaca">
                     <td>TOTALS: </td>
                     <td><input style="width:50%"  readonly v-model.number="form.total_rate" type="text"></td>
                     <td><input style="width:50%"  readonly v-model.number="form.total_discount" type="text"></td>

                     <td><input style="width:50%"  readonly v-model.number="form.total_taxtotal" type="text"></td>
                     <td><input style="width:50%"  readonly v-model.number="form.total_quantity" type="text"></td>

                     <td><input style="width:50%"  readonly v-model.number="form.total_subtotal" type="text"></td>
                     <td></td>
                   </tr>
                   <tr>
                     <td></td>
                     <td></td>

                     <td></td>
                     <td></td>
                     <td>Tax Total: </td>
                     <td><input style="width:50%" readonly  v-model.number="form.grand_tax_total" type="text"></td>
                    </tr>
                    <tr>
                      <td></td>
                      <td></td>

                      <td></td>
                      <td></td>
                      <td>Discount Total: </td>
                      <td><input style="width:50%" readonly v-model.number="form.grand_discount" type="text"></td>
                     </tr>
                   <tr>
                     <td></td>
                     <td></td>

                     <td></td>
                     <td></td>
                     <td>Shipping & Delivery: </td>
                     <td><input style="width:50%" readonly @change="updateTotal" v-model.number="form.shipping" type="text"></td>
                    </tr>
                   <tr>
                     <td></td>
                     <td></td>
                     <td></td>

                     <td></td>
                     <td>Grand Total: </td>
                     <td><input style="width:50%" readonly v-model.number="form.grand_total" type="text"></td>
                    </tr>
                  </tbody>
                </table>


              </div>
              <!-- /.card-body -->

            </div>

            <div class="row">
            <div class="col-12">
              <button style="width:100%" type="submit" class="btn btn-danger" :disabled="isDisabled">CREATE RETURN</button>
              </div>
              </div>


                </div>
                </form>
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
                isDisabled : false,
                purchase: [
                  {
                    id:null
                  }
                ],
                options: [],
                form: new Form({
                  amount_paid: null,
                  items: [],
                  supplier_id: null,
                  account_id: null,
                  location_id: null,
                  total_subtotal: 0,
                  total_price: 0,
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
            RemoveItem(id)
            {
              this.form.items.splice(id, 1);
              this.updateTotal();
            },
            loadPurchase(){
                axios.get("/api/purchase/get/"+this.$route.params.id).then(({ data }) => (this.form = data.data[0]));


            },

            create(){

                //this.isDisabled = true;
              this.updateTotal();

                axios.post('/api/purchase/return', this.form)
                .then((response)=>{


                    Toast.fire({
                            icon: 'success',
                            title: response.data.message
                    });

                    this.$Progress.finish();
                    this.$router.push('/purchase/returns');

                })
                .catch(()=>{
                    Toast.fire({
                        icon: 'error',
                        title: 'Some error occured! Please try again'
                    });
                })


            },

            updateTotal()
            {

              this.form.total_subtotal = this.form.items.reduce((total, obj) => parseFloat(obj.subtotal) + total,0);
              this.form.total_taxtotal = this.form.items.reduce((total, obj) => parseFloat(obj.gst) + total,0);
              this.form.total_quantity = this.form.items.reduce((total, obj) => parseFloat(obj.quantity) + total,0);
              this.form.total_rate = this.form.items.reduce((total, obj) => parseFloat(obj.cost) + total,0);

              this.form.total_discount = this.form.items.reduce((total, obj) => parseFloat(obj.discount*obj.quantity) + total,0);
              this.form.grand_discount = this.form.total_discount;
              this.form.grand_tax_total = this.form.items.reduce((total, obj) => parseFloat(obj.gst*obj.quantity) + total,0);
              this.form.grand_total = parseFloat(this.form.total_subtotal)+parseFloat(this.form.shipping);
            },




          },
            created() {


                this.$Progress.start();
                //this.load();

                this.loadPurchase();


                this.$Progress.finish();

            },
            mounted()
            {

            this.$watch('form.items', function () {

     this.updateTotal();
   }, {deep:true})
            },

        }
    </script>
