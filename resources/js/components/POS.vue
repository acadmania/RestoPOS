<template>

    <section class="content">
      <form @submit.prevent="">
      <div class="container-fluid">
          <span v-hotkey="keymap"></span>

        <aside id="billbox" class="monitor-view" style="right: 0px; position: fixed; width: 32%; overflow: hidden auto; height: 495px;" data-select2-id="billbox">
  <div class="row">
    <div class="col-lg-12">
      <div class="card card-default">

            <div class="card-body">
              <div class="row">
               <div class="col-lg-12">
                 <div class="form-group">

                    <v-select id="prodSearchID" v-hotkey="keymap" :options="options"  placeholder="search or scan" ref="searchBox"  v-model="selected" :filterable="false"  @option:selected="selectedMat"   @search="fetchOptions" />



                  </div>

               </div>
              </div>
              <div class="row">
                <div class="col-lg-12">
                  <table class="table table-hover" id="cartTable" style="background: rgb(255, 255, 255);">
                    <thead>
                      <tr>
                        <th>Item</th>
                        <th>Rate</th>
                        <th>Quantity</th>
                        <th>Total</th>

                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(i,index) in form.items">
                        <td :style= "[i.printed ? {'color' :'red'} :  {'color' :'blue'} ]"> <b> {{i.product_name}} {{i.code}} </b><span v-for="m in i.selectedModifiers"> | {{m.name}} </span> <span v-if="i.modifiers.length > 0" style="font-size:22px;cursor:pointer" @click="modifierModal(i)"><b> + </b></span></td>
                        <td> {{parseFloat(i.price) + parseFloat(i.selectedModifiers.reduce((total, obj) => parseFloat(obj.price) + total,0))}} </td>
                        <td  style="width:27%;">
                            <i class="fa fa-minus red" style="border: 1px solid black; padding: 1px;"   @click="decrementQty(i)"></i>
                            <input type="number" step="any" @keyup="qtyChange(i);" @change="qtyChange(i); updateTotal(); modifiedCheck(i)"  v-model="i.quantity"  class="qty" style="width:50%;">
                            <i class="fa fa-plus green"  style="border: 1px solid black; padding: 1px;"  @click="incrementQty(i)"></i>
                        </td>
                        <td style="text-align:right">
                            <!-- {{i.subtotal = i.quantity * (parseFloat(i.price)+ parseFloat(i.selectedModifiers.reduce((total, obj) => parseFloat(obj.price) + total,0)))}} -->
                         <input v-model="i.subtotal" @keyup="subtotalChange(i)" style="width:3rem">
                        <i class="fa fa-copy blue"   @click="copyItem(i)"></i>
                        <i class="fa fa-trash red" v-if="!(i.printed == 1)"  @click="RemoveItem(index)"></i></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

            </div>
            <!-- /.card-body -->
          </div>
    </div>
  </div>

  </aside>
  <div class="row">
  <div class="col-lg-3">
    <input style="margin-top: 1.3%;"  type="radio" name="order_type" v-on:change="clearMenu()" v-model="form.type" value="d" required > DINE-IN

            &nbsp;&nbsp;&nbsp;&nbsp;<input name="order_type" style="margin-top: 1.3%;" v-on:change="clearMenu()"   type="radio" value="t" v-model="form.type" >  TAKEWAY

              &nbsp;&nbsp;&nbsp;&nbsp;<input name="order_type" style="margin-top: 1.3%;" v-on:change="clearMenu()"  type="radio" value="s"  v-model="form.type" > DELIVERY
            <span v-if="settings.pre_order"> <input name="order_type" style="margin-top: 1.3%;"  v-on:change="clearMenu()" type="radio" value="b"   v-model="form.type" > PREBOOK
              <date-picker v-if="form.type == 'b'" v-model="form.dateTime" type="datetime" :use12h="true" :minute-step="1"></date-picker>
            </span>
            <!--<br><input name="order_type" style="margin-top: 1.3%;" type="radio" value="b"  v-model="form.type" > BOOKING
              <date-picker v-model="form.dateTime" placeholder="date & time" required type="datetime" v-if="form.type=='b'" :use12h="true"></date-picker>-->
  </div>
  <div class="col-lg-3">
    <div class="form-group" style="margin-bottom:unset">
        <v-select ref="customerBox" :options="customers" placeholder="Customer name Or Number" v-model="form.customer" :filterable="false"  @search="fetchCustomers"  @option:selected="selectedCus"/>

    </div>
  </div>
  <div class="col-lg-2"  >
    <div class="form-group">
        <!-- <v-select v-if="form.type=='d'" ref="tableSearchbox"  @search:blur="openSearchBox"  :options="tables"   placeholder="Search Table"  v-model="form.table" :filterable="false"  @search="fetchTablesfetchTables"  @option:selected="selectedTable"/> -->
        <v-select v-if="form.type=='d'" ref="tableSearchbox"  @search:blur="openSearchBox"  :options="tables"   placeholder="Search Table"  v-model="form.table" :filterable="false"    @option:selected="selectedTable"/>

    </div>
  </div>

  </div>


        <div class="row productGrid monitor-view">
      <div class="col-lg-8">
        <div class="card card-primary card-tabs">
              <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill" href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home" aria-selected="true">Categories</a>
                  </li>
                  <li>  <div v-if="form.type=='d' && settings.supplier_dine_in == 1" class="form-group" style="padding-left:400px;">

                    <select  class="form-control" v-model="form.employee_id">

                              <option value="null">Select Employee</option>
                              <option
                                  v-for="(cus,index) in employees" :key="index"
                                  :value="index">{{ cus }}</option>
                            </select>
                                          </div>
                      </li>






                </ul>

              </div>
              <div class="card-body">
                <div class="tab-content" style="white-space: nowrap;overflow-x: auto;" id="custom-tabs-one-tabContent">
                  <div class="tab-pane fade active show" id="custom-tabs-one-home" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab">


                          <div v-for="c in cats" @click="filter(c)"  class="filterBox" style="vertical-align: top;
    display: inline-block;
    text-align: center;
    width: 120px;">

                        <img height="50" :src="'/storage/'+c.image" alt="">
                         <span style="display: block;">{{c.name}}</span>



                      </div>






                  </div>


                </div>


              </div>
              <!-- /.card -->
            </div>
      </div>

    </div>

    <div class="row productGrid monitor-view">
        <div class="col-lg-8">
          <div class="card card-default">

                <div class="card-body" id="productbox" style="overflow: hidden auto; height: 305px; background: rgb(255, 255, 255);">
                  <div class="row">

                    <div @click="selectFood(f)" v-if="selectedCat === f.food_category_id || selectedCat === 'all'" v-for="f in foods" class="col-lg-2" style="text-align:center;margin-top:1%">

                         <!-- <img height="80" :src="'/storage/'+f.image"> -->
                         <span v-if="f.image && !(f.image.includes('food.png'))"><img height="80" :src="'/storage/'+f.image"></span>
                         <span v-else-if="f.image && f.image.includes('food.png')"><img :src=" f.image" height="50"/></span>
                         <span v-else><img :src="'/images/food.png'" height="50"/></span>
                        <br>{{f.name}} | <span v-if="f.sale_price">{{f.sale_price}}</span><span v-else>{{f.price}}</span>

                      </div>

                  </div>
                </div>
                <!-- /.card-body -->
              </div>
        </div>
      </div>

      <!-- <aside id="billbox" class="mobile-view" style="right: 0px; position: fixed; width: 32%; overflow: hidden auto; height: 495px;" data-select2-id="billbox"> -->
        <div class="row mobile-view" style="right: 0px; left: 0px; position: fixed; width: 100%; overflow: hidden auto; height: 495px;">
          <div class="col-sm-12">
            <div class="card card-default">

                  <div class="card-body">
                    <div class="row">
                     <div class="col-lg-12">
                       <div class="form-group">

                          <v-select id="prodSearchID" v-hotkey="keymap" :options="options"  placeholder="search or scan" ref="mobileSearchBox"  v-model="selected" :filterable="false"  @option:selected="selectedMat"   @search="fetchOptions" />



                        </div>

                     </div>
                    </div>
                    <div class="row">
                      <div class="col-lg-12">
                        <table class="table table-hover" id="cartTable" style="background: rgb(255, 255, 255);">
                          <thead>
                            <tr>
                              <th>Item</th>
                              <th>Rate</th>
                              <th>Quantity</th>
                              <th>Total</th>

                            </tr>
                          </thead>
                          <tbody>
                            <tr v-for="(i,index) in form.items">
                              <td :style= "[i.printed ? {'color' :'red'} :  {'color' :'blue'} ]"> <b> {{i.product_name}} {{i.code}} </b><span v-for="m in i.selectedModifiers"> | {{m.name}} </span> <span v-if="i.modifiers.length > 0" style="font-size:22px;cursor:pointer" @click="modifierModal(i)"><b> + </b></span></td>
                              <td> {{parseFloat(i.price) + parseFloat(i.selectedModifiers.reduce((total, obj) => parseFloat(obj.price) + total,0))}} </td>
                              <td  style="width:27%">
                                <i class="fa fa-minus red" style="border: 1px solid black; padding: 1px;"   @click="decrementQty(i)"></i>
                                <input type="number" step="any" @keyup="qtyChange(i);" @change="qtyChange(i); updateTotal(); modifiedCheck(i)"  v-model="i.quantity"  class="qty" style="width:40%;">
                                <i class="fa fa-plus green"  style="border: 1px solid black; padding: 1px;"  @click="incrementQty(i)"></i>                          </td>
                              <td style="text-align:right">
                                <input v-model="i.subtotal" @keyup="subtotalChange(i)" style="width:3rem">
                              <i class="fa fa-copy blue"   @click="copyItem(i)"></i>
                              <i class="fa fa-trash red" v-if="!(i.printed == 1)"  @click="RemoveItem(index)"></i></td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>

                  </div>
                  <!-- /.card-body -->
                </div>
          </div>
        </div>

        <!-- </aside> -->
      <footer class="main-footer" style="bottom: 0;position: fixed;left: 0;right: 0;">

            <div class="" style="width:10%;float:left;text-align:right;padding-right:2%;">




            </div>

            <div id="footerbox" style="width:65%;float:left;text-align:right;padding-right:2%;">
              <div class="row">

                  <div class="col-4">
              <div class="form-group">

                  <select required class="form-control" @change="payChange"  v-model="form.payment_method">
                    <option :value="null" selected="" disabled="">Payment Method</option>
                    <option value="Cash">Cash</option>
                    <option value="Unpaid">Unpaid</option>
                    <option value="Card">Card</option>
                    <option value="Online">Online</option>
                    <option value="GPay">GPay</option>
                    <option value="Phonepe">Phonepe</option>
                    <option value="Amazon Pay">Amazon Pay</option>
                  </select>

              </div>



              </div>
              <div class="col-8">
                <input type="text" v-if="form.payment_method!=='Unpaid'" v-model.number="form.amount_paid" ref="tendered" placeholder="Given Amount"   required="">




                <span style="font-size:16px;color:black;margin-right:2%" v-if="form.type !== 'b'">BALANCE: {{form.cash_balance = form.grand_total - form.amount_paid}}</span>
                  <button type="submit" v-if="(settings.each_kot_new_order==0 || settings.each_kot_new_order==null)" class="formButtons btn btn-success " @click="create('Completed')" name="button">COMPLETE</button>
                  <button type="submit" v-if="settings.each_kot_new_order==1 || (settings.pre_order==1 && form.type=='b')" class="formButtons btn btn-danger" @click="create('Ordered')" name="button">ORDER</button>
                  <button type="submit" v-if="form.type=='d' && (settings.each_kot_new_order==0 || settings.each_kot_new_order==null)"  class="formButtons btn btn-danger" @click="createTableOrder()" name="button">ORDER</button>

              </div>
              </div>
              <div class="mobile-view" style="width:32%;text-align:left;padding-right:2%;">

                <input type="text" v-model="form.grand_discount" @keyup="grandDiscountChange"  placeholder="Discount"  >

                <span id="grand_total" style="font-size:20px;color:red">TOTAL: {{Math.round(form.grand_total)}}</span>

                </div>
              <div class="row">

                <div class="col-12" style="text-align:left">
                  <input placeholder="Order Note" v-model="form.note">
                  <span @click="closePOS">CLOSE POS</span>
                  <span v-if="this.payments !== null" style="color:red;margin-left:5%">TOTAL PAID:  {{this.payments.reduce((total, obj) => parseFloat(obj.amount) + total,0)}}
                  <span style="color:red;margin-left:5%"> DUE: {{this.form.grand_total - this.payments.reduce((total, obj) => parseFloat(obj.amount) + total,0)}}
                  </span>
                  </span>
                </div>
              </div>

            </div>

            <div class="monitor-view" style="width:32%;float:right;text-align:right;padding-right:2%;">

            <input type="text" v-model="form.grand_discount" @keyup="grandDiscountChange"  placeholder="Discount"  >

            <span id="grand_total" style="font-size:20px;color:red">TOTAL: {{Math.round(form.grand_total)}}</span>

            </div>


          </footer>
          <div class="modal fade" id="customerAdd" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
              <div class="modal-dialog" role="document">
                  <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title">Create New Customer</h5>

                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>

                  <!-- <form @submit.prevent="createUser"> -->

                  <form @submit.prevent="createCustomer()">
                      <div class="modal-body">
                          <div class="form-group">
                              <label>Name</label>
                              <input v-model="form.name" type="text" name="name"
                                  class="form-control" >

                          </div>


                          <div class="form-group">
                              <label>Points Option</label>
                              <select class="form-control" v-model="form.no_points">
                                <option value="0">Yes</option>
                                <option value="1">No</option>
                              </select>
                              <has-error :form="form" field="customer_group_id"></has-error>
                          </div>



                          <div class="form-group">
                              <label>Phone</label>
                              <input v-model="form.phone" type="text" name="name"
                                  class="form-control" >

                          </div>

                          <div class="form-group">
                              <label>Address</label>
                              <input v-model="form.address" type="text" name="name"
                                  class="form-control" >

                          </div>



                          <div class="form-group">
                              <label>Area</label>
                              <input v-model="form.area" type="text" name="name"
                                  class="form-control" >

                          </div>
                          <div class="form-group">
                              <label>District</label>
                              <input v-model="form.district" type="text" name="name"
                                  class="form-control" >

                          </div>





                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                          <button type="submit" class="btn btn-primary">Create</button>
                      </div>
                    </form>
                  </div>
              </div>

          </div>

          <div class="modal fade" id="closeRegister" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
              <div class="modal-dialog" role="document">
                  <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title">Close Register</h5>

                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>

                  <!-- <form @submit.prevent="createUser"> -->

                  <form @submit.prevent="submitCloseRegisterModal()">
                      <div class="modal-body">
                        <table class="table table-hover" >
                          <tbody>
                            <tr v-for="(value,index) in closePOSdata">
                              <td>
                                {{index}}
                              </td>
                              <td>
                                {{value}}
                              </td>
                            </tr>
                          </tbody>
                        </table>



                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">CANCEL</button>

                          <button type="submit" class="btn btn-primary">CLOSE REGISTER</button>
                      </div>
                    </form>
                  </div>
              </div>
          </div>


          <div class="modal fade" id="ModiferModal" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
              <div class="modal-dialog" role="document">
                  <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title">Modifiers</h5>

                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>

                  <!-- <form @submit.prevent="createUser"> -->

                  <form @submit.prevent="submitModiferForm()">
                      <div class="modal-body">
                        <table class="table table-hover" >
                          <tbody>
                            <tr v-for="m in itemModifers">
                              <td>
                                <input type="checkbox" :value="m" v-model="item.selectedModifiers">
                              </td>
                              <td>
                                {{m.name}}
                              </td>
                              <td>
                                {{m.price}}
                              </td>
                            </tr>
                          </tbody>
                        </table>



                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-primary" data-dismiss="modal">DONE</button>


                      </div>
                    </form>
                  </div>
              </div>
          </div>

        </div>
      </form>
      </section>

    </template>


    <script>

        export default {

            data () {
              return {
                payments:null,
                item:null,
                selectedModifiers:[],
                itemModifers:null,
                selectedCat: 'all',
                cats:null,
                foods:null,
                settings: {
                  each_kot_new_order:null
                },
                discounts:null,
                isDisabled : false,
                lStorage: null,
                closePOSdata: null,
                flag: null,
                sale_id: null,
                options: [],
                customerGroups :null,
                form: new Form({
                  type: null,
                  dateTime :null,
                  amount_paid: null,
                  items: [],
                  table: {},
                  supplier_id: null,
                  employee_id: null,
                  discounts: [],
                  account_id: null,
                  total_subtotal: 0,
                  total_price: 0,
                  total_taxtotal: 0,
                  total_quantity: 0,
                  total_discount:0,
                  shipping: 0,
                  customer: {},
                  grand_total: 0,
                  grand_discount: 0,
                  grand_tax_total: 0,
                  payment_method:null,
                }),
                formCustomer: new Form({

                  id : '',
                  name: '',
      customer_group_id: '',
      address: '',
      area: '',
      tax_number: '',
      district: '',
      phone: '',
                }),
                customers: [{label: 'New Customer', code: 'null'}],
                tables: [],
                locations: [],
                accounts: [],
                selected: null,

    }

            },


            methods: {
                incrementQty(item){
                    item.quantity++;
                    this.qtyChange(item);
                    this.modifiedCheck(item);
                    if(this.settings.pos_post_product_add_action == "New Row")
                        {
                            // this.$refs.searchBox.searchEl.blur();
                            this.$refs.searchBox.searchEl.focus();
                            // this.$refs.mobileSearchBox.searchEl.blur();
                            this.$refs.mobileSearchBox.searchEl.focus();
                        }
                },
                decrementQty(item){
                    item.quantity--;
                    this.qtyChange(item);
                    this.modifiedCheck(item);
                    if(this.settings.pos_post_product_add_action == "New Row")
                        {
                            // this.$refs.searchBox.searchEl.blur();
                            this.$refs.searchBox.searchEl.focus();
                            // this.$refs.mobileSearchBox.searchEl.blur();
                            this.$refs.mobileSearchBox.searchEl.focus();
                        }
                },
                qtyChange(item)
                {
                    item.subtotal = item.price*item.quantity;
                },
                subtotalChange(item)
                {
                    setTimeout(() => {
                        if (item.price == 0) {
                        item.price = item.subtotal;
                    }
                    else
                    item.quantity = item.subtotal/item.price;
            }, 2000);


                },
                copyItem(item)
                {
                    this.form.items.push(JSON.parse(JSON.stringify(item)));


                },
              clearMenu(){
                  this.form.table = {};
                  this.form.items = [];

              },
              modifiedCheck(item)
              {

                if(item.printed)
                {
                  item.is_modified = 1;
                }
                else
                {
                  item.is_modified = 0;
                }
              },
              modifierModal(item)
              {
                this.itemModifers = item.modifiers;
                this.item=item;
                $('#ModiferModal').modal('show');
              },
              payChange()
              {
                if((this.form.payment_method !== 'Cash' && this.form.payment_method !== 'Unpaid') )
                this.form.amount_paid = this.form.grand_total;
                else
                this.form.amount_paid = null;
              },
              openSearchBox () {
                this.$refs.searchBox.searchEl.focus();

    },


              removeDiscounts(){
                this.form.discounts = [];
                this.discounts = [];
                this.updateTotal();
              },





              submitCloseRegisterModal(){
                  $('#closeRegister').modal('hide');
                axios.get("/api/pos/close").then();
                this.$router.push('/pos/list');
              },
              closePOS()
              {
                axios.get("/api/pos/close/request").then(({ data }) => (this.closePOSdata = data));
                $('#closeRegister').modal('show');
              },
              createCustomer(){
                this.form.post('/api/customer')
                .then((response)=>{
                    $('#customerAdd').modal('hide');

                    Toast.fire({
                            icon: 'success',
                            title: response.data.message
                    });

                    this.$Progress.finish();
                    this.form.customer = response.data.data;

                    this.form.customer.code = response.data.data.id;
                    this.$refs.searchBox.searchEl.focus();
                })
                .catch(()=>{
                    Toast.fire({
                        icon: 'error',
                        title: 'Some error occured! Please try again'
                    });
                })
              },
              grandTaxTotalChange(){
                for(var i=0; i<this.form.items.length; i++)
                {
                  this.form.items[i].tax_total = 0;
                }
                this.form.total_taxtotal = 0;
                this.form.grand_total = this.form.total_subtotal+this.form.grand_tax_total+this.form.shipping-this.form.grand_discount;
              },
              grandDiscountChange(){
                for(var i=0; i<this.form.items.length; i++)
                {
                  this.form.items[i].discount = 0;

                }
                this.form.total_discount = 0;
                if(this.form.grand_discount.toString().includes("%"))
                {
                  this.form.grand_total = this.form.items.reduce((total, obj) => parseFloat(obj.subtotal) + total,0);
                  this.form.grand_discount = (this.form.grand_total*this.form.grand_discount.split("%")[0]) /100;
                }

                this.form.grand_total = this.form.total_subtotal+this.form.grand_tax_total+this.form.shipping-this.form.grand_discount;
              },

              RemoveItem(id)
              {
                this.form.items.splice(id, 1);
                this.updateTotal();
              },
              selectedCus(selectedOption)
              {

                if(this.form.type == 'b')
                {

                    axios.get("/api/booking/order/get/"+this.form.customer.code).then(({ data }) => (
                      this.payments = data.data.pay_statuses,
                      data.data.items.forEach(
                        i=>{
                          this.form.items.push({
                          product_name: i.food_name,
                          product_id: i.food_id,
                          tax_total:i.gst,
                          price: i.price,
                          rate: i.price,
                          hsn: i.hsn,
                          code: i.code,
                          gst_percentage: i.gst_percentage,
                          discount: i.discount,
                          cost: i.cost,
                          quantity: i.quantity,
                          printed: i.printed,
                          selectedModifiers: [],
                          modifiers: [],

          })
                        }
                      )



                ))
                }

                if(selectedOption.code === "null")
                {

                  $('#customerAdd').modal('show');
                }


              },

              selectFood(food)
              {
                let itemExist = false;
                if(this.form.items)
                this.form.items.forEach(i=>{
            if(i.product_id == food.id && i.printed != 1)
            {
              i.quantity = parseInt(i.quantity)+1;
              i.subtotal = i.price * i.quantity
              itemExist = true;
            }

          });

          if(itemExist == false)
          {
            axios.get("/api/sale/product/get/"+food.id+"/"+this.form.customer.code).then(({ data }) => (
              this.pushData(data)


            ))
          }


                this.updateTotal();
                this.options = [];


                this.selected = null;
                //this.$refs.searchBox.searchEl.blur();


              },
              selectedTable()
              {
                this.form.items = [];
                axios.get("/api/table/order/get/"+this.form.table.value).then(({ data }) => (

                  data.data.forEach(
                    i=>{
                      this.form.items.push({
                      id: i.id,
                      product_name: i.food_name,
                      product_id: i.food_id,
                      tax_total:i.tax_total,
                      price: i.price,
                      selectedModifiers: [],
                      modifiers: [],
                      rate: i.rate,
                      hsn: i.hsn,
                      code: i.code,
      gst_percentage: i.gst_percentage,
      discount: i.discount,
                      cost: i.cost,
      quantity: i.quantity,
      printed: i.printed,
      is_modified: 0,

      })
                    }
                  )



            ))



              },

              openSearchBox()
              {
                this.$refs.searchBox.searchEl.focus();
              },

              pushData(data)
              {
                const calculatedPrice = (data[1].price_include_gst == 1 || data[1].price_include_gst == null)
                    ? data[0].price
                    : parseFloat(data[0].price) + (parseFloat(data[0].price) * parseFloat(data[1].gst_percentage) / 100);

                this.form.items.unshift({
                  product_name: data[1].name,
                  product_id: data[1].id,
                  tax_total: data[0].tax_total,
                  price: calculatedPrice,
                  rate: data[0].rate,
                  hsn: data[1].hsn,
                  code: data[0].code,
                  modifiers:  data[1].modifiers,
                  selectedModifiers : [],
                  price_include_gst: data[1].price_include_gst,
                  cost_include_gst: data[1].cost_include_gst,
                  gst_percentage: data[1].gst_percentage,
                  discount: data[0].discount,
                  cost: data[0].cost,
                  gstplus: data[1].gst_percentage + 100,
                  category: data[1].category,
                  quantity: 1,
                  subtotal: calculatedPrice * 1,
                  printed: 0
                })
           if(this.settings.pos_post_product_add_action == "Quantity On Last Product")
              {
                setTimeout(() => {
              document.getElementsByClassName("qty")[0].focus();
            }, 1000);
              }

              else
                this.$refs.searchBox.searchEl.focus();
              },

            selectedMat(selectedOption)
              {

                let itemExist = false;
                if(this.form.items)
                this.form.items.forEach(i=>{
            if(i.product_id == selectedOption.code && i.printed != 1)
            {
              i.quantity = parseInt(i.quantity)+1;
              i.subtotal = i.price * i.quantity;
              itemExist = true;
            }
          });

          if(itemExist == false)
          {
            axios.get("/api/sale/product/get/"+selectedOption.code+"/"+this.form.customer.code).then(({ data }) => (
              this.pushData(data)
              ))

              if(this.settings.pos_post_product_add_action == "New Row")
                {
                    this.$refs.searchBox.searchEl.blur();
                    this.$refs.searchBox.searchEl.focus();
                    this.$refs.mobileSearchBox.searchEl.blur();
                    this.$refs.mobileSearchBox.searchEl.focus();

                }

            else if(this.settings.pos_post_product_add_action == "Quantity On Last Product")
            {

                setTimeout(() => {
              document.getElementsByClassName("qty")[0].focus();
            }, 1000);
              }

              else
              this.$refs.searchBox.searchEl.focus();

        }

              //   else
        // {




        // }

                this.updateTotal();
                this.options = [];


                this.selected = null;
                //this.$refs.searchBox.searchEl.blur();
                //this.$refs.searchBox.searchEl.focus();

              },



              totalTaxUpdate()
              {

              },

              updateTotal()
              {
                this.form.items.forEach(itm=>{

                itm.subtotal = (parseFloat(itm.price)*parseFloat(itm.quantity)).toFixed(1);
                });

                this.form.total_subtotal = this.form.items.reduce((total, obj) => parseFloat(obj.subtotal) + total,0);
                this.form.total_taxtotal = this.form.items.reduce((total, obj) => parseFloat(obj.tax_total) + total,0);
                this.form.total_quantity = this.form.items.reduce((total, obj) => parseFloat(obj.quantity) + total,0);
                this.form.total_rate = this.form.items.reduce((total, obj) => parseFloat(obj.rate) + total,0);
                this.form.total_price = this.form.items.reduce((total, obj) => parseFloat(obj.price) + total,0);
                this.form.total_discount = this.form.items.reduce((total, obj) => parseFloat(obj.discount*obj.quantity) + total,0);
                //this.form.grand_discount = this.form.total_discount;
                this.form.grand_tax_total = this.form.items.reduce((total, obj) => parseFloat(obj.tax_total*obj.quantity) + total,0);
                this.form.grand_total = parseFloat(this.form.total_subtotal)+parseFloat(this.form.shipping)-parseFloat(this.form.grand_discount);
                if (this.settings.pos_auto_fill_cash_amount && this.form.payment_method !== 'Unpaid')
              this.form.amount_paid = this.form.grand_total


              },
              filter(c)
              {
                this.selectedCat = c.id;
              },
               loadLocations(){
                  axios.get("/api/location/list").then(({ data }) => (this.locations = data.data));
              },
               loadCats(){
                   axios.get("/api/food/category/all").then(({ data }) => (this.cats = data.data));
              },
              loadTables(){
                   axios.get("/api/table/all").then(({ data }) => (
                    this.tables = data.data

                    ));
              },
               loadFoods(){
                  axios.get("/api/food/all").then(({ data }) => (this.foods = data.data));
              },
              loadSuppliers(){
                  axios.get("/api/employee/list").then(({ data }) => (this.employees = data.data));
              },

              loadAccounts(){
                  axios.get("/api/account/list").then(({ data }) => {
                    this.accounts = data.data;
                    this.discounts = data.discounts;
                    this.settings = data.settings;
                    if(this.settings.customer !== null){
                      this.form.customer = data.settings.customer;
                      this.form.customer.code = data.settings.customer.id;
                      this.form.customer.label = data.settings.customer.name;
                    }
                    if(this.settings.table_id !== null){
                      this.form.table = data.settings.table_id;
                      //this.form.customer.code = data.settings.customer.id;
                    }
                    if(this.settings.pos_default_order_type !== null){
                    //here is the issue when clicking on the radio button order type which is not showing the table search
                        // this.form.type = data.settings.pos_default_order_type;
                     this.$refs.searchBox.searchEl.focus();
                  }
                  if(this.settings.pos_default_payment_method !== null){
                    this.form.payment_method = data.settings.pos_default_payment_method;

                  }
                  if(this.settings.pos_default_order_type !== null){
                    if(this.settings.pos_default_order_type == 'd'){
                       this.form.type = data.settings.pos_default_order_type;
                    }
                    if(this.settings.pos_default_order_type == 't'){
                       this.form.type = data.settings.pos_default_order_type;
                    }
                    if(this.settings.pos_default_order_type == 's'){
                       this.form.type = data.settings.pos_default_order_type;
                    }
                    if(this.settings.pos_default_order_type == 'b'){
                       this.form.type = data.settings.pos_default_order_type;
                    }
                  }

                  setTimeout(() => {
                    if(this.settings.pos_all_products !== null)
               {

                this.foods.forEach(element => {

                    this.form.items.unshift({
                  product_name:element.name,
                  product_id: element.id,
                  tax_total: 0,
                  price: element.price,
                  rate: element.price,
                  hsn: '',
                  code: element.code,
                  modifiers:  [],
                  selectedModifiers : [],
                  price_include_gst: element.price,
                  cost_include_gst: element.price,
                  gst_percentage: element.gst_percentage,
                  discount: 0,
                  cost: 0,
                  gstplus: element.gst_percentage + 100,
                  category: '',
                  quantity: 0,
                  subtotal: element.price * 0,
                  printed: 0
                })
                });

               }
                  }, 500);




                  }

                  );

              },
             load(){
               return axios.get("/api/pos/check/registers").then(({ data }) => (this.flag = data));



              },
              fetchOptions (search, loading) {
                if(search.length) {
                loading(true);
                this.searchProds(loading, search, this);

              }
              },
              searchProds: _.debounce((loading, search, vm) => {
                axios.get("/api/food/search/"+search).then(
                  ({ data }) => (vm.options = data)
                );
                  loading(false);
      }, 350),


      fetchCustomers (search, loading) {
        if(search.length) {
        loading(true);
        this.searchCus(loading, search, this);
      }
      },
      searchCus: _.debounce((loading, search, vm) => {
        axios.get("/api/customer/search/"+search).then(
          ({ data }) => (vm.customers = data.concat([{label: 'New Customer', code: 'null'}]))
        );
        loading(false);
  }, 350),


//   fetchTables (search, loading) {
//     if(search.length) {
//     loading(true);
//     this.searchTable(loading, search, this);
//   }
//   },
//   searchTable: _.debounce((loading, search, vm) => {
//     axios.get("/api/table/search/"+search).then(
//       ({ data }) => (vm.tables = data)
//     );
//     loading(false);
//   }, 350),

              create(status){

                if(this.form.items.length === 0)
                {
                  alert("Add At least one item");
                return;
                }

                if(this.form.type == null)
                return;
                if(status == "Completed" && this.form.payment_method == null)
                return;
                if(status == "Completed" && this.form.payment_method == "Cash" && this.form.amount_paid == null)
                return;
                if(this.form.type == "d" && Object.keys(this.form.table).length === 0)
                {
                  alert("Select Table For DINE-IN");
                return;
                }



                this.form.orderStatus = status;
                this.isDisabled = true;
                this.updateTotal();

                  this.form.post('/api/sale')
                  .then((response)=>{


                      Toast.fire({
                              icon: 'success',
                              title: response.data.message
                      });

                      this.$Progress.finish();

                  if (this.settings.print_format == "super_market") {
                      //this.$router.push(`/sale/print_super_market/${response.data.data.id}`);
                      setInterval(window.open(`/sale/restaurant_style_1/${response.data.data.id}`), 4000);

                  }
                  else if (this.settings.print_format == "restaurant_style_2") {
                      //this.$router.push(`/sale/print_super_market/${response.data.data.id}`);
                      setInterval(window.open(`/sale/restaurant_style_2/${response.data.data.id}`), 4000);

                  }
                  else if (this.settings.print_format == "restaurant_style_3") {
                      //this.$router.push(`/sale/print_super_market/${response.data.data.id}`);
                      setInterval(window.open(`/sale/restaurant_style_3/${response.data.data.id}`), 4000);

                  }
                  else if (this.settings.print_format == "restaurant_style_4") {
                      //this.$router.push(`/sale/print_super_market/${response.data.data.id}`);
                      setInterval(window.open(`/sale/restaurant_style_4/${response.data.data.id}`), 4000);

                  }else if (this.settings.print_format == "restaurant_style_5") {
                      //this.$router.push(`/sale/print_super_market/${response.data.data.id}`);
                      setInterval(window.open(`/sale/restaurant_style_5/${response.data.data.id}`), 4000);

                  }
                  else if (this.settings.print_format == "bakery_style_1") {
                      //this.$router.push(`/sale/print_super_market/${response.data.data.id}`);
                      setInterval(window.open(`/sale/bakery_style_1/${response.data.data.id}`), 4000);

                  }
                        //this.$router.go();

                        //
                       // if((this.settings.each_kot_new_order == 1 || this.settings.all_orders_kot == 1)  && (this.settings.kot_printer != 'null' || this.settings.kot_printer != null))
                        if((this.settings.all_orders_kot == 1)  && (this.settings.kot_printer != 'null' || this.settings.kot_printer != null))
                        axios.get(`/api/kot/direct/print/${response.data.data.id}/complete`).then();

                        //else if((this.settings.each_kot_new_order == 1 || this.settings.all_orders_kot == 1)  && (this.settings.kot_printer != 'null' || this.settings.kot_printer != null))
                        // else
                        // window.open(`/kot/order/${response.data.data.id}`);

                          //  this.$router.go();
                          Object.assign(this.$data, this.$options.data.apply(this))
                  })
                  .catch(()=>{
                      Toast.fire({
                          icon: 'error',
                          title: 'Some error occured! Please try again'
                      });
                  })


              },
              createTableOrder(){
                if(this.form.items.length === 0)
                {
                  alert("Add At least one item");
                return;
                }

                if(this.form.type == null)
                return;


                if(this.form.type == "d" && Object.keys(this.form.table).length === 0)
                {
                  alert("Select Table For DINE-IN");
                return;
                }


                this.isDisabled = true;
                this.updateTotal();

                  this.form.post('/api/table/order')
                  .then((response)=>{


                      Toast.fire({
                              icon: 'success',
                              title: response.data.message
                      });

                      this.$Progress.finish();




                    if((this.settings.each_kot_new_order == 1 || this.settings.all_orders_kot == 1)  && (this.settings.kot_printer != 'null' || this.settings.kot_printer != null))
                        axios.get(`/api/kot/direct/print/${this.form.table.value}/order`).then();
                        else
                        window.open(`/table/order/${this.form.table.value}`);
                        // this.$router.go();
                        Object.assign(this.$data, this.$options.data.apply(this))
                  })
                  .catch(()=>{
                      Toast.fire({
                          icon: 'error',
                          title: 'Some error occured! Please try again'
                      });
                  })


              },


              selectDinein()
              {
                this.form.type = 'd';
                this.$forceUpdate();

                this.$refs.tableSearchbox.searchEl.focus();
              },
              selectTakeaway()
              {
                this.form.type = 't';
                this.$forceUpdate();
              },
              selectDelivery()
              {
                this.form.type = 's';
                this.$forceUpdate();
              },selectPreBook()
              {

                this.form.type = 'b';
                this.$forceUpdate();
              },

              selectCash()
              {
                this.form.payment_method = "Cash"
                this.$refs.tendered.focus();
              },
              selectCard()
              {
                this.form.payment_method = "Card"
                this.$refs.tendered.focus();
              },
              selectOnline()
              {
                this.form.payment_method = "Online"
                this.$refs.tendered.focus();
              },
              selectGpay()
              {
                this.form.payment_method = "GPay"
                this.$refs.tendered.focus();
              },
              selectPhonepe()
              {
                this.form.payment_method = "Phonepe"
                this.$refs.tendered.focus();
              },
              selectAmazonpay()
              {
                this.form.payment_method = "Amazon Pay"
                this.$refs.tendered.focus();
              },
              selectUnpaid()
              {
                this.form.payment_method = "Unpaid"
                this.$refs.tendered.focus();
              },

              onKeydown(event )
            {


                if(event.keyCode == 112)
                {
                    event.preventDefault();
                    this.selectDinein();
                }


                else if(event.keyCode == 113)
                {
                    event.preventDefault();
                this.selectTakeaway();
            }
                else if(event.keyCode == 114)
                {
                    event.preventDefault();
                this.selectDelivery();
            }
                else if(event.keyCode == 122)
                {
                    event.preventDefault();
                this.createTableOrder();
            }
                else if(event.keyCode == 123)
                {
                    event.preventDefault();
                this.create();
            }
                else if(event.keyCode == 13 && event.target.classList.contains('qty'))
                {
                    event.preventDefault();
                this.$refs.searchBox.searchEl.focus();
                }
                else if(event.keyCode == 117)
                {
                    event.preventDefault();
                    this.$refs.tableSearchbox.searchEl.focus();
                }
                else if (event.keyCode == 13 && event.target.classList.contains('vs__search'))
                {
                    //13 = enter key

              document.getElementsByClassName("qty")[0].focus();

                }

                else if (event.keyCode == 40 && event.target.classList.contains('qty'))
                {
                    //40 = down arrow
                    event.preventDefault();
                    event.target.parentElement.parentElement.nextElementSibling.children[2].children[0].focus();

                }
                else if (event.keyCode == 38 && event.target.classList.contains('qty'))
                {
                    //38 = up arrow
                    event.preventDefault();
                    event.target.parentElement.parentElement.previousElementSibling.children[2].children[0].focus();

                }
                else if (event.keyCode == 119 && event.target.classList.contains('qty'))
                {
                    //119 = f8
                    event.preventDefault();
                    event.target.value = parseInt(event.target.value) + 1;

                }
                else if (event.keyCode == 118 && event.target.classList.contains('qty'))
                {
                    //118 = f7
                    event.preventDefault();
                    event.target.value = parseInt(event.target.value) - 1;

                }
                else if (event.keyCode == 46 && event.target.classList.contains('qty'))
                {
                    //46 = delete
                    event.preventDefault();
                    event.target.parentElement.nextElementSibling.children[0].click()
                    this.$refs.searchBox.searchEl.focus();

                }
            }

            },

            computed: {
      keymap () {
        return {
          // 'esc+ctrl' is OK.
          'f1': this.selectDinein,
          'f2': this.selectTakeaway,
          'f3': this.selectDelivery,
          'f4': this.selectPreBook,
          'f11': this.createTableOrder,
          'f12': this.create,
          'ctrl+c': this.selectCash,
          'ctrl+d': this.selectCard,
          'ctrl+o': this.selectOnline,
          'ctrl+g': this.selectGpay,
          'ctrl+p': this.selectPhonepe,
          'ctrl+a': this.selectAmazonpay,
          'ctrl+u': this.selectUnpaid,

        }
      },



    },

            mounted()
            {

                document.addEventListener( "keydown", this.onKeydown );

            this.$watch('form.items', function () {
     this.updateTotal();
   }, {deep:true})
            },
            watch:{

              options : function(){

                if(this.options.length == 1)
                {

                  this.selectedMat(this.options[0])
                }
              },
              flag : function(){

                if(!this.flag)
                this.$router.push('/pos/list');
              }
            },
               created() {


                  this.$Progress.start();
                  this.loadFoods();
                  this.load();

                   this.loadAccounts();
                   this.loadLocations();

                   this.loadCats();
                   this.loadSuppliers();
                   this.loadTables();


                 this.$Progress.finish();


              },

          }

          if (window.location.href.indexOf("pos") > -1) {

            document.getElementsByClassName('main-header')[0].style.display = "none";
          }
          else{

            document.getElementById('app').append = '<nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom" style="display: flex;"><ul class="navbar-nav"><li class="nav-item"><a data-widget="pushmenu" href="#" class="nav-link"><i class="fa fa-bars"></i></a></li></ul> <a href="/pos" class="btn btn-sm btn-primary">OPEN POS</a></nav>"';
          }
          const xhttp = new XMLHttpRequest();
          xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
         // Typical action to be performed when the document is ready:
         var arr = JSON.parse(xhttp.responseText);
         if(arr)
         if(arr.each_kot_new_order != 1)
         {
           const kfcs = document.querySelectorAll('.kfc');
           kfcs.forEach(box => {
   box.style.display = 'none';

   });
         }
      }};
    xhttp.open("GET", "/api/settings");
    xhttp.send();

      </script>
  <style>

  .content-header{
    padding:unset;
  }


  .monitor-view {
    display: none;
}


  @media (min-width: 768px) {
    .mobile-view {
      display: none !important;
    }

    .monitor-view {
        display: block !important;
    }

  }
  @media print {
    #closeRegister {
        display: block !important;
        position: static !important;
        background: transparent !important;
    }

  }





  </style>
