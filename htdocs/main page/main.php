<!DOCTYPE html>

<?php require $_SERVER['DOCUMENT_ROOT'] . "/shared.php" ?>

<html>

<head>
    <title></title>
    <link rel="stylesheet" href="/shared.css">
</head>

<body>
    <?php
    session_start();
    checkLoginState();

    $conn = startSQLConnect();
    $sql = "SELECT * FROM mgr.userdata WHERE Username='{$_SESSION['username']}'";
    $ret = $conn->query($sql);
    $row = mysqli_fetch_row($ret);
    echo "<h1>Welcome back! {$_SESSION['username']}</h1>";
    echo "<p>Room Number: {$row[2]}</p>";
    echo "<a href='logout.php'>Logout</a><br>";
    $conn->close();
    ?>
    <a href="../schedule page/schedule.php">Schedule...</a>
    <?php
    ?>
</body>

</html>