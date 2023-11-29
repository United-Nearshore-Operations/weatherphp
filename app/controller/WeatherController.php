<?php

class WeatherController
{
    private $apiKey;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function getWeather($city, $unit = 'metric')
    {
        $apiEndpoint = "http://api.openweathermap.org/data/2.5/weather";
        $apiUrl = "$apiEndpoint?q=$city&units=$unit&appid=$this->apiKey";

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $apiResponse = curl_exec($ch);
        curl_close($ch);

        if ($apiResponse === false) {
            return ["error" => "Error occurred while fetching data from the API."];
        }

        $weatherData = json_decode($apiResponse, true);

        if ($weatherData["cod"] != 200) {
            return ["error" => "City not found. Please enter a valid city name."];
        }

        $result = [
            "cityName" => $weatherData["name"],
            "temperature" => $weatherData["main"]["temp"],
            "weatherDescription" => $weatherData["weather"][0]["description"],
        ];

        return $result;
    }
}
