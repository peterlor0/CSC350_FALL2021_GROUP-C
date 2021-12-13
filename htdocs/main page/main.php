<!DOCTYPE html>

<?php require $_SERVER['DOCUMENT_ROOT'] . "/shared.php" ?>

<html>

<head>
    <title>Home</title>
    <link rel="stylesheet" href="/shared.css">
    <link rel="stylesheet" href="./main.css">

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>

<body>
    <?php
    session_start();
    checkLoginState();

    $conn = startSQLConnect();

    $sql = "SELECT * FROM mgr.userdata WHERE Username='{$_SESSION['username']}'";
    $query = $conn->query($sql);
    $row = mysqli_fetch_assoc($query);

    echoNavBar($_SESSION['username'], $row['RoomNum']);

    //this week

    $date = getDateTimeRangeOfThisWeekSchedule();
    $sql = "SELECT Date FROM mgr.schedule WHERE Date >= '{$date['start']}' AND Date < '{$date['end']}' AND Username = '{$_SESSION['username']}'";
    $query = $conn->query($sql);

    $date1 = date("M d, Y, l", strtotime($date['start']));
    $date2 = date("M d, Y, l", strtotime($date['end']) - 1);

    echo "<div class='container'>";
    echo "<h3 class='date'>This Week ({$date1} - {$date2}):</h3>";

    if ($query && $query->num_rows > 0) {
        $ret = mysqli_fetch_assoc($query);
        $tm = strtotime($ret['Date']);

        echo "<ion-icon name='time-outline'></ion-icon>Your schedule: " . date("Y-m-d, l, h:i A", $tm) . " - " . date("h:i A", $tm + 3600 * 3 - 1) . "<br>";
    } else {

        //if time passed this last slot and user did not schedule this week, echo "n/a"
        if (time() > strtotime(getDateTimeOfLastSlotOfThisWeek())) {
            echo "<p>N/A</p>";
        } else {

    ?>
            <p>
                <a href="../schedule page/schedule.php?thisweek=1">
                    <ion-icon name="calendar-outline"></ion-icon>
                    Schedule
                </a>
            </p>

        <?php

        }
    }

    //next week
    if (isAvailableForNextWeekSchedule()) {
        $date = getDateTimeRangeOfNextWeekSchedule();
        $sql = "SELECT Date FROM mgr.schedule WHERE Date >= '{$date['start']}' AND Date < '{$date['end']}' AND Username = '{$_SESSION['username']}'";
        $query = $conn->query($sql);

        $date1 = date("M d, Y, l", strtotime($date['start']));
        $date2 = date("M d, Y, l", strtotime($date['end']) - 1);

        echo "<hr><h3 class='date'>Next Week ({$date1} - {$date2}):</h3>";

        if ($query && $query->num_rows > 0) {
            $ret = mysqli_fetch_assoc($query);
            $tm = strtotime($ret['Date']);

            echo "<ion-icon name='time-outline'></ion-icon>Your schedule: " . date("Y-m-d, l, h:i A", $tm) . " - " . date("h:i A", $tm + 3600 * 3 - 1) . "<br>";
        } else {
        ?>
            <p>
                <a href="../schedule page/schedule.php?thisweek=0">
                    <ion-icon name="calendar-outline"></ion-icon>
                    Schedule
                </a>
            </p>
    <?php
        }
    }

    echo "</div>";

    $conn->close();

    ?>
</body>

</html>