<?php
require_once 'app/controller/WeatherController.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jsonData = file_get_contents("php://input");
    $postData = json_decode($jsonData, true); 

    $city = isset($postData["city"]) ? $postData["city"] : "";
    $unit = isset($postData["unit"]) ? $postData["unit"] : 'metric';

    $apiKey = "0877f4d04d008b320394454c77914d87";
    $weatherController = new WeatherController($apiKey);
    $weatherResult = $weatherController->getWeather($city, $unit);

    header('Content-Type: application/json');

    echo json_encode($weatherResult) ?: json_last_error_msg();
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather App - Raine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5" id="app">
    <h2 class="mb-4 text-center">Weather App</h2>

    <form @submit.prevent="getWeather" class="mb-3">
        <div class="input-group">
            <input v-model="city" type="text" class="form-control" placeholder="Enter City" required>
            <select v-model="unit" class="form-select">
                <option value="metric">Celsius</option>
                <option value="imperial">Fahrenheit</option>
            </select>
            <button type="submit" class="btn btn-primary">Get Weather</button>
        </div>
    </form>

    <div v-if="weatherResult.error" class="alert alert-danger" role="alert">
        {{ weatherResult.error }}
    </div>

    <div v-if="weatherResult.cityName" class="card">
        <div class="card-body">
            <h3 class="card-title">{{ weatherResult.cityName }}</h3>
            <p class="card-text">Temperature: {{ weatherResult.temperature }}&deg;{{ unit === 'imperial' ? 'F' : 'C' }}</p>
            <p class="card-text">Description: {{ weatherResult.weatherDescription }}</p>
        </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    new Vue({
        el: '#app',
        data: {
            city: '',
            unit: 'metric',
            weatherResult: {}
        },
        methods: {
            getWeather() {
                axios.post('index.php', { city: this.city, unit: this.unit,})
                    .then(response => {
                        this.weatherResult = response.data;
                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        },
        watch: {
            unit() {
                this.getWeather();
            }
        }
    });
</script>
</body>

</html>