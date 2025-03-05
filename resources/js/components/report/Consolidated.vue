<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">

            <div class="row" v-if="is('Super Admin') || is('Admin')">
                <form @submit.prevent="generate()">
            <date-picker v-model="form.date" type="date" range></date-picker>
            <div class="form-group">
                <label>Group By</label>
                <select required class="form-control"  v-model="form.group">
                  <option value="Daily">Daily</option>
                  <option value="Monthly">Monthly</option>
                  <option value="Yearly">Yearly</option>
                </select>

            </div>
            <button type="submit" class="btn btn-success">Show</button>
            <button @click="downloadExcel" class="btn btn-success">Download</button>
          </form>
            </div>
            <!-- /.card -->
          </div>
        </div>

        <div class="row">

          <table id="table1" class="table table-hover">
            <thead>
              <tr>
                <th></th>
                <th style="padding:0">Sales Count</th>
                <th style="padding:0">Sale Amount</th>
                <th style="padding:0">Sale Discount</th>

                <!--<th>Sale Cost</th>-->
                <th style="padding:0">Purchase Count</th>
                <th style="padding:0">Purchase Amount</th>

                <th style="padding:0">Salaries</th>
                <th style="padding:0">Salary Advances</th>
                <th style="padding:0">Incentives</th>
                <th style="padding:0">Expenses</th>
                <th style="padding:0">Benefit</th>
              </tr>
            </thead>
            <tbody>
               <tr v-for="item in results.periods">

                <td style="padding:0">{{item}}</td>
                <td  style="padding:0">{{results.salesCount[item]}}</td>
                <td style="padding:0" >{{results.salesAmount[item]}}</td>
                <td  style="padding:0">{{results.salesDiscount[item]}}</td>


                  <!--<td >{{results.salesCost[item]}}</td>-->
                <td  style="padding:0">{{results.purchaseCount[item]}}</td>
                <td  style="padding:0">{{parseFloat(results.purchaseAmount[item]).toFixed(2)}}</td>
                <td style="padding:0" >{{results.salariesArray[item]}}</td>
                <td  style="padding:0">{{results.SalaryAdvance[item]}}</td>
                <td  style="padding:0">{{results.Incentive[item]}}</td>
                <td  style="padding:0">{{results.expensesArray[item]}}</td>
                <td style="padding:0" >{{
                    parseFloat(
                parseFloat(results.salesAmount[item])
                - parseFloat(results.purchaseAmount[item])
                - parseFloat(results.salariesArray[item])
                - parseFloat(results.SalaryAdvance[item])
                - parseFloat(results.expensesArray[item])
                ).toFixed(2)
                }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-if="!is('Super Admin') && !is('Admin')">
            <not-found></not-found>
        </div>

        <!-- Modal -->

    </div>
  </section>
</template>

<script>
    export default {
        data () {
            return {

                editmode: false,
                results: {},
                date : null,
                form: new Form({
                    id : '',
                    name: '',
                    code: '',
                    balance: '',

                })
            }
        },
        methods: {
          downloadExcel()
          {
           TableToExcel.convert(document.getElementById("table1"));
          },


            generate(){
              this.$Progress.start();
                this.form.post('/api/report/consolidated')
                .then((response)=>{


                    Toast.fire({
                            icon: 'success',
                            title: response.data.message
                    });
                    this.results = response.data.data;
                    this.$Progress.finish();

                })
                .catch(()=>{
                    Toast.fire({
                        icon: 'error',
                        title: 'Some error occured! Please try again'
                    });
                })
            },


        },
        mounted() {
            window.scrollTo(0, 0)
        },
        created() {

            this.$Progress.start();

            this.$Progress.finish();
        },

    }
</script>
