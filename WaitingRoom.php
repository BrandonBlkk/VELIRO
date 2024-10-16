<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VELIRO Men's Clothing</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css" integrity="sha512-HXXR0l2yMwHDrDyxJbrMD9eLvPe3z3qL3PPeozNTsiHJEENxx8DH2CxmV05iwG0dwoz5n4gQZQyYLUNt1Wdgfg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="output.css?v=<?php echo time(); ?>">
</head>

<body>
    <section class="waiting-section flex flex-col items-center justify-center min-h-screen p-2">
        <div class="waiting-title text-center mb-8">
            <h1 class="text-3xl font-bold">Hang Tight</h1>
            <h2 class="text-xl text-gray-600">You're now in a virtual queue</h2>
            <p class="text-gray-700">You are now placed in a virtual queue and will be redirected to our site shortly.</p>
        </div>
        <div class="waiting-img max-w-[700px] mb-8 select-none">
            <img class="max-w-full h-auto" src="Images/travelers-with-suitcases-semi-flat-color-character-editable-full-body-people-sitting-on-wooden-bench-and-waiting-on-white-simple-cartoon-spot-illustration-for-web-graphic-d.jpg" alt="Waiting Image">
        </div>
        <p id="time" class="text-lg">
            <span class="font-semibold">Your estimated waiting time is: </span>
            <span id="countdown" class="text-indigo-600">10:00</span> minutes
        </p>
        <p id="alert" class="text-red-500 font-bold mt-2">DO NOT EXIT PAGE</p>
    </section>


    <script type="text/javascript">
        let totalSeconds = 600; // 10 minutes = 600 seconds

        function updateCountdown() {
            let minutes = Math.floor(totalSeconds / 60);
            let seconds = totalSeconds % 60;

            // Format minutes and seconds with leading zeros if needed
            let formattedMinutes = minutes < 10 ? "0" + minutes : minutes;
            let formattedSeconds = seconds < 10 ? "0" + seconds : seconds;

            document.getElementById("countdown").textContent = formattedMinutes + ":" + formattedSeconds;
            totalSeconds--;

            if (totalSeconds < 0) {
                window.location.href = "SignIn.php";
            }
        }

        setInterval(updateCountdown, 1000);
    </script>
</body>

</html>