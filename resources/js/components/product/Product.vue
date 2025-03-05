<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">

            <div class="card" v-if="is('Super Admin') || is('Admin')">
              <div class="card-header">
                <h3 class="card-title">Product / Ingredient  List</h3>
                <input style="margin-left:2%" type="text" v-model="searchText" name="name" placeholder="Search.." @keyup="search"  autocomplete="off">
                <button type="button" class="btn btn-sm btn-primary" @click="generateBarcode">Generate Barcodes</button>
              <!-- <b> STOCK VALUE = {{ items.data.reduce((total, obj) => parseFloat(obj.cost)*parseFloat(sum(obj.locations))) + total,0) }}</b> -->
                <div class="card-tools">

                  <button type="button" class="btn btn-sm btn-primary" @click="newModal">
                      <i class="fa fa-plus-square"></i>
                      Add New
                  </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th></th>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Code</th>
                      <th>Cost</th>
                      <th>Stock</th>
                      <th>Value</th>

                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="item in items.data" :key="item.id">
                      <td><input type="checkbox" :value="item" v-model="sel" /></td>
                      <td>{{item.id}}</td>
                      <td >{{item.name}}</td>
                      <td>{{item.code}}</td>
                      <td>{{item.cost}}</td>
                      <td>{{sum(item.locations)}}</td>
                      <td>{{parseFloat((sum(item.locations)*item.cost)).toFixed(2)}}</td>

                      <td>

                        <div class="btn-group">
                    <button style="padding:0;height:28px" type="button" class="btn btn-info">Action</button>
                    <button style="padding:0;height:28px" type="button" class="btn btn-info dropdown-toggle dropdown-icon" data-toggle="dropdown" aria-expanded="false">
                      <span class="sr-only">Toggle Dropdown</span>
                      <div class="dropdown-menu" role="menu" style="">

                        <a class="dropdown-item" href="#" @click="editModal(item)">Edit</a>
                        <a class="dropdown-item" href="#" @click="RemoveItem(item.id)">Delete</a>
                        <a class="dropdown-item" href="#" @click="Consume(item)">Consume</a>


                      </div>
                    </button>
                  </div>

                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <select  @change="load()"  v-model="numberOfItems">
                    <option value="10">10</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="All">ALL</option>
                  </select>
                <!-- <pagination :data="items" :limit="3" @pagination-change-page="page => getResults(page,searchText )"></pagination> -->
                  <pagination :data="items" :limit="3" @pagination-change-page="page => getResults(page,searchText )"></pagination>
              </div>
            </div>
            <!-- /.card -->

            <div v-if="!is('Super Admin') && !is('Admin')" >
                <not-found></not-found>
            </div>
          </div>
        </div>




        <!-- Modal -->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" v-show="!editmode">Create New </h5>
                    <h5 class="modal-title" v-show="editmode">Update </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- <form @submit.prevent="createUser"> -->

                <form @submit.prevent="editmode ? update() : create()">
                    <div class="modal-body">
                      <div class="row">
                        <div class="form-group col-md-4">
                            <label>Name</label>
                            <input v-model="form.name" type="text" name="name"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                            <has-error :form="form" field="name"></has-error>
                        </div>
                        <div v-if="!editmode" class="form-group col-md-4">
                          <br>
                            <label>Directly Sellable</label>
                            <input v-model="form.sellable" type="checkbox"
                                >

                        </div>
                        <div v-if="!editmode && form.sellable" class="form-group col-md-4">
                            <label>Code</label>
                            <input v-model="form.code" type="text" name="description"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('code') }">
                            <has-error :form="form" field="code"></has-error>
                        </div>
                        <div class="form-group col-md-6">
                            <label>GST Percentage</label>
                            <input v-model="form.gst_percentage"  type="text"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('sgst_percentage') }">
                            <has-error :form="form" field="cost"></has-error>
                        </div>
                        <div class="form-group col-md-6">
                            <label>HSN Code</label>
                            <input v-model="form.hsn"  type="text"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('cgst_percentage') }">
                            <has-error :form="form" field="cost"></has-error>
                        </div>



                        <div class="form-group col-md-6">
                            <label>Cost</label>
                            <input v-model="form.cost" required type="text"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('cost') }">
                            <has-error :form="form" field="cost"></has-error>
                        </div>


                        <div class="form-group col-md-6" v-if="form.gst_percentage !== '' && form.gst_percentage !== null">
                            <label>GST Included / Exculded</label>
                            <select required class="form-control"  v-model="form.cost_include_gst">
                              <option></option>
                              <option value="0">Excluding GST</option>
                              <option value="1">Including GST</option>
                            </select>
                        </div>


                        <div v-if="!editmode && form.sellable" class="form-group col-md-6">
                            <label>MRP / Selling Price</label>
                            <input v-model="form.price" type="text"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('price') }">
                            <has-error :form="form" field="price"></has-error>
                        </div>


                      <div class="form-group col-md-6" v-if="!editmode && form.sellable && form.gst_percentage !== '' && form.gst_percentage !== null">
                          <label>GST Included / Exculded</label>
                          <select required class="form-control"  v-model="form.price_include_gst">
                            <option></option>
                            <option value="0">Excluding GST</option>
                            <option value="1">Including GST</option>
                          </select>
                      </div>


                      <div class="form-group col-md-4" v-if="form.gst_percentage == '' || form.gst_percentage == null">
                      </div>

                        <div  v-if="!editmode && form.sellable" class="form-group col-md-6">
                            <label>Offer Selling Price</label>
                            <input v-model="form.sale_price" type="text"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('sale_price') }">
                            <has-error :form="form" field="price"></has-error>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Product Category</label>
                            <select class="form-control" v-model="form.product_category_id">
                              <option
                                  v-for="(cat,index) in categories" :key="index"
                                  :value="index"
                                  :selected="index == form.product_category_id">{{ cat }}</option>
                            </select>
                            <has-error :form="form" field="product_category_id"></has-error>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Product Brand</label>
                            <select class="form-control" v-model="form.product_brand_id">
                              <option
                                  v-for="(b,index) in brands" :key="index"
                                  :value="index"
                                  :selected="index == form.product_brand_id">{{ b }}</option>
                            </select>
                            <has-error :form="form" field="product_brand_id"></has-error>
                        </div>


                        <div  v-if="!editmode && form.sellable" class="form-group col-md-6">
                            <label>Food Category</label>
                            <select class="form-control" v-model="form.food_category_id">
                              <option
                                  v-for="(cat,index) in categories">{{ cat }}</option>
                            </select>

                        </div>
                        <div  v-if="!editmode && form.sellable" class="form-group col-md-6">
                            <label>Food Brand</label>
                            <select class="form-control" v-model="form.food_brand_id">
                              <option
                                  v-for="(b,index) in brands" :key="index"
                                  :value="index"
                                  :selected="index == form.food_brand_id">{{ b }}</option>
                            </select>
                            <has-error :form="form" field="food_brand_id"></has-error>
                        </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button v-show="editmode" type="submit" class="btn btn-success">Update</button>
                        <button v-show="!editmode" type="submit" class="btn btn-primary">Create</button>
                    </div>
                  </form>
                </div>
            </div>
        </div>

        <!--MODAL FOR CONSUMPTION-->
        <div class="modal fade" id="consume" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                <div class="modal-header">

                    <h5 class="modal-title">CONSUME</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- <form @submit.prevent="createUser"> -->

                <form @submit.prevent="createConsumption()">
                    <div class="modal-body">
                      <div class="row">
                        <div class="col-12">

                        </div>
                      </div>
                      <div class="row">
                        <input v-model="formConsume.id" type="hidden">
                        <div class="form-group col-md-4">
                            <label>Quantity</label>
                            <input v-model="formConsume.quantity" type="text" name="name"
                                class="form-control" >

                        </div>
                        <div class="form-group col-md-4">
                          <br>
                            <label>Is Wastage?</label>
                            <input v-model="formConsume.wastage" type="checkbox"
                                >
                        </div>
                        <div class="form-group col-md-6" v-if="form.gst_percentage !== '' && form.gst_percentage !== null">
                            <label>GST Included / Exculded</label>
                            <select required class="form-control"  v-model="form.cost_include_gst">
                              <option></option>
                              <option value="0">Excluding GST</option>
                              <option value="1">Including GST</option>
                            </select>
                        </div>

                          <div class="form-group col-md-6">
                            <label>Taken By</label>
                            <select class="form-control" v-model="formConsume.employee_id">
                              <option
                                  v-for="(cat,index) in employees" :key="index"
                                  :value="index"
                                  >{{ cat }}</option>
                            </select>

                        </div>
                          <div class="form-group col-md-6">
                            <label>Taken For</label>
                            <select class="form-control" v-model="formConsume.kitchen_id">
                              <option
                                  v-for="(cat,index) in kitchens" :key="index"
                                  :value="index"
                                  >{{ cat }}</option>
                            </select>

                        </div>

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button v-show="editmode" type="submit" class="btn btn-success">Update</button>
                        <button v-show="!editmode" type="submit" class="btn btn-primary">Create</button>
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

              sel:[],
              searchText:null,
              numberOfItems: 10,
              customerGroups: null,
                editmode: false,
                items : {},
                formConsume: new Form({
                  id:''
                }),
                form: new Form({
                  customer_group_discounts: [
                      {
                        group_id: '',
                        discounted_price: ''
                      }
                    ],
                  units: [
                      {
                        pivot :
                          {
                            unit_a_id: '',
                            unit_a_value: '',
                            unit_b_id: '',
                            unit_b_value: ''
                          }


                      }
                    ],
                    id : '',
                    name: '',
                    code: '',
                    hsn: '',
                    cost: '',
                    sellable: '',
                    gst_percentage: null,
                    cost_include_gst: '',
                    price_include_gst: '',
                    price: '',
                    sale_price: '',
                    sale_unit: '',
                    purchase_unit: '',

                    type: '',
                    category : '',
                    product_category_id: '',
                    product_brand_id: '',
                    food_category_id: '',
                    food_brand_id: '',

                }),
                categories: [],
                brands: [],
                units: [],
                kitchens: [],
                employees: [],
            }
        },
        methods: {
            scrollToTop() {
                window.scrollTo(0,0);
        },
          generateBarcode(){
            if(this.sel.length == 0)
            {
              alert("Select Products");
            }
            else
            {
              localStorage.products = JSON.stringify(this.sel);
              this.$router.push('/barcode/form');
            }
          },
          newDisc()
          {
            this.form.customer_group_discounts.push({ group_id: '', discounted_price: '' });
          },
          newUnit()
          {
            this.form.units.splice(0, 0, { pivot: { unit_a_id: '', unit_a_value: '' , unit_b_id: '' , unit_b_value: '' }});
          //  this.form.units.push({ pivot.unit_a_id: '', unit_a_value: '' , unit_b_id: '' , unit_b_value: '' });
          },
          search()
          {
            if(this.searchText == '')
            {
              this.load();
            }
            else
            {
              axios.get("/api/product/list/search/"+this.searchText).then(
                ({ data }) => (this.items = data.data)
              );
            }


          },
          loadCustomerGroups(){
              axios.get("/api/customer/group/list").then(({ data }) => (this.customerGroups = data.data));
          },
          sum(locations)
          {
            return locations.reduce((total, obj) => parseFloat(obj['pivot'].quantity) + total,0).toFixed(2);
          },
            getResults(page = 1,searchText=null) {
                  this.$Progress.start();
                  axios.get('/api/Product?page=' + page + '&searchText=' + searchText + '&numberOfItems='  +this.numberOfItems).then(({ data }) => (this.items = data.data));
                  this.scrollToTop();
                  this.$Progress.finish();
            },
            update(){
                this.$Progress.start();
                this.form.put('/api/Product/'+this.form.id)
                .then((response) => {
                    // success
                    $('#addNew').modal('hide');
                    Toast.fire({
                      icon: 'success',
                      title: response.data.message
                    });
                    this.$Progress.finish();
                        //  Fire.$emit('AfterCreate');

                    this.load();
                })
                .catch(() => {
                    this.$Progress.fail();
                });

            },
            editModal(item){
                //this.loadCustomerGroups();
                this.editmode = true;
                this.form.reset();
                $('#addNew').modal('show');
                this.form.fill(item);
                if(this.form.units)
                if(this.form.units.length <= 0)
                {
                  this.form.units.splice(0, 0, { pivot: { unit_a_id: '', unit_a_value: '' , unit_b_id: '' , unit_b_value: '' }});
                }
            },
            Consume(item){
                //this.loadCustomerGroups();
                this.editmode = true;
                this.formConsume.reset();
                $('#consume').modal('show');
                this.formConsume.fill(item);

            },
            newModal(){
                //this.loadCustomerGroups();
                this.editmode = false;
                this.form.reset();
                $('#addNew').modal('show');
            },

            load(){
                this.getResults();
                    // axios.get("/api/Product").then(({ data }) => (this.items = data.data));

            },
            createConsumption()
            {


                  this.formConsume.post('/api/direct/consume')
                  .then((response)=>{
                      $('#consume').modal('hide');

                      Toast.fire({
                              icon: 'success',
                              title: response.data.message
                      });

                      this.$Progress.finish();
                      this.load();
                  })
                  .catch(()=>{
                      Toast.fire({
                          icon: 'error',
                          title: 'Some error occured! Please try again'
                      });
                  })
            },
            create(){

                this.form.post('/api/Product')
                .then((response)=>{
                    $('#addNew').modal('hide');

                    Toast.fire({
                            icon: 'success',
                            title: response.data.message
                    });

                    this.$Progress.finish();
                    this.load();
                })
                .catch(()=>{
                    Toast.fire({
                        icon: 'error',
                        title: 'Some error occured! Please try again'
                    });
                })
            },
             RemoveItem(id)
        {

              Swal.fire({
                  title: 'Are you sure?',
                  text: "You won't be able to revert this!",
                  showCancelButton: true,
                  confirmButtonColor: '#d33',
                  cancelButtonColor: '#3085d6',
                  confirmButtonText: 'Yes, delete it!'
                  }).then((result) => {

                      // Send request to the server
                        if (result.value) {
                              this.form.delete('/api/Product/'+id).then(()=>{
                                      Swal.fire(
                                      'Deleted!',
                                      'Your file has been deleted.',
                                      'success'
                                      );
                                  // Fire.$emit('AfterCreate');
                                  this.load();
                              }).catch((data)=> {
                                  Swal.fire("Failed!", data.message, "warning");
                              });
                        }
                  })
        },
        loadCategories(){
            axios.get("/api/product/category/list").then(({ data }) => (this.categories = data.data));
        },
        loadBrands(){
            axios.get("/api/product/brand/list").then(({ data }) => (this.brands = data.data));
        },
        loadFoodCategories(){
            axios.get("/api/food/category/list").then(({ data }) => (this.categories = data.data));
        },
        loadFoodBrands(){
            axios.get("/api/food/brand/list").then(({ data }) => (this.brands = data.data));
        },
        loadUnits(){
            axios.get("/api/product/unit/list").then(({ data }) => (this.units = data.data));
        },
        loadKitchens(){
            axios.get("/api/kitchen/list").then(({ data }) => (this.kitchens = data.data));
        },
        loadEmployees(){
            axios.get("/api/employee/list").then(({ data }) => (this.employees = data.data));
        },

        },
        mounted() {
            window.scrollTo(0, 0)
        },
        created() {

            this.$Progress.start();
            this.load();
            this.loadKitchens();
            this.loadEmployees();
            this.loadCategories();
            this.loadBrands();
            this.loadFoodCategories();
            this.loadFoodBrands();
          //  this.loadUnits();
            this.$Progress.finish();
        },

    }
</script>
