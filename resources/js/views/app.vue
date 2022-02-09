<template>
    <div>
        <form method="get" @submit.prevent="submit">
            <div class="md:w-1/3">
                <label>Start Date:
                    <datepicker v-model="start_date" class="shadow appearance-none border border-gray-500 rounded py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" :disabledDates="disabledDates"></datepicker>
                </label>
                <label>End Date:
                    <datepicker v-model="end_date" class="shadow appearance-none border border-gray-500 rounded py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" :disabledDates="disabledDates"></datepicker>
                </label>
            </div>
            <button @click="submit" type="button" class="cursor-pointer text-xs font-semibold px-5 md:px-8 py-1 md:py-2 rounded-full text-white bg-green-700" :disabled="isbeingCreating">
                <svg class="animate-spin mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" v-if="isbeingCreating">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span v-show="!isbeingCreating">Submit</span>
          </button>
        </form>
        <div id="chart-conainer">
            <canvas id="myChart"></canvas>
        </div>

        <div style="padding: 10px;backgroundColor:ghostwhite">
            
            <h1>Fastest Asteroid</h1>
            <blockquote><strong>{{ fastestAstroid }}</strong></blockquote>

           <h1>Closest Asteroid</h1>
           <blockquote><strong>{{ closetAstroid }}</strong></blockquote>

           <h1> Asteroid Size Collection</h1>
           <li v-for="item in astriodSizeCollection">
               {{item}}
           </li>


        </div>
    </div>
</template>

<script>
import Datepicker from 'vuejs-datepicker';
import * as moment from 'moment'
import data from '../data.js'

export default {
    components: {
        Datepicker
    },
    computed:{},
     data: () => ({
        isbeingCreating: false,
        start_date: '',                 
        end_date:'', 
        dates: [],
        astroids: [],
        fastestAstroid:[],
        closetAstroid:[],
        astriodSizeCollection:[],
        disabledDates: {
          to: new Date(Date.now() - 8640000)
        },
    }),
       
    methods: {
        drawGraph(dates, astroids) {
            document.getElementById('chart-conainer').innerHTML = '<canvas id="myChart"></canvas>'
            const ctx = document.getElementById('myChart').getContext('2d');
            const myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Asteroid',
                    data: astroids,
                    backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            },

            });
        },
        submit(){
            this.isbeingCreating = true
            let vm = this
            axios.get('/nasa/api?start_date='+ moment(this.start_date).format('yyyy-MM-DD') + '&end_date='+ moment(this.end_date).format('yyyy-MM-DD') ).then(function (response) {
                vm.dates = response.data.dates
                vm.astroids = response.data.astroids
                vm.fastestAstroid = response.data.fastestAstroid
                vm.closetAstroid = response.data.closetAstroid
                vm.astriodSizeCollection = response.data.astriodSizeCollection 
                vm.drawGraph(vm.dates, vm.astroids)
                if (response) {
                     vm.isbeingCreating = false
                } else {
                    vm.isbeingCreating = false
                }
            })    
        }
   },
    mounted () {
        this.drawGraph([], [])
    }
};
  
</script>



var state = {

}

