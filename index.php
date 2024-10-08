<?php
include 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My True Bank</title>
    <style>
        :root {
            --primary-color: #FFFFFF;
        }
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            /* padding: 4px; */
            padding-inline:7px;
            margin: 0;
            margin-top:6px;
        }
        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 0 10px;
        }
        video, iframe {
            width: 100%;
            max-width: 100%;
            height: 190px;
            margin-top: 40px;
        }
       
        form {
            display: flex;
            flex-direction: column;
            max-width: 100%;
            margin: 0 auto;
        }
       
        label {
            margin-bottom: 5px;
            font-weight: bold;
            text-align: left;
            display: block; /* Ensure label takes up full width */
        }
        input {
            margin-bottom: 15px; /* Space between fields */
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px; /* More rounded corners */
            width: 100%; /* Full width */
            box-sizing: border-box; /* Include padding in width calculation */
        }
        button {
            border: none;
            background-color: var(--primary-color);;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            width: 100%; /* Full width */
            box-sizing: border-box; /* Include padding in width calculation */
            transition: background-color 0.3s ease;
            cursor: pointer;
            font-size: 16px;
            font-size: 18px
        }
        button:hover {
            background-color: #0056b3;
        }
        .header-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 5px;
        }
        .header-container img {
            width: 50%; /* Responsive logo width */
            max-width: 200px; /* Limit maximum width */
            height: auto;
        }
        h2 {
            text-align: center;
            margin-top: 50px;
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        /* Responsive design adjustments */
        @media (min-width: 1024px) {
           body{
            /* width:70%; */
            display: flex;
            justify-content: center;
            padding: 20px;

           }
         

           h1{
                font-size: 20px;
            }
        }


        @media (max-width: 480px) {
            .header-container img {
                width: 90%; /* Further adjust logo size for very small screens */
            }
            h2 {
                font-size: 18px; /* Further adjust heading size for very small screens */
            }
            button {
                font-size: 16px; /* Further adjust button font size for very small screens */
            }
            h1{
                font-size: 20px;
            }
            .footer img{
              width: 100%;
              height: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-container">
            <h1 id="heading"></h1>
        </div>

        <div class="video-container">
            <div id="loader" style="display: none;">Loading...</div>
            <div id="not-found-message" style="display: none; color: red;"></div>
            <iframe id="video" width="560" height="400" src=" " frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>

        <div class="form-container" style="margin-top: 40px;">
            <h2>Customer Details</h2>
            <form id="detailsForm">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your name" required>

                <label for="number">Contact Number</label>
                <input type="tel" id="number" name="number" placeholder="Enter your contact number" required>

                <button type="submit">Proceed Now</button>
            </form>
        </div>

        <div class="footer" style="margin-top: 9px;">
            <img id="img" src=" " alt="Logo" style="height:100%; border: var(--primary-color) solid 1px; border-radius: 10px;">
        </div>
    </div>
    <script>
        // API_HOST = "http://192.168.1.17:8000"; //DEVELOPMENT
        API_HOST = "https://mwbapi.mytruebank.com"; //PRODUCTION

        let attempt = 0;
        
        async function getData(retries = 2) {
    const getUrl = API_HOST + "/video_links";
    document.getElementById("loader").style.display = "block"; // Show loader
    document.getElementById("not-found-message").style.display = "none"; // Hide not found message
    document.getElementById("detailsForm").style.display = "block"; // Show form initially
    while (attempt < retries) {
        try {
            const response = await fetch(getUrl, {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    Authorization: "Basic dXNlcm5hbWU6cGFzc3dvcmQ=",
                },
            });

            if (response.status === 401) {
                attempt++;
                console.log(`401 Unauthorized - retrying (${attempt}/${retries})`);
                if (attempt >= retries) {
                    throw new Error("Maximum retry attempts reached");
                }
                await new Promise((resolve) => setTimeout(resolve, 1000));
                continue;
            }

            const data = await response.json();
            const slug = window.location.pathname.split('/').pop();
            console.log(slug, "slug");

            const filterData = data._items.find((item) => item.name === slug);

            if (filterData) {
                document.getElementById("heading").innerHTML = filterData.heading;
                document.getElementById("video").src = filterData.video_link;
                document.querySelector(":root").style.setProperty("--primary-color", filterData.color_code);
                document.getElementById("img").src = API_HOST + "/" + filterData.image_link;
                document.getElementById("detailsForm").style.display = "block"; // Show the form if slug is found
            } else {
                document.getElementById("not-found-message").style.display = "block"; // Show not found message
                document.getElementById("detailsForm").style.display = "none"; // Hide the form if no slug is found
            }

            return filterData;
        } catch (e) {
            console.log("error ", e);
            attempt++;
            if (attempt >= retries) {
                throw new Error("Failed after multiple attempts");
            }
            await new Promise((resolve) => setTimeout(resolve, 2000));
        } finally {
            document.getElementById("loader").style.display = "none"; // Hide loader after the request completes
        }
    }
}

getData(); // Call the function to fetch data

document.addEventListener("DOMContentLoaded", async function () {
  const dataUrls = await getData();
  console.log("dataUrls", dataUrls);
  document
    .getElementById("detailsForm")
    .addEventListener("submit", function (e) {
      e.preventDefault(); // Prevent the default form submission

      // Get the input values
      const name = document.getElementById("name").value;
      const contactNumber = document.getElementById("number").value;

      // Define the API URL and request parameters
      const url = API_HOST + "/applied_leads"; // The PHP script that handles form submission
      const method = "POST";
      const headers = {
        "Content-Type": "application/x-www-form-urlencoded",
        "Authorization": "Basic dXNlcm5hbWU6cGFzc3dvcmQ=",
      };
      const body = new URLSearchParams({
        name: name,
        contact_no: contactNumber,
      });

      // Perform the AJAX request
      fetch(url, {
        method: method,
        headers: headers,
        body: body.toString(),
      })
        .then((response) => {
          if (!response.ok) {
            return response.json().then((err) => {
              throw new Error(err.error || "Something went wrong");
            });
          }
          return response.json();
        })
        .then((data) => {
          console.log("Success:", data);
          window.location.href = dataUrls.redirect_url;

        })
        .catch((err) => {
          console.error("Error:", err);
        });
    });
});

</script>
</body>
</html>
