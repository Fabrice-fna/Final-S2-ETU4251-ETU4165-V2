<?php
$conn = mysqli_connect("localhost", "root", "", "emprunt");
if (!$conn) {
die ("Erreur :" . mysqli_connect_error());
}
