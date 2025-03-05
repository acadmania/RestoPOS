<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">

            <div class="card" v-if="is('Super Admin') || is('Admin')">
              <div class="card-header">
                <h3 class="card-title">Food  List</h3>
                <input style="margin-left:2%" type="text" v-model="searchText" name="name" placeholder="Search.." @keyup="searchList"  autocomplete="off">


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
                      <!-- <th></th> -->
                      <!-- <th>ID</th> -->
                      <th>Name</th>
                      <th>Code</th>
                      <th>Image</th>
                      <th>Price</th>


                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="item in items.data" :key="item.id">
                      <!-- <td><input type="checkbox" :value="item" v-model="sel" /></td>
                      <td>{{item.id}}</td> -->
                      <td >{{item.name}}</td>
                      <td>{{item.code}}</td>
                      <td>
                        <span v-if="item.image && !(item.image.includes('food.png'))"><img :src="'/storage/'+item.image" height="50"/></span>
                      <span v-else-if="item.image && item.image.includes('food.png')"><img :src=" item.image" height="50"/></span>
                    <span v-else><img :src="'/images/food.png'" height="50"/></span>
                    </td>
                      <td v-if="item.sale_price">{{item.sale_price}}</td>
                      <td v-if="item.sale_price == null">{{item.price}}</td>


                      <td>

                        <a href="#" @click="editModal(item)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                         /
                        <a href="#" @click="RemoveItem(item.id)">
                            <i class="fa fa-trash red"></i>
                        </a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
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
                            <label>Name*</label>
                            <input v-model="form.name" type="text" name="name"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                            <has-error :form="form" field="name"></has-error>
                        </div>

                        <div  class="form-group col-md-4">
                            <label>Code</label>
                            <input v-model="form.code" type="text" name="description"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('code') }">
                            <has-error :form="form" field="code"></has-error>
                        </div>
                        <div class="form-group col-md-4">
                            <label>HSN Code</label>
                            <input v-model="form.hsn"  type="text"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('cgst_percentage') }">
                            <has-error :form="form" field="cost"></has-error>
                        </div>

                        <div class="form-group col-md-6">
                            <label>GST Percentage</label>
                            <input v-model="form.gst_percentage"  type="text"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('sgst_percentage') }">
                            <has-error :form="form" field="cost"></has-error>
                        </div>

                        <div class="form-group col-md-6" v-if="form.gst_percentage !== '' && form.gst_percentage !== null">

                        </div>


                        <div class="form-group col-md-6">
                            <label>Cost</label>
                            <input v-model="form.cost"  type="text" class="form-control">

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
                            <label>MRP / Normal Selling Price*</label>
                            <input v-model="form.price" type="text"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('price') }">
                        </div>


                      <div class="form-group col-md-6" v-if="form.gst_percentage !== '' && form.gst_percentage !== null">
                          <label>GST Included / Exculded</label>
                          <select required class="form-control"  v-model="form.price_include_gst">
                            <option></option>
                            <option value="0">Excluding GST</option>
                            <option value="1">Including GST</option>
                          </select>
                      </div>




                        <div class="form-group col-md-6">
                            <label>Offer Selling Price</label>
                            <input v-model="form.sale_price" type="text"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('sale_price') }">
                            <has-error :form="form" field="price"></has-error>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Category</label>
                            <select class="form-control" v-model="form.food_category_id">
                              <option
                                  v-for="(cat,index) in categories" :key="index"
                                  :value="index"
                                  :selected="index == form.food_category_id">{{ cat }}</option>
                            </select>
                            <has-error :form="form" field="food_category_id"></has-error>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Kitchen</label>
                            <select class="form-control" v-model="form.kitchen_id">
                              <option
                                  v-for="(cat,index) in kitchens" :key="index"
                                  :value="index"
                                  :selected="index == form.kitchen_id">{{ cat }}</option>
                            </select>

                        </div>
                        <div class="form-group col-md-6">
                            <label>Brand</label>
                            <select class="form-control" v-model="form.food_brand_id">
                              <option
                                  v-for="(b,index) in brands" :key="index"
                                  :value="index"
                                  :selected="index == form.food_brand_id">{{ b }}</option>
                            </select>
                            <has-error :form="form" field="food_brand_id"></has-error>
                        </div>
                        <div class="form-group">

                          <div class="input-group">
                          <div class="custom-file">

                            <input type="file" class="custom-file-input" v-on:change="handleFileUpload()" ref="file" id="exampleInputFile">
                            <label class="custom-file-label" for="exampleInputFile">Choose Image</label>
                          </div>

                        </div>
                        </div>

                        <div class=" col-md-12">
                          <div class="form-group">

                            <v-select :options="options"  placeholder="Search Ingredients" v-model="selected" :filterable="false"  @option:selected="selectedMat" @search="fetchOptions" />

                          </select>
          								</div>
                          <table class="table">
                            <tbody>
                              <tr v-for="(itm,index) in form.products">
                                <td>
                                  {{itm.pivot.product_name}}
                                </td>
                                <td>
                                  <input v-model="itm.pivot.quantity">
                                </td>
                                <td>
                                    {{parseFloat(itm.pivot.quantity) * parseFloat(itm.cost)}}
                                </td>
                                <td>
                                    <a href="#" @click="RemoveIngredients(index)">
                                        <i class="fa fa-trash red"></i>
                                    </a>
                                </td>
                              </tr>
                              <tr>
                            <td colspan="2"></td>
                            <td>Total Cost: {{ totalCost() }} </td>
                            <td></td>
                            </tr>

                            </tbody>
                          </table>
                        </div>

                        <div class=" col-md-12">
                          <div class="form-group">

                            <v-select :options="modifiers"  placeholder="Search Mofifiers" v-model="selectedModifier" :filterable="false"  @option:selected="selectedMod" @search="fetchModifiers" />

                          </select>
          								</div>
                          <table class="table">
                            <tbody>
                              <tr v-for="(itm,index) in form.modifiers">
                                <td>
                                  {{itm.name}}
                                </td>
                                <td>
                                  {{itm.price}}
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                        </div>
                        <!--
                        <div class=" col-md-12">
                          <h4>Customer Group Discount</h4>
                        </div>
                        <div v-for="(grp, index) in form.customer_group_discounts" class="row">
                        <div class="form-group col-md-4">
                            <label>Customer Group</label>
                            <select class="form-control" v-model="grp.group_id">
                              <option
                                  v-for="(cat,index) in customerGroups" :key="index"
                                  :value="index"
                                  :selected="index == grp.group_id">{{ cat }}</option>
                            </select>
                            <has-error :form="form" field="customer_group_id"></has-error>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Discounted Price</label>
                            <input v-model="grp.discounted_price" type="text"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('sale_price') }">
                            <has-error :form="form" field="price"></has-error>
                        </div>
                        <div class="form-group col-md-4">
                            <br>
                          <button type="button" class="btn btn-sm btn-primary" @click="newDisc">
                              <i class="fa fa-plus-square"></i>
                              Add
                          </button>
                        </div>
                      </div>
                    -->



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
              selected: null,
              selectedModifier: null,
              sel:[],
              options: [],
              modifiers: [],
              searchText:null,
              customerGroups: null,
                editmode: false,
                items : {},
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
                    modifiers:[],
                  products: [
                      {
                        pivot :
                          {
                            product_name:'',
                            product_id: '',
                            quantity: '',
                            cost: '',

                          }


                      }
                    ],
                    id : '',
                    name: '',
                    code: '',
                    hsn: '',
                    cost: '',
                    gst_percentage: null,
                    cost_include_gst: '',
                    price_include_gst: '',
                    price: '',
                    sale_price: '',
                    sale_unit: '',
                    purchase_unit: '',
                    kitchen_id: '',

                    type: '',
                    category : '',
                    food_category_id: '',
                    food_brand_id: '',

                }),
                categories: [],
                kitchens: [],
                brands: [],
                units: [],
            }
        },
        methods: {
        scrollToTop() {
                window.scrollTo(0,0);
        },
          selectedMat(selectedOption)
          {
            let itemExist = false;
  if(this.form.products)
            this.form.products.forEach(i=>{
        if(i.product_id == selectedOption.code)
        {

          itemExist = true;
        }
      });
      if(itemExist == false)
      {

        var pivot = {};
        axios.get("/api/food/ingredient/get/"+selectedOption.code).then(({ data }) => (

            pivot.product_name = data.data.name,
            pivot.quantity =  0,
            pivot.product_id = data.data.id,

  this.form.products.push({
        cost:data.data.cost,
        pivot
          })


        ));
}


      },
          selectedMod(selectedOption)
          {
            let itemExist = false;
  if(this.form.modifiers)
            this.form.modifiers.forEach(i=>{
        if(i.id == selectedOption.code)
        {
          itemExist = true;
        }
      });
      if(itemExist == false)
      {


        axios.get("/api/modifier/get/"+selectedOption.code).then(({ data }) => (
  this.form.modifiers.push({
        id:data.data.id,
        name: data.data.name,
        price: data.data.price,

          })


        ));
}


      },
          fetchOptions (search, loading) {

            if(search.length) {
            loading(true);
            this.search(loading, search, this);
          }
          },
          search: _.debounce((loading, search, vm) => {
            axios.get("/api/purchase/product/search/"+search).then(
              ({ data }) => (vm.options = data)
            );
            loading(false);
  }, 350),
          handleFileUpload(){
  this.file = this.$refs.file.files[0];

},
          fetchModifiers (search, loading) {

            if(search.length) {
            loading(true);
            this.search_mod(loading, search, this);
          }
          },
          search_mod: _.debounce((loading, search, vm) => {
            axios.get("/api/modifiers/search/"+search).then(
              ({ data }) => (vm.modifiers = data)
            );
            loading(false);
  }, 350),


          handleFileUpload(){
  this.file = this.$refs.file.files[0];

},
          totalCost()
          {
            // return this.form.products.reduce((total, cost) => parseFloat(total + cost.pivot.quantity * cost.pivot.cost,0));
            return this.form.products.reduce((total, item) => {
              return total + item.pivot.quantity * item.cost;
            }, 0)
          },

          newDisc()
          {
            this.form.customer_group_discounts.push({ group_id: '', discounted_price: '' });
          },

          searchList()
          {
            if(this.searchText == '')
            {
              this.load();
            }
            else
            {
              axios.get("/api/food/list/search/"+this.searchText).then(
                ({ data }) => (this.items = data.data)
              );
            }


          },

          sum(locations)
          {
            return locations.reduce((total, obj) => parseFloat(obj['pivot'].quantity) + total,0);
          },
            getResults(page = 1,searchText = null) {
                  this.$Progress.start();
                  axios.get('/api/Food?page=' + page + '&searchText=' + searchText).then(({ data }) => (this.items = data.data));
                  this.scrollToTop();
                  this.$Progress.finish();
            },
            update(){

                this.$Progress.start();
                let formData = new FormData();
                formData.append('file', this.file);
                if(this.form.code)
                formData.append('code', this.form.code);
                if(this.form.hsn)
                formData.append('hsn', this.form.hsn);
                if(this.form.products)
                formData.append('products', JSON.stringify(this.form.products));
                if(this.form.modifiers)
                formData.append('modifiers', JSON.stringify(this.form.modifiers));
                if(this.form.gst_percentage)
                formData.append('gst_percentage', this.form.gst_percentage);
                if(this.form.cost)
                formData.append('cost', this.form.cost);
                if(this.form.cost_include_gst)
                formData.append('cost_include_gst', this.form.cost_include_gst);
                if(this.form.price)
                formData.append('price', this.form.price);
                if(this.form.price_include_gst)
                formData.append('price_include_gst', this.form.price_include_gst);
                if(this.form.sale_price)
                formData.append('sale_price', this.form.sale_price);
                if(this.form.food_category_id)
                formData.append('food_category_id', this.form.food_category_id);
                if(this.form.food_brand_id)
                formData.append('food_brand_id', this.form.food_brand_id);
                if(this.form.kitchen_id)
                formData.append('kitchen_id', this.form.kitchen_id);

                formData.append('name', this.form.name);
                formData.append('_method', 'PUT');




                axios.post('/api/Food/'+this.form.id, formData)
                .then((response)=>{

                    // success
                    $('#addNew').modal('hide');
                    Toast.fire({
                      icon: 'success',
                      title: response.data.message
                    });
                    this.$Progress.finish();
                        //  Fire.$emit('AfterCreate');
                      this.getResults(this.items.current_page,null);
                    //this.load();
                })
                .catch(() => {
                    this.$Progress.fail();
                });

            },
            editModal(item){

                this.editmode = true;
                this.form.reset();
                $('#addNew').modal('show');
                this.form.fill(item);


            },

            newModal(){

                this.editmode = false;
                this.form.reset();
                $('#addNew').modal('show');
            },

            load(){
                    this.getResults();


            },

            create(){
                let formData = new FormData();
                formData.append('file', this.file);
                if(this.form.code)
                formData.append('code', this.form.code);
                if(this.form.hsn)
                formData.append('hsn', this.form.hsn);
                if(this.form.products)
                formData.append('products', JSON.stringify(this.form.products));
                if(this.form.modifiers)
                {

                  formData.append('modifiers', JSON.stringify(this.form.modifiers));
                }

                if(this.form.gst_percentage)
                formData.append('gst_percentage', this.form.gst_percentage);
                if(this.form.cost)
                formData.append('cost', this.form.cost);
                if(this.form.cost_include_gst)
                formData.append('cost_include_gst', this.form.cost_include_gst);
                if(this.form.price)
                formData.append('price', this.form.price);
                if(this.form.price_include_gst)
                formData.append('price_include_gst', this.form.price_include_gst);
                if(this.form.sale_price)
                formData.append('sale_price', this.form.sale_price);
                if(this.form.food_category_id)
                formData.append('food_category_id', this.form.food_category_id);
                if(this.form.kitchen_id)
                formData.append('kitchen_id', this.form.kitchen_id);
                if(this.form.food_brand_id)
                formData.append('food_brand_id', this.form.food_brand_id);

                formData.append('name', this.form.name);


                axios.post('/api/Food', formData)
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
                              this.form.delete('/api/Food/'+id).then(()=>{
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
        RemoveIngredients(id){
          this.form.products.splice(id, 1);


        },
        loadCategories(){
            axios.get("/api/food/category/list").then(({ data }) => (this.categories = data.data));
        },
        loadKitchens(){
            axios.get("/api/kitchen/list").then(({ data }) => (this.kitchens = data.data));
        },
        loadBrands(){
            axios.get("/api/food/brand/list").then(({ data }) => (this.brands = data.data));
        },


        },
        mounted() {
            window.scrollTo(0, 0)
        },
        created() {

            this.$Progress.start();
            this.load();
            this.loadCategories();
            this.loadKitchens();
            this.loadBrands();

            this.$Progress.finish();
        },

    }
</script>
