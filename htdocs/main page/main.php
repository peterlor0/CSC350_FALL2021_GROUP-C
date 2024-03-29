<!DOCTYPE html>

<?php require "../shared/shared.php" ?>

<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="../shared/shared.css">

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>

<body>
    <?php
    session_start();
    //var_dump($_SESSION);

    //if there's no uuid, redirect to index.php
    if (!isset($_GET['uuid'])) {
        redirectPageTo("../index.php");
    }

    $userdata = sessionGetDataByUUID($_GET['uuid']);
    if ($userdata == null) {
        redirectPageTo("../index.php");
    }

    $conn = startSQLConnect();

    echoNavBar($userdata);

    //this week

    $date = getDateTimeRangeOfThisWeekSchedule();
    $sql = "SELECT Date FROM mgr.schedule WHERE Date >= '{$date['start']}' AND Date < '{$date['end']}' AND Username = '{$userdata['username']}'";
    $query = $conn->query($sql);

    $date1 = date("M d, Y, l", strtotime($date['start']));
    $date2 = date("M d, Y, l", strtotime($date['end']) - 1);

    echo "<div class='container'>";
    echo "<h2>Your Schedule</h2><hr>";
    echo "<h3 class='date'>This Week ({$date1} - {$date2}):</h3>";

    if ($query && $query->num_rows > 0) {
        $ret = mysqli_fetch_assoc($query);
        $tm = strtotime($ret['Date']);

        echo "<p><ion-icon name='time-outline'></ion-icon>" . date("Y-m-d, l, h:i A", $tm) . " - " . date("h:i A", $tm + 3600 * 3 - 1) . "</p>";
    } else {

        //if time passed the last slot and user did not schedule this week, echo "n/a"
        if (time() > strtotime(getDateTimeOfLastSlotOfThisWeek())) {
            echo "<p>N/A</p>";
        } else {

    ?>
            <p>
                <?php
                echo "<a href='../schedule page/schedule.php?thisweek=1&uuid={$userdata['uuid']}'>";
                ?>
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
        $sql = "SELECT Date FROM mgr.schedule WHERE Date >= '{$date['start']}' AND Date < '{$date['end']}' AND Username = '{$userdata['username']}'";
        $query = $conn->query($sql);

        $date1 = date("M d, Y, l", strtotime($date['start']));
        $date2 = date("M d, Y, l", strtotime($date['end']) - 1);

        echo "<hr><h3 class='date'>Next Week ({$date1} - {$date2}):</h3>";

        if ($query && $query->num_rows > 0) {
            $ret = mysqli_fetch_assoc($query);
            $tm = strtotime($ret['Date']);

            echo "<p><ion-icon name='time-outline'></ion-icon>" . date("Y-m-d, l, h:i A", $tm) . " - " . date("h:i A", $tm + 3600 * 3 - 1) . "</p>";
        } else {
        ?>
            <p>
                <?php
                echo "<a href='../schedule page/schedule.php?thisweek=0&uuid={$userdata['uuid']}'>";
                ?>
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