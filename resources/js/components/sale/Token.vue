<template>

  <section class="content">
    <div class="container">
    <div class="row">
    <div style="font-size:50px;text-align:center;font-family: fantasy;color:white" class="col-12">
      AFC THIRUVARUR
    </div>
    </div>
    </div>
    <div class="container-fluid">
        <div class="row">
        <div  style="text-align:center;font-weight:bolder;border-right-style:solid;" class="col-6">
          <h1 style="color:white"><b>PREPARING</b></h1>
          <span v-for="t in tokens">
            <div style="background-color:yellow;color:black;font-size:50px;font-weight:bolder;padding-left:50px;padding-right:50px;margin:50px;display:inline" v-if="t.status == 'Ordered'">
              {{t.id}}
            </div>
          </span>
        </div>
        <div style="text-align:center" class="col-6">
          <h1 style="color:white"><b>NOW SERVING</b></h1>
          <span v-for="t in tokens">
            <div style="background-color:yellow;color:black;font-size:50px;font-weight:bolder;padding-left:50px;padding-right:50px;margin:50px;display:inline" v-if="t.status == 'Ready'">
              {{t.id}}
            </div>
          </span>
        </div>
        </div>
      </div>
      
    </section>

  </template>


  <script>

      export default {

          data () {
            return {
              tokens:null

  }

          },


          methods: {



            load(){
                axios.get("/api/sale/token/get").then(({ data }) => (this.tokens = data.data));
            },


          },

          computed: {


  },

          mounted()
          {
            if (window.location.href.indexOf("token") > -1) {

              document.getElementsByClassName('main-header')[0].style.display = "none";
              document.getElementsByClassName('main-sidebar')[0].style.display = "none";
              //document.getElementsByClassName('content-wrapper')[0].style.backgroundColor = "red !important";
              //document.getElementsByClassName('content-wrapper')[0].style.backgroundColor = "red !important";
              document.getElementsByClassName('content-wrapper')[0].style.setProperty("background-color","red","important");
              document.getElementsByClassName('wrapper')[0].style.setProperty("background-color","red","important");

            }



          },

            created() {


              this.load();
              this.timer = setInterval(this.load, 4000);



            },

        }


    </script>
<style>

.content-header{
  padding:unset;
}
</style>
