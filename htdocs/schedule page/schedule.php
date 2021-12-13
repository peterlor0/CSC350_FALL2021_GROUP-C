<!DOCTYPE html>

<?php require $_SERVER['DOCUMENT_ROOT'] . "/shared.php" ?>

<html>

<head>
    <title>Schedule</title>
    <link rel="stylesheet" href="/shared.css">
    <link rel="stylesheet" href="schedule.css">

    <script src="./schedule.js"></script>
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

    $flag = true;

    if ($_GET['thisweek'] == "1") {
        //this week
        $date = getDateTimeRangeOfThisWeekSchedule();

        if (isUserAlreadyScheduleThisWeek($conn, $_SESSION['username'])) {
            $flag = false;
        } else {
            $fullSlotsList = generateSlotsByRange(getNextSlotDateTime(), $date['end']);
        }
    } else if ($_GET['thisweek'] == "0") {
        //next week
        if (!isAvailableForNextWeekSchedule()) {
            $flag = false;
        }

        if (isUserAlreadyScheduleNextWeek($conn, $_SESSION['username'])) {
            $flag = false;
        }

        $date = getDateTimeRangeOfNextWeekSchedule();
        $fullSlotsList = generateSlotsByRange($date['start'], $date['end']);
    } else {
        $flag = false;
    }

    if ($flag) {
        $sql = "SELECT Date FROM mgr.schedule WHERE Date >= '{$date['start']}' AND Date < '{$date['end']}'";
        $query = $conn->query($sql);

        $slotsAlreadySchedule = array();

        while ($ret = mysqli_fetch_assoc($query)) {
            array_push($slotsAlreadySchedule, $ret['Date']);
        }

        //all slots - already schedule slots = available slots
        $slotsAvailable = diffDateTimeList($fullSlotsList, $slotsAlreadySchedule);

        $day = "";
    ?>

        <div class="outerContainer">
            <div class="container">
                <h2>Select a slot</h2>
                <form action="./submit.php" method="post">
                    <div class="slotList">

                        <?php
                        foreach ($slotsAvailable as &$i) {
                            $day2 = date("w", strtotime($i));

                            //$tmp,$tmp2,$tmp3 only for display

                            if ($day2 != $day) {
                                if ($day != "") {
                                    echo "</div>
                                        </div>";
                                }

                                $tmp = date("M d, Y, l", strtotime($i));

                                echo "<div class='panel'>
                                        <div class='panelHeader' 
                                            onclick='onPanelHeaderClick(this)'>
                                        {$tmp}
                                        </div>
                                    <div class='panelContent'>";
                            }

                            $day = $day2;

                            $tmp2 = date("h:i A", strtotime($i)) . " - " . date("h:i A", strtotime($i) + 3600 * 3 - 1);
                            $tmp3 = $tmp . ", " . $tmp2;

                            echo "<p class='item'>
                                    <label>
                                    <input data-datetime='{$tmp3}' type='radio' name='slot' value='{$i}' onclick='onClick(this)'>
                                    {$tmp2}
                                </label>
                            </p>";
                        }

                        if ($day != "") {
                            echo "</div>
                                </div>";
                        }

                        ?>

                    </div>

                    <?php

                    $size = count($slotsAvailable);
                    echo "<p>{$size} slot(s) available</p>"

                    ?>

                    <p>Your Selection: <span id="selection" class="selection">None</span></p>

                    <p>
                        <a href="/main page/main.php"><button type="button">Cancel</button></a>
                        <input id="submit" type="submit" value="Submit" disabled>
                    </p>

                </form>

            </div>
        </div>

        <script src="./schedule2.js"></script>

    <?php
    } else {
        echo "error<br>";
    ?>
        <p><a href="../main page/main.php"><button type="button">Continue</button></a></p>
    <?php
    }

    $conn->close();

    ?>


</body>

</html>