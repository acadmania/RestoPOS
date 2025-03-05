<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">

            <div class="card" v-if="is('Super Admin')">
              <div class="card-header">
                <h3 class="card-title">Discount List</h3>

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
                      <th>ID</th>
                      <th>Name</th>


                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="item in items.data" :key="item.id">

                      <td>{{item.id}}</td>
                      <td >{{item.title}}</td>


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
                  <pagination :data="items" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>


        <div v-if="!is('Super Admin')">
            <not-found></not-found>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" v-show="!editmode">Create New Customer</h5>
                    <h5 class="modal-title" v-show="editmode">Update Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- <form @submit.prevent="createUser"> -->

                <form @submit.prevent="editmode ? update() : create()">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Title</label>
                            <input v-model="form.title" type="text"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                            <has-error :form="form" field="name"></has-error>
                        </div>


                        <div class="form-group">
                            <label>Type</label>
                            <select class="form-control" required v-model="form.type">
                              <option selected disabled></option>
                              <option value="Cart">Cart</option>
                            </select>

                        </div>

                        <div class="form-group">
                            <label>Value Type</label>
                            <select class="form-control" required v-model="form.value_type">
                              <option selected disabled></option>
                              <option value="By Percentage">By Percentage</option>
                              <option value="By Value">By Value</option>
                            </select>

                        </div>

                        <div class="form-group">
                            <label>Minimum</label>
                            <input v-model="form.minimum" type="text"
                                class="form-control" >
                        </div>

                        <div class="form-group">
                            <label>Maximum</label>
                            <input v-model="form.maximum" type="text"
                                class="form-control" >
                        </div>

                        <div class="form-group">
                            <label>Value</label>
                            <input v-model="form.value" type="text"
                                class="form-control" >
                        </div>

                        <div class="form-group">
                            <label>Exlcuded Products</label>
                            <vue-tags-input
                              v-model="form.product"
                              :tags="form.excluded_categories"
                              :autocomplete-items="filteredItems"
                              @tags-changed="newTags => form.excluded_categories = newTags"
                            />
                            <has-error :form="form" field="tags"></has-error>
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
import VueTagsInput from '@johmun/vue-tags-input';
    export default {
      components: {
         VueTagsInput,
       },

        data () {
            return {
                product:  '',
                items : {},
                autocompleteItems: [],
                editmode: false,

                form: new Form({
                    id : '',
                    excluded_categories:  [],
                    title : '',
                    type: '',
                    value_type: '',
                    minimum: '',
                    maximum: '',
                    maximum: '',
                    value: '',
                })
            }
        },
        methods: {

            getResults(page = 1) {

                  this.$Progress.start();

                  axios.get('/api/discount?page=' + page).then(({ data }) => (this.items = data.data));

                  this.$Progress.finish();
            },

            update(){
                this.$Progress.start();
                this.form.put('/api/discount/'+this.form.id)
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
            loadProducts(){
              axios.get("/api/discount/category/list").then(response => {

                 this.autocompleteItems = response.data.data.map(a => {

                     return { text: a.name, id: a.id};
                 });
             }).catch((error) => console.warn(error));
           },
            load(){

                    axios.get("/api/discount").then(({ data }) => (this.items = data.data));

            },

            create(){

                this.form.post('/api/discount')
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
                              this.form.delete('/api/discount/'+id).then(()=>{
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
        }

        },
        mounted() {
            window.scrollTo(0, 0)
        },
        created() {

            this.$Progress.start();
            this.load();
            this.loadProducts();
            this.$Progress.finish();
        },
        filters: {
           truncate: function (text, length, suffix) {
               return text.substring(0, length) + suffix;
           },
       },
        computed: {
          filteredItems() {
            return this.autocompleteItems.filter(i => {
              return i.text.toLowerCase().indexOf(this.product.toLowerCase()) !== -1;
            });
          },
        },

    }
</script>
