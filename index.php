<?php
$apiKey = "ebb0092f78c63a016d0211bf8256d1e0";
$baseUrl = "https://api.openweathermap.org/data/2.5/weather";

$majorCities = ["Jakarta", "Surabaya", "Bandung", "Medan", "Yogyakarta", "Bali", "Makassar", "Semarang", "Palembang", "Balikpapan"];
$weatherDataList = [];

foreach ($majorCities as $city) {
    $url = "$baseUrl?q=" . urlencode($city) . "&appid=$apiKey&units=metric";
    $response = file_get_contents($url);
    if ($response !== FALSE) {
        $data = json_decode($response, true);
        if (isset($data['cod']) && $data['cod'] == 200) {
            $weatherDataList[] = [
                'name' => $data['name'],
                'temp' => $data['main']['temp'],
                'description' => $data['weather'][0]['description'],
                'icon' => $data['weather'][0]['icon'],
                'humidity' => $data['main']['humidity'],
                'wind_speed' => $data['wind']['speed']
            ];
        }
    }
}

$searchWeather = null;
if (isset($_GET['search'])) {
    $searchCity = htmlspecialchars($_GET['search']);
    $searchUrl = "$baseUrl?q=" . urlencode($searchCity) . "&appid=$apiKey&units=metric";
    $searchResponse = file_get_contents($searchUrl);
    if ($searchResponse !== FALSE) {
        $searchData = json_decode($searchResponse, true);
        if (isset($searchData['cod']) && $searchData['cod'] == 200) {
            $searchWeather = [
                'name' => $searchData['name'],
                'temp' => $searchData['main']['temp'],
                'description' => $searchData['weather'][0]['description'],
                'icon' => $searchData['weather'][0]['icon'],
                'humidity' => $searchData['main']['humidity'],
                'wind_speed' => $searchData['wind']['speed']
            ];
        }
    }
}

if (isset($_GET['lat']) && isset($_GET['lon'])) {
    $lat = htmlspecialchars($_GET['lat']);
    $lon = htmlspecialchars($_GET['lon']);
    $locationUrl = "$baseUrl?lat=$lat&lon=$lon&appid=$apiKey&units=metric";
    $locationResponse = file_get_contents($locationUrl);
    if ($locationResponse !== FALSE) {
        $locationData = json_decode($locationResponse, true);
        if (isset($locationData['cod']) && $locationData['cod'] == 200) {
            $searchWeather = [
                'name' => $locationData['name'],
                'temp' => $locationData['main']['temp'],
                'description' => $locationData['weather'][0]['description'],
                'icon' => $locationData['weather'][0]['icon'],
                'humidity' => $locationData['main']['humidity'],
                'wind_speed' => $locationData['wind']['speed']
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Dashboard - Major Cities</title>
    <style>
    body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    background: linear-gradient(to bottom, #00aaff, #ffffff);
    color: #333;
}
header {
    background: #007ACC;
    color: #fff;
    padding: 20px;
    text-align: center;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}
header h1 {
    margin: 0;
    font-size: 2.5em;
}
.container {
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
}
.search-bar {
    text-align: center;
    margin-bottom: 30px;
}
.search-bar input {
    padding: 15px;
    width: 60%;
    max-width: 400px;
    border: 2px solid #007ACC;
    border-radius: 8px;
    outline: none;
    font-size: 1em;
    transition: all 0.3s;
}
.search-bar input:focus {
    border-color: #005c99;
    box-shadow: 0 0 8px rgba(0, 92, 153, 0.5);
}
.search-bar button {
    padding: 15px 25px;
    background: #007ACC;
    color: #fff;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1em;
    transition: background 0.3s;
}
.search-bar button:hover {
    background: #005c99;
}
.city-weather {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
}
.city {
    flex: 1 1 calc(30% - 20px);
    background: #f9f9fb;
    padding: 20px;
    border: 1px solid #eaeaea;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}
.city:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
}
.city img {
    max-width: 80px;
    margin-bottom: 15px;
}
.city h2 {
    margin: 10px 0;
    font-size: 1.5em;
    color: #007ACC;
}
.city p {
    margin: 5px 0;
    font-size: 1em;
    color: #555;
}
.city p strong {
    color: #333;
}
@media (max-width: 768px) {
    .city {
        flex: 1 1 calc(45% - 20px);
    }
}
@media (max-width: 480px) {
    .city {
        flex: 1 1 calc(100% - 20px);
    }
}</style>
</head>
<body>
    <header>
        <h1>Weather Dashboard - Major Cities</h1>
    </header>
    <div class="container">
        <div class="search-bar">
            <form method="GET">
                <input type="text" name="search" placeholder="Search for a city...">
                <button type="submit">Search</button>
            </form>
            <button  id="getBtnCuaca" onclick="getLocation()">Use My Location</button>
        </div>

        <?php if ($searchWeather): ?>
            <div class="city">
                <h2><?= htmlspecialchars($searchWeather['name']) ?></h2>
                <img src="https://openweathermap.org/img/wn/<?= htmlspecialchars($searchWeather['icon']) ?>@2x.png" alt="Weather icon">
                <p><strong>Temperature:</strong> <?= htmlspecialchars($searchWeather['temp']) ?>&deg;C</p>
                <p><strong>Description:</strong> <?= htmlspecialchars($searchWeather['description']) ?></p>
                <p><strong>Humidity:</strong> <?= htmlspecialchars($searchWeather['humidity']) ?>%</p>
                <p><strong>Wind Speed:</strong> <?= htmlspecialchars($searchWeather['wind_speed']) ?> m/s</p>
            </div>
        <?php endif; ?>

        <div class="city-weather">
            <?php foreach ($weatherDataList as $cityWeather): ?>
                <div class="city">
                    <h2><?= htmlspecialchars($cityWeather['name']) ?></h2>
                    <img src="https://openweathermap.org/img/wn/<?= htmlspecialchars($cityWeather['icon']) ?>@2x.png" alt="Weather icon">
                    <p><strong>Temperature:</strong> <?= htmlspecialchars($cityWeather['temp']) ?>&deg;C</p>
                    <p><strong>Description:</strong> <?= htmlspecialchars($cityWeather['description']) ?></p>
                    <p><strong>Humidity:</strong> <?= htmlspecialchars($cityWeather['humidity']) ?>%</p>
                    <p><strong>Wind Speed:</strong> <?= htmlspecialchars($cityWeather['wind_speed']) ?> m/s</p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <script>
        
    //   $("#getBtnCuaca").click();
      function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    window.location.href = `?lat=${lat}&lon=${lon}`;
                }, function(error) {
                    alert('Unable to retrieve location. Please allow location access.');
                });
            } else {
                alert('Geolocation is not supported by your browser.');
            }
        }

        if (Notification.permission === 'default') {
            Notification.requestPermission();
        }

        <?php if ($searchWeather): ?>
        const notification = new Notification("Weather Update", {
            body: "<?= htmlspecialchars($searchWeather['name']) ?>: <?= htmlspecialchars($searchWeather['temp']) ?>°C, <?= htmlspecialchars($searchWeather['description']) ?>.",
            icon: "https://openweathermap.org/img/wn/<?= htmlspecialchars($searchWeather['icon']) ?>@2x.png"
        });
        <?php endif; ?>;
        

    </script>
</body>
</html>
