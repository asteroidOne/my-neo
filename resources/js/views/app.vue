<template>
	<div>
        <form method="get" @submit.prevent="submit">
            <label>Start Date:
                <datepicker v-model="start_date"></datepicker>
            </label>
            <label>End Date:
                <datepicker v-model="end_date"></datepicker>
            </label>
            <button>Submit</button>
        </form>
        <div>
            <canvas id="myChart"></canvas>
        </div>

        <div style="padding: 10px;backgroundColor:ghostwhite">
            
            <h1>Fastest Asteroid</h1>
            <blockquote><strong>{{ fastestAstroid[0] }}</strong></blockquote>

           <h1>Closest Asteroid</h1>
           <blockquote><strong>{{ closetAstroid[0] }}</strong></blockquote>

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
        start_date: '',                 
        end_date:'', 
        dates: [],
        astroids: [],
        fastestAstroid:[],
        closetAstroid:[],
        astriodSizeCollection:[],
    }),
     
    methods: {
        submit(){
            window.location.reload();
            let vm = this
            vm.dates = Object.keys(data.near_earth_objects)
            for (var o in data.near_earth_objects) {
                vm.astroids.push(data.near_earth_objects[o].length)
            }
            axios.get('http://api.nasa.gov/neo/rest/v1/feed?start_date='+moment(vm.start_date).format('yyyy-MM-DD')+'&end_date='+moment(vm.end_date).format('yyyy-MM-DD')+'&detailed=true&api_key=DEMO_KEY').then(function (response) {
                vm.dates = Object.keys(response.near_earth_objects)
                var fastestAstroidSpeed = 0
                var closetAstroidDistance = 0
                for (var o in response.near_earth_objects) {
                    response.near_earth_objects[o].forEach(function(element) {
                        // console.log(element)
                        // GET FASTEST ASTERIOD
                        var currentAstroidSpeed = element.close_approach_data[0].relative_velocity.kilometers_per_hour
                        if (currentAstroidSpeed > fastestAstroidSpeed) {
                            fastestAstroidSpeed = currentAstroidSpeed;
                            vm.fastestAstroid = []
                            vm.fastestAstroid.push({
                                AsteroidName: element.name, 
                                ID: element.id, 
                                Speed: currentAstroidSpeed
                            })
                        }

                        // CLOSEST ASTERIOD
                        var currentAstroidDistance = element.close_approach_data[0].miss_distance.kilometers
                        if (closetAstroidDistance == 0 || currentAstroidDistance < closetAstroidDistance) {
                            closetAstroidDistance = currentAstroidDistance
                            vm.closetAstroid = []
                            vm.closetAstroid.push({
                                AsteroidName: element.name, 
                                ID: element.id, 
                                Distance: currentAstroidDistance
                            })
                        }

                        // ASTERIOD SIZE COLLECTION
                        var currentAstroiAvgSize = (element.estimated_diameter.kilometers.estimated_diameter_max + element.estimated_diameter.kilometers.estimated_diameter_min)/2

                        vm.astriodSizeCollection.push({
                            AsteroidName: element.name, 
                            ID: element.id, 
                            AvgSizeInKm: currentAstroiAvgSize
                        })
                    })
                    vm.astroids.push(data.near_earth_objects[o].length)
                }
            })    
        }
   },
    mounted () {
    let vm = this
    this.dates = Object.keys(data.near_earth_objects)
    var fastestAstroidSpeed = 0
    var closetAstroidDistance = 0
    for (var o in data.near_earth_objects) {
        data.near_earth_objects[o].forEach(function(element) {
            // console.log(element)
            // GET FASTEST ASTERIOD
            var currentAstroidSpeed = element.close_approach_data[0].relative_velocity.kilometers_per_hour
            if (currentAstroidSpeed > fastestAstroidSpeed) {
                fastestAstroidSpeed = currentAstroidSpeed;
                vm.fastestAstroid = []
                vm.fastestAstroid.push({
                    AsteroidName: element.name, 
                    ID: element.id, 
                    Speed: currentAstroidSpeed
                })
            }

            // CLOSEST ASTERIOD
            var currentAstroidDistance = element.close_approach_data[0].miss_distance.kilometers
            if (closetAstroidDistance == 0 || currentAstroidDistance < closetAstroidDistance) {
                closetAstroidDistance = currentAstroidDistance
                vm.closetAstroid = []
                vm.closetAstroid.push({
                    AsteroidName: element.name, 
                    ID: element.id, 
                    Distance: currentAstroidDistance
                })
            }

            // ASTERIOD SIZE COLLECTION
            var currentAstroiAvgSize = (element.estimated_diameter.kilometers.estimated_diameter_max + element.estimated_diameter.kilometers.estimated_diameter_min)/2
            vm.astriodSizeCollection.push({
                AsteroidName: element.name, 
                ID: element.id, 
                AvgSizeInKm: currentAstroiAvgSize
            })
        })
        this.astroids.push(data.near_earth_objects[o].length)
    }
        const ctx = document.getElementById('myChart').getContext('2d');
        const myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: this.dates,
            datasets: [{
                label: 'Asteroid',
                data: this.astroids,
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
    }
};
  
</script>

