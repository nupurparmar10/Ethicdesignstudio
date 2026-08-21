<?php
include_once("connect.php");

if (isset($_POST['country'])) {
    $country_id = intval($_POST['country']);
    echo "<option value=''>--Select--</option>";
    $result = mysqli_query($con, "SELECT id, name FROM states WHERE country_id=$country_id ORDER BY name");
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<option value='{$row['id']}'>{$row['name']}</option>";
    }
}

if (isset($_POST['state'])) {
    $state_id = intval($_POST['state']);
    echo "<option value=''>--Select--</option>";
    $result = mysqli_query($con, "SELECT id, name FROM cities WHERE state_id=$state_id ORDER BY name");
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<option value='{$row['id']}'>{$row['name']}</option>";
    }
}

if (isset($_POST['state_id'])) {
    $state_id = intval($_POST['state_id']);

    $city_query = mysqli_query($con, "SELECT id, name FROM cities WHERE state_id = '$state_id' ORDER BY name");

    echo '<option value="">--Select City--</option>';
    while ($row = mysqli_fetch_assoc($city_query)) {
        echo "<option value='{$row['id']}-{$row['name']}'>{$row['name']}</option>";
    }
}


