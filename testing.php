<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        select, input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }
        button {
            margin-top: 15px;
            padding: 10px;
            background-color: blue;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h2>Sign Up</h2>
    <form>
        <label for="fullname">Full Name:</label>
        <input type="text" id="fullname" required>

        <label for="region">Region:</label>
        <select id="region" required>
            <option value="">Select Region</option>
        </select>

        <label for="province">Province:</label>
        <select id="province" required>
            <option value="">Select Province</option>
        </select>

        <label for="city">City:</label>
        <select id="city" required>
            <option value="">Select City</option>
        </select>

        <label for="municipality">Municipality:</label>
        <select id="municipality" required>
            <option value="">Select Municipality</option>
        </select>

        <label for="barangay">Barangay:</label>
        <select id="barangay" required>
            <option value="">Select Barangay</option>
        </select>

        <button type="submit">Sign Up</button>
    </form>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const regionSelect = document.getElementById("region");
            const provinceSelect = document.getElementById("province");
            const citySelect = document.getElementById("city");
            const municipalitySelect = document.getElementById("municipality");
            const barangaySelect = document.getElementById("barangay");

            // Fetch Regions
            fetch("curl --location 'https://psgc.vercel.app/api/region'")
                .then(response => response.json())
                .then(data => {
                    data.forEach(region => {
                        let option = document.createElement("option");
                        option.value = region.code;
                        option.textContent = region.name;
                        regionSelect.appendChild(option);
                    });
                });

            // Fetch Provinces Based on Region Selection
            regionSelect.addEventListener("change", function () {
                provinceSelect.innerHTML = "<option value=''>Select Province</option>";
                fetch(`https://psgc.vercel.app/api/region/${this.value}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.provinces) {
                            data.provinces.forEach(province => {
                                let option = document.createElement("option");
                                option.value = province.code;
                                option.textContent = province.name;
                                provinceSelect.appendChild(option);
                            });
                        }
                    });
            });

            // Fetch Cities
            fetch("https://psgc.vercel.app/api/city")
                .then(response => response.json())
                .then(data => {
                    data.forEach(city => {
                        let option = document.createElement("option");
                        option.value = city.code;
                        option.textContent = city.name;
                        citySelect.appendChild(option);
                    });
                });

            // Fetch Municipalities Based on Province Selection
            provinceSelect.addEventListener("change", function () {
                municipalitySelect.innerHTML = "<option value=''>Select Municipality</option>";
                fetch(`https://psgc.vercel.app/api/province/${this.value}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.municipalities) {
                            data.municipalities.forEach(municipality => {
                                let option = document.createElement("option");
                                option.value = municipality.code;
                                option.textContent = municipality.name;
                                municipalitySelect.appendChild(option);
                            });
                        }
                    });
            });

            // Fetch Barangays Based on Municipality Selection
            municipalitySelect.addEventListener("change", function () {
                barangaySelect.innerHTML = "<option value=''>Select Barangay</option>";
                fetch(`https://psgc.vercel.app/api/municipality/${this.value}/barangay`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(barangay => {
                            let option = document.createElement("option");
                            option.value = barangay.code;
                            option.textContent = barangay.name;
                            barangaySelect.appendChild(option);
                        });
                    });
            });

        });
    </script>
</body>
</html>
